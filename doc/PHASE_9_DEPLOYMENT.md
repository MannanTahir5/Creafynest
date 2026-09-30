# Phase 9 — Production deployment (Azee)

This runbook assumes a typical Linux host (VPS or PaaS) with **PHP 8.2+**, **MySQL 8+**, **Nginx or Apache**, and **Composer**. Node.js is only required **on the machine that builds assets** (often the same server, or your CI runner).

---

## 1. Production environment variables

Copy `.env.example` to `.env` on the server and set at least:

| Variable | Production value |
|----------|------------------|
| `APP_NAME` | Your site name (e.g. `Azee`) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | **HTTPS** canonical URL, e.g. `https://example.com` (no trailing slash) |
| `APP_KEY` | Run `php artisan key:generate` once per environment |
| `LOG_LEVEL` | `error` or `warning` (avoid `debug` in production) |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` | Your MySQL host and port |
| `DB_DATABASE` | **`azee`** (per project convention) |
| `DB_USERNAME` / `DB_PASSWORD` | Strong credentials |
| `SESSION_DRIVER` | `database` (already common) or `redis` if you add Redis |
| `CACHE_STORE` | `database` or `redis` (avoid `file` on multi-node setups) |
| `FILESYSTEM_DISK` | `local` (uploads stay on `public` disk under `storage/app/public`) |

Generate the key:

```bash
php artisan key:generate
```

---

## 2. Provision MySQL database `azee`

On the MySQL server:

```sql
CREATE DATABASE azee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'azee_app'@'%' IDENTIFIED BY 'use_a_strong_password';
GRANT ALL PRIVILEGES ON azee.* TO 'azee_app'@'%';
FLUSH PRIVILEGES;
```

Point `.env` at this database and user, then:

```bash
php artisan migrate --force
```

Use `--force` so migrations run non-interactively when `APP_ENV=production`.

---

## 3. Install PHP dependencies (production)

```bash
composer install --no-dev --optimize-autoloader
```

Do **not** install dev dependencies on the production server unless you need them for debugging.

---

## 4. Build frontend assets

From the project root (requires Node 18+ recommended):

```bash
npm ci
npm run build
```

This runs **Vite** and writes hashed assets under `public/build/`. Committing `public/build` is optional; many teams build on the server or in CI and deploy the artifact.

---

## 5. Storage link and permissions

Public uploads are served via `storage/app/public` and the **`public/storage`** symlink:

```bash
php artisan storage:link
```

Ensure the web server user can write to:

- `storage/`
- `bootstrap/cache/`

Example (adjust user/group to your stack, e.g. `www-data`):

```bash
chown -R www-data:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

---

## 6. Enable Laravel caches

After `.env` is correct and config is stable:

```bash
composer production:optimize
```

This runs `php artisan optimize` (config, events, routes, views as applicable). See also `composer production:cache-views` and `composer production:clear-caches` in `composer.json`.

**When you change `.env` or config files**, clear and rebuild:

```bash
composer production:clear-caches
composer production:optimize
```

---

## 7. Web server and PHP

- Document root should be **`public/`** (not project root).
- Point PHP-FPM (or equivalent) at the same path.
- Recommended: **HTTPS only**, redirect HTTP → HTTPS at the edge (Nginx/Cloudflare).

### HTTPS and reverse proxies (Cloudflare / load balancer)

If TLS terminates in front of Laravel, set `APP_URL` to `https://...` and set **`TRUSTED_PROXIES`** in `.env`:

- `TRUSTED_PROXIES=*` — trust all proxies (typical behind Cloudflare when you control the chain).
- Or a comma-separated list of IP addresses.

`AppServiceProvider::boot()` reads `config('app.trusted_proxies')` (from `TRUSTED_PROXIES` in `.env`) and calls `TrustProxies::at(...)` when it is non-empty. Leave `TRUSTED_PROXIES` unset on local dev unless you use a TLS-terminating proxy locally.

See also [Trusted proxies](https://laravel.com/docs/11.x/requests#configuring-trusted-proxies) and **Phase 7** caching notes: `doc/PHASE_7_PERFORMANCE.md`.

---

## 8. Queue and scheduler (optional)

`.env.example` may use `QUEUE_CONNECTION=database`. For light traffic, `sync` is acceptable. If you use the database queue:

```bash
php artisan queue:work --tries=1
```

Run this under **supervisor** or **systemd**, not in an SSH session.

For scheduled tasks (if you add any later):

```cron
* * * * * cd /path/to/azee && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Post-deploy verification checklist

After deploy, verify in a browser (or with `curl`):

| Check | URL / action |
|-------|----------------|
| Health | `GET /up` → **200** |
| Home | `/` |
| Public pages | `/about`, `/portfolio`, `/services`, `/blog`, `/contact` |
| Contact POST | Submit form → success message, row in `contacts` admin |
| Admin login | `/login` → dashboard `/admin` |
| Uploads | Create/edit project or blog with image → image loads on public URL under `/storage/...` |
| SEO | `/sitemap.xml`, `/robots.txt` |

---

## 10. Cloudflare (optional)

- DNS **proxied** (orange cloud) for CDN/DDoS protection.
- SSL mode **Full (strict)** when origin has a valid certificate.
- Cache rules: cache static `/build/*` aggressively; do not cache authenticated HTML for `/admin`. Details overlap with **`doc/PHASE_7_PERFORMANCE.md`**.

---

## 11. Minimal “first deploy” command sequence

```bash
git pull
composer deploy:install-php-deps
npm ci && npm run build
composer deploy:migrate
php artisan storage:link
composer production:optimize
# reload php-fpm / nginx as needed
```

(`deploy:install-php-deps` and `deploy:migrate` are Composer shortcuts defined in `composer.json`.)

---

## 12. Rollback

- **Code**: redeploy previous release (git tag / branch).
- **Migrations**: restore DB backup; forward-fix migrations only with care.
- **Caches**: `composer production:clear-caches` then fix config and run `composer production:optimize` again.
