# Phase 0 — Prerequisites + Decisions (Locked)

This phase removes ambiguity so implementation (Phase 1+) can proceed cleanly. Target database name: **`azee`**.

---

## Environment prerequisites (Windows)

- [ ] Install:
  - [ ] PHP (Laravel 11 compatible)
  - [ ] Composer
  - [ ] Node.js + npm
  - [ ] MySQL Server
- [ ] Confirm you can run:
  - [ ] `php -v`
  - [ ] `composer -V`
  - [ ] `node -v` and `npm -v`
  - [ ] MySQL client access (Workbench or CLI)

---

## Database prerequisites (MySQL)

- [ ] Create database: **`azee`**
- [ ] Ensure your MySQL user has permissions on `azee`

Recommended SQL (adjust user/password as needed):

```sql
CREATE DATABASE IF NOT EXISTS azee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Example (optional):
-- CREATE USER 'azee_user'@'localhost' IDENTIFIED BY 'strong_password';
-- GRANT ALL PRIVILEGES ON azee.* TO 'azee_user'@'localhost';
-- FLUSH PRIVILEGES;
```

---

## Locked implementation decisions (defaults)

These are the defaults we will implement unless explicitly changed later.

### Storage disk for uploads

- **Decision**: Use Laravel Storage disk **`public`** for all uploaded images (projects/blogs/testimonials).
- **Why**: Simplest local + production parity; supports `php artisan storage:link`.

### Projects: `tech_stack` format

- **Decision**: Store `tech_stack` as **JSON** (array of strings).
  - Example: `["Laravel","Vue 3","Inertia","Tailwind","MySQL"]`
- **Why**: Easier rendering as badges and easier future filtering.

### Projects: `gallery` format

- **Decision**: Store `gallery` as **JSON** (array of image paths/URLs).
  - Example: `["projects/p1-1.webp","projects/p1-2.webp"]`
- **Why**: Meets requirements with minimal schema complexity; easy multi-upload in admin.

### Portfolio filtering taxonomy

- **Decision** (Phase 1–6): Use a **simple `category` string field** on `projects`.
  - Example: `"Web App"`, `"Landing Page"`, `"E-commerce"`
- **Why**: Fast to ship and matches “category-based” filters without adding extra tables yet.
- **Future upgrade path**: Add `project_categories` + pivot table if needed.

### Blog content format

- **Decision**: Store blog `content` as **HTML** (longtext) for the core scope.
- **Optional enhancement**: Markdown editor + safe rendering later (Phase 10).

---

## Phase 0 acceptance criteria

- [ ] DB `azee` exists and is reachable from your environment
- [ ] Decisions above are accepted as the project defaults
- [ ] Ready to start Phase 1 (Laravel 11 + Inertia/Vue/Tailwind + Lucide + Open Sans)

