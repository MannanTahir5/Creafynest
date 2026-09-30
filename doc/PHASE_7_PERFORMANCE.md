# Phase 7 — Performance and hardening (reference)

This document captures what the codebase does for Phase 7 and how to run it in production.

## Application caching

- **Home** (`/`): featured projects, services preview, and testimonials are cached together for one hour under `ContentCache::HOME`.
- **Blog index** (`/blog`): the category list is cached under `ContentCache::BLOG_CATEGORIES` (posts and search results are not cached).
- **Portfolio index** (`/portfolio`): the distinct project category filter list is cached under `ContentCache::PORTFOLIO_CATEGORIES` (paginated projects are not cached).
- **Sitemap** (`/sitemap.xml`): rendered XML body is cached under `ContentCache::SITEMAP_XML` for one hour.

Caches are cleared automatically when admin creates, updates, or deletes **projects**, **blogs**, **categories**, **services**, or **testimonials**. `DatabaseSeeder` calls `ContentCache::forget()` at the start of a seed run.

To clear manually: `php artisan cache:clear` (or `composer production:clear-caches` below).

## WebP derivatives

On upload, the admin flow generates a **sibling `.webp`** next to JPEG/PNG originals on the `public` disk (via GD when `imagewebp` is available). The public **Portfolio** and **Blog** detail pages use `<picture>` when a WebP file exists (`ContentImage.vue`). If GD/WebP is missing, originals are used only.

## Images in the browser

- Non-hero images default to **`loading="lazy"`** and **`decoding="async"`** through `ContentImage`.
- The main project cover uses **`fetchpriority="high"`** and eager loading to support LCP.

## Laravel production optimization

Composer scripts (run on the server after `composer install --no-dev` and env configuration):

| Command | Purpose |
|--------|---------|
| `composer production:optimize` | Runs `php artisan optimize` (config, events, routes, views where applicable). |
| `composer production:cache-views` | Runs `php artisan view:cache` if you want to pre-compile Blade only. |
| `composer production:clear-caches` | Runs `php artisan optimize:clear` (useful after deploy or config changes). |

Typical deploy sequence (adjust for your host):

1. Pull code, install Composer dependencies, build front-end assets (`npm ci && npm run build`).
2. Run migrations.
3. Run `composer production:optimize`.
4. Ensure `php artisan storage:link` and correct filesystem permissions.

Local development: run `composer production:clear-caches` when config or routes change and behavior looks stale.

## Query behavior

- Public **blog** listing already uses `with('category')` on paginated posts.
- **Blog detail** loads `category` and runs a bounded related-posts query.
- **Portfolio** and **home** list queries remain small and paginated or limited as before.

## CDN (e.g. Cloudflare) — suggested rules

- **Cache aggressively**: static build assets under `/build/*` (long TTL, immutable if filenames are hashed by Vite).
- **Do not cache HTML** for personalized or authenticated responses if you use session cookies broadly; for mostly anonymous marketing pages you may cache HTML with short TTL and respect `Cache-Control` from Laravel if you add it later.
- **Bypass cache** for `/admin`, `/login`, and authenticated POST/PUT/DELETE.
- **Images**: cache `/storage/*` with a long TTL; purge on deploy if you replace files in place without changing URLs.
- **Sitemap**: short TTL or purge when content changes; the app also caches sitemap XML for one hour server-side.

These are operational guidelines; tune TTLs and page rules to your traffic and cookie behavior.
