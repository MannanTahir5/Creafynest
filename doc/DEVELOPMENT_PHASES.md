# Azee Portfolio Website — Development Phases

This document groups the full project scope into **development phases** so you can build sequentially without missing requirements. It is derived from `doc/PROJECT_TASK_LIST.md`. Database name: **`azee`**.

---

## Phase 0 — Prerequisites + planning guardrails

- [ ] Install local dependencies: PHP (Laravel 11 compatible), Composer, Node.js/npm, MySQL
- [ ] Create MySQL database: **`azee`**
- [ ] Confirm project root + `doc/` is canonical documentation location
- [ ] Decide a few baseline conventions (record decisions in this file)
  - [ ] Project image storage disk (`public` recommended)
  - [ ] `tech_stack` storage format (JSON vs string)
  - [ ] Project gallery storage (JSON column vs related table)
  - [ ] Project filtering taxonomy (simple `category` field vs categories/pivot)
  - [ ] Blog content format (HTML vs Markdown; Markdown is optional feature)

---

## Phase 1 — Bootstrap the stack (Laravel 11 + Inertia + Vue 3 + Tailwind)

- [ ] Create Laravel 11 project in workspace root
- [ ] Configure `.env` for MySQL:
  - [ ] `DB_CONNECTION=mysql`
  - [ ] `DB_DATABASE=azee`
  - [ ] `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] Verify DB connectivity (run migrations)
- [ ] Install + configure Inertia:
  - [ ] `inertiajs/inertia-laravel`
  - [ ] Vue 3 + `@inertiajs/vue3`
  - [ ] Inertia middleware + root view
  - [ ] Verify a sample Inertia page renders
- [ ] Install + configure Tailwind CSS
- [ ] Install Lucide (`lucide-vue-next`) and verify an icon renders
- [ ] Add Open Sans globally (Google Fonts or self-hosted) and apply via Tailwind/theme

Deliverable:

- [ ] App boots, renders an Inertia page styled by Tailwind, using Open Sans, with at least one Lucide icon.

---

## Phase 2 — Global UI shell (layout + nav + footer + UI primitives)

- [ ] Global Inertia layout (container, spacing, typography scale)
- [ ] Navbar
  - [ ] Links: Home, About, Portfolio, Services, Blog, Contact
  - [ ] Mobile menu (<640px)
  - [ ] Active link state
- [ ] Footer
  - [ ] Social links placeholders/config
  - [ ] Copyright
- [ ] UI primitives (recommended)
  - [ ] Button variants
  - [ ] Card
  - [ ] Badge/Tag
  - [ ] SectionHeading
  - [ ] Form inputs + error styles
  - [ ] Pagination component
  - [ ] EmptyState
  - [ ] Image wrapper (lazy loading + responsive behavior)
- [ ] Motion/UX rules
  - [ ] Hover states on cards/buttons/links
  - [ ] Smooth but minimal transitions
  - [ ] Optional reduced-motion support

Deliverable:

- [ ] All future pages share the same responsive shell and consistent design language.

---

## Phase 3 — Public routes + pages (static-first, then data-driven)

### 3A) Routing + controllers (public)

- [ ] Implement routes and controllers:
  - [ ] `GET /` Home
  - [ ] `GET /about` About
  - [ ] `GET /portfolio` Portfolio listing
  - [ ] `GET /portfolio/{slug}` Project detail
  - [ ] `GET /services` Services
  - [ ] `GET /blog` Blog listing (categories + search later)
  - [ ] `GET /blog/{slug}` Blog detail
  - [ ] `GET /contact` Contact
  - [ ] `POST /contact` Contact submission
- [ ] Add 404 behavior for unknown slugs

### 3B) Build Vue pages + sections (UI complete even before real data)

- [ ] Home `/`
  - [ ] Hero (intro + CTA)
  - [ ] About preview
  - [ ] Featured work preview
  - [ ] Services preview
  - [ ] Testimonials preview
  - [ ] Contact CTA
- [ ] About `/about`
  - [ ] Bio/introduction
  - [ ] Experience timeline
  - [ ] Skills (with icons)
  - [ ] Tools/technologies
- [ ] Portfolio `/portfolio`
  - [ ] Project grid
  - [ ] Filter UI (hook up later if data not ready)
- [ ] Project detail `/portfolio/{slug}`
  - [ ] Title + hero image
  - [ ] Description
  - [ ] Tech stack list
  - [ ] Gallery
  - [ ] Live/GitHub links
- [ ] Services `/services`
  - [ ] Service cards
  - [ ] Pricing section (optional)
  - [ ] CTA
- [ ] Blog `/blog`
  - [ ] Listing layout
  - [ ] Category filter UI
  - [ ] Search UI
  - [ ] Pagination UI
- [ ] Blog detail `/blog/{slug}`
  - [ ] Article layout (SEO-ready)
  - [ ] Cover image
  - [ ] Category/date
  - [ ] Related posts (optional)
- [ ] Contact `/contact`
  - [ ] Contact form UI
  - [ ] Google Map embed slot
  - [ ] Social links

### 3C) Build reusable content components

- [ ] `Hero.vue`
- [ ] `ProjectCard.vue`
- [ ] `BlogCard.vue`
- [ ] `ServiceCard.vue`
- [ ] `TestimonialCard.vue`
- [ ] `ContactForm.vue`

Deliverable:

- [ ] The entire public site exists and looks correct/responsive with placeholder/static data.

---

## Phase 4 — Data layer (migrations + models + seeders) and wiring pages to DB

### 4A) Migrations + models

- [ ] Users (for admin auth)
- [ ] Projects
  - [ ] `title`, `slug` (unique), `description`, `tech_stack`, `image`, `gallery`, `live_url`, `github_url`, timestamps
- [ ] Categories
  - [ ] `name`, `slug` (unique), timestamps
- [ ] Blogs
  - [ ] `title`, `slug` (unique), `content`, `image`, `category_id`, `meta_title`, `meta_description`, timestamps
- [ ] Services
  - [ ] `title`, `description`, `icon`, timestamps
- [ ] Testimonials
  - [ ] `name`, `feedback`, `image`, timestamps
- [ ] Contacts
  - [ ] `name`, `email`, `message`, timestamps
- [ ] Indexes + FKs
  - [ ] Unique slugs (projects/blogs/categories)
  - [ ] Blog FK to categories (+ chosen cascading behavior)
  - [ ] Useful indexes (`created_at`, `category_id`, etc.)

### 4B) Seeders/factories (dev/demo)

- [ ] Seed: projects, categories, blogs, services, testimonials

### 4C) Wire DB data to public pages

- [ ] Home: featured projects/services/testimonials (query + eager loading)
- [ ] Portfolio listing: paginated projects + filter support
- [ ] Project detail: load by slug, show gallery + tech stack
- [ ] Services: list from DB
- [ ] Blog listing: paginated, category filter, search
- [ ] Blog detail: load by slug, show category/date/content
- [ ] Contact form: validate + store to `contacts`

Deliverable:

- [ ] Public pages are fully data-driven with correct pagination/filter/search behavior.

---

## Phase 5 — Admin system (Breeze auth + /admin + CRUD + uploads)

### 5A) Auth (Laravel Breeze)

- [ ] Install Breeze (Inertia + Vue 3)
- [ ] Confirm login/logout flow works

### 5B) Admin area structure

- [ ] `/admin` routes protected by auth middleware
- [ ] Admin layout (sidebar + responsive drawer)
- [ ] Dashboard page with quick stats

### 5C) CRUD modules

- [ ] Projects CRUD
  - [ ] Slug handling strategy (auto/manual)
  - [ ] Single image upload
  - [ ] Gallery (multi-upload)
  - [ ] URL validation (live/github)
- [ ] Categories CRUD
  - [ ] Slug handling
  - [ ] Deletion policy (prevent vs reassign)
- [ ] Blogs CRUD
  - [ ] Category select
  - [ ] Image upload
  - [ ] Meta title/description editing
  - [ ] Content editor (textarea minimum)
- [ ] Services CRUD
  - [ ] Icon name mapping/picker (optional)
- [ ] Testimonials CRUD
  - [ ] Image upload
- [ ] Contacts management
  - [ ] Read-only view + delete
  - [ ] Read/unread (optional)

### 5D) Storage setup (uploads)

- [ ] Configure `public` disk usage + `storage:link`
- [ ] Validate upload mime/size
- [ ] Ensure uploaded images are visible on public pages

Deliverable:

- [ ] Admin can manage all content end-to-end; public site reflects updates.

---

## Phase 6 — SEO (must-have)

### 6A) Dynamic meta and Open Graph via Inertia

- [ ] Standardize a `meta` prop shape for all pages
- [ ] Global head handler in layout:
  - [ ] `<title>`
  - [ ] description
  - [ ] OG tags (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`)
  - [ ] Twitter card tags (recommended)

### 6B) Sitemap + robots

- [ ] Generate/serve `sitemap.xml`
  - [ ] Includes static pages + project slugs + blog slugs
- [ ] Serve `robots.txt`
  - [ ] Disallow `/admin`
  - [ ] Point to sitemap

### 6C) Schema markup (JSON-LD)

- [ ] Global Person/Organization + Website schema
- [ ] Blog post schema on blog detail pages
- [ ] Project schema on project detail pages (if applicable)

Deliverable:

- [ ] Per-page SEO metadata renders correctly; sitemap/robots/schema are present and correct.

---

## Phase 7 — Performance + hardening

- [ ] Images
  - [ ] WebP strategy for uploads and/or build assets
  - [ ] Lazy load non-critical images
  - [ ] Responsive sizing strategy (`srcset`) (optional)
- [ ] Laravel performance
  - [ ] Production caching strategy (config/route/view caches)
  - [ ] `php artisan optimize` as deploy step
- [ ] Query performance
  - [ ] Eager-load to prevent N+1
  - [ ] Ensure pagination everywhere it matters
- [ ] Application caching
  - [ ] Cache shared lists (categories, home sections)
  - [ ] Invalidate cache on admin updates
- [ ] CDN plan (Cloudflare)
  - [ ] Asset caching rules
  - [ ] Image caching strategy

Deliverable:

- [ ] Fast pages, minimal queries, optimized media, production caching plan in place.

---

## Phase 8 — QA + polish (release readiness)

- [ ] Responsiveness review (mobile/tablet/desktop)
- [ ] Form validation and error handling (contact + admin forms)
- [ ] Empty states for lists (no projects/blogs)
- [ ] 404/edge case handling (bad slugs)
- [ ] Visual polish: spacing, hover states, consistent typography
- [ ] Basic accessibility pass (focus states, color contrast)
- [ ] Smoke test: all admin CRUD paths + uploads

Deliverable:

- [ ] The site is stable, consistent, and ready for deployment.

---

## Phase 9 — Deployment

- [ ] Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Provision MySQL DB **`azee`** in production
- [ ] Run migrations
- [ ] Build frontend assets for production
- [ ] Enable caches (`optimize`, config/route/view caches)
- [ ] Storage permissions + `storage:link`
- [ ] Verify:
  - [ ] public pages
  - [ ] admin auth
  - [ ] uploads
  - [ ] `sitemap.xml` + `robots.txt`
- [ ] Connect Cloudflare (optional) + confirm caching rules

---

## Phase 10 — Optional enhancements (do after core is stable)

- [ ] Dark mode (Tailwind dark mode + toggle + persistence)
- [ ] Markdown blog editor + safe rendering + preview
- [ ] Advanced portfolio filters (multi-select + URL query syncing)
- [ ] Analytics integration
- [ ] Newsletter subscription (new table + opt-in flow)

# Azee Portfolio Website — Development Phases

This document groups the full project scope into **development phases** so you can build sequentially without missing requirements. It is derived from `doc/PROJECT_TASK_LIST.md`. Database name: **`azee`**.

---

## Phase 0 — Prerequisites + planning guardrails

- [ ] Install local dependencies: PHP (Laravel 11 compatible), Composer, Node.js/npm, MySQL
- [ ] Create MySQL database: **`azee`**
- [ ] Confirm project root + `doc/` is canonical documentation location
- [ ] Decide a few baseline conventions (record decisions in this file)
  - [ ] Project image storage disk (`public` recommended)
  - [ ] `tech_stack` storage format (JSON vs string)
  - [ ] Project gallery storage (JSON column vs related table)
  - [ ] Project filtering taxonomy (simple `category` field vs categories/pivot)
  - [ ] Blog content format (HTML vs Markdown; Markdown is optional feature)

---

## Phase 1 — Bootstrap the stack (Laravel 11 + Inertia + Vue 3 + Tailwind)

- [ ] Create Laravel 11 project in workspace root
- [ ] Configure `.env` for MySQL:
  - [ ] `DB_CONNECTION=mysql`
  - [ ] `DB_DATABASE=azee`
  - [ ] `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] Verify DB connectivity (run migrations)
- [ ] Install + configure Inertia:
  - [ ] `inertiajs/inertia-laravel`
  - [ ] Vue 3 + `@inertiajs/vue3`
  - [ ] Inertia middleware + root view
  - [ ] Verify a sample Inertia page renders
- [ ] Install + configure Tailwind CSS
- [ ] Install Lucide (`lucide-vue-next`) and verify an icon renders
- [ ] Add Open Sans globally (Google Fonts or self-host) and apply via Tailwind/theme

Deliverable:

- [ ] App boots, renders an Inertia page styled by Tailwind, using Open Sans, with at least one Lucide icon.

---

## Phase 2 — Global UI shell (layout + nav + footer + UI primitives)

- [ ] Global Inertia layout (container, spacing, typography scale)
- [ ] Navbar
  - [ ] Links: Home, About, Portfolio, Services, Blog, Contact
  - [ ] Mobile menu (<640px)
  - [ ] Active link state
- [ ] Footer
  - [ ] Social links placeholders/config
  - [ ] Copyright
- [ ] UI primitives (recommended)
  - [ ] Button variants
  - [ ] Card
  - [ ] Badge/Tag
  - [ ] SectionHeading
  - [ ] Form inputs + error styles
  - [ ] Pagination component
  - [ ] EmptyState
  - [ ] Image wrapper (lazy loading + responsive behavior)
- [ ] Motion/UX rules
  - [ ] Hover states on cards/buttons/links
  - [ ] Smooth but minimal transitions
  - [ ] Optional reduced-motion support

Deliverable:

- [ ] All future pages share the same responsive shell and consistent design language.

---

## Phase 3 — Public routes + pages (static-first, then data-driven)

### 3A) Routing + controllers (public)

- [ ] Implement routes and controllers:
  - [ ] `GET /` Home
  - [ ] `GET /about` About
  - [ ] `GET /portfolio` Portfolio listing
  - [ ] `GET /portfolio/{slug}` Project detail
  - [ ] `GET /services` Services
  - [ ] `GET /blog` Blog listing (categories + search later)
  - [ ] `GET /blog/{slug}` Blog detail
  - [ ] `GET /contact` Contact
  - [ ] `POST /contact` Contact submission
- [ ] Add 404 behavior for unknown slugs

### 3B) Build Vue pages + sections (UI complete even before real data)

- [ ] Home `/`
  - [ ] Hero (intro + CTA)
  - [ ] About preview
  - [ ] Featured work preview
  - [ ] Services preview
  - [ ] Testimonials preview
  - [ ] Contact CTA
- [ ] About `/about`
  - [ ] Bio/introduction
  - [ ] Experience timeline
  - [ ] Skills (with icons)
  - [ ] Tools/technologies
- [ ] Portfolio `/portfolio`
  - [ ] Project grid
  - [ ] Filter UI (hook up later if data not ready)
- [ ] Project detail `/portfolio/{slug}`
  - [ ] Title + hero image
  - [ ] Description
  - [ ] Tech stack list
  - [ ] Gallery
  - [ ] Live/GitHub links
- [ ] Services `/services`
  - [ ] Service cards
  - [ ] Pricing section (optional)
  - [ ] CTA
- [ ] Blog `/blog`
  - [ ] Listing layout
  - [ ] Category filter UI
  - [ ] Search UI
  - [ ] Pagination UI
- [ ] Blog detail `/blog/{slug}`
  - [ ] Article layout (SEO-ready)
  - [ ] Cover image
  - [ ] Category/date
  - [ ] Related posts (optional)
- [ ] Contact `/contact`
  - [ ] Contact form UI
  - [ ] Google Map embed slot
  - [ ] Social links

### 3C) Build reusable content components

- [ ] `Hero.vue`
- [ ] `ProjectCard.vue`
- [ ] `BlogCard.vue`
- [ ] `ServiceCard.vue`
- [ ] `TestimonialCard.vue`
- [ ] `ContactForm.vue`

Deliverable:

- [ ] The entire public site exists and looks correct/responsive with placeholder/static data.

---

## Phase 4 — Data layer (migrations + models + seeders) and wiring pages to DB

### 4A) Migrations + models

- [ ] Users (for admin auth)
- [ ] Projects
  - [ ] `title`, `slug` (unique), `description`, `tech_stack`, `image`, `gallery`, `live_url`, `github_url`, timestamps
- [ ] Categories
  - [ ] `name`, `slug` (unique), timestamps
- [ ] Blogs
  - [ ] `title`, `slug` (unique), `content`, `image`, `category_id`, `meta_title`, `meta_description`, timestamps
- [ ] Services
  - [ ] `title`, `description`, `icon`, timestamps
- [ ] Testimonials
  - [ ] `name`, `feedback`, `image`, timestamps
- [ ] Contacts
  - [ ] `name`, `email`, `message`, timestamps
- [ ] Indexes + FKs
  - [ ] Unique slugs (projects/blogs/categories)
  - [ ] Blog FK to categories (+ chosen cascading behavior)
  - [ ] Useful indexes (`created_at`, `category_id`, etc.)

### 4B) Seeders/factories (dev/demo)

- [ ] Seed: projects, categories, blogs, services, testimonials

### 4C) Wire DB data to public pages

- [ ] Home: featured projects/services/testimonials (query + eager loading)
- [ ] Portfolio listing: paginated projects + filter support
- [ ] Project detail: load by slug, show gallery + tech stack
- [ ] Services: list from DB
- [ ] Blog listing: paginated, category filter, search
- [ ] Blog detail: load by slug, show category/date/content
- [ ] Contact form: validate + store to `contacts`

Deliverable:

- [ ] Public pages are fully data-driven with correct pagination/filter/search behavior.

---

## Phase 5 — Admin system (Breeze auth + /admin + CRUD + uploads)

### 5A) Auth (Laravel Breeze)

- [ ] Install Breeze (Inertia + Vue 3)
- [ ] Confirm login/logout flow works

### 5B) Admin area structure

- [ ] `/admin` routes protected by auth middleware
- [ ] Admin layout (sidebar + responsive drawer)
- [ ] Dashboard page with quick stats

### 5C) CRUD modules

- [ ] Projects CRUD
  - [ ] Slug handling strategy (auto/manual)
  - [ ] Single image upload
  - [ ] Gallery (multi-upload)
  - [ ] URL validation (live/github)
- [ ] Categories CRUD
  - [ ] Slug handling
  - [ ] Deletion policy (prevent vs reassign)
- [ ] Blogs CRUD
  - [ ] Category select
  - [ ] Image upload
  - [ ] Meta title/description editing
  - [ ] Content editor (textarea minimum)
- [ ] Services CRUD
  - [ ] Icon name mapping/picker (optional)
- [ ] Testimonials CRUD
  - [ ] Image upload
- [ ] Contacts management
  - [ ] Read-only view + delete
  - [ ] Read/unread (optional)

### 5D) Storage setup (uploads)

- [ ] Configure `public` disk usage + `storage:link`
- [ ] Validate upload mime/size
- [ ] Ensure uploaded images are visible on public pages

Deliverable:

- [ ] Admin can manage all content end-to-end; public site reflects updates.

---

## Phase 6 — SEO (must-have)

### 6A) Dynamic meta and Open Graph via Inertia

- [ ] Standardize a `meta` prop shape for all pages
- [ ] Global head handler in layout:
  - [ ] `<title>`
  - [ ] description
  - [ ] OG tags (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`)
  - [ ] Twitter card tags (recommended)

### 6B) Sitemap + robots

- [ ] Generate/serve `sitemap.xml`
  - [ ] Includes static pages + project slugs + blog slugs
- [ ] Serve `robots.txt`
  - [ ] Disallow `/admin`
  - [ ] Point to sitemap

### 6C) Schema markup (JSON-LD)

- [ ] Global Person/Organization + Website schema
- [ ] Blog post schema on blog detail pages
- [ ] Project schema on project detail pages (if applicable)

Deliverable:

- [ ] Per-page SEO metadata renders correctly; sitemap/robots/schema are present and correct.

---

## Phase 7 — Performance + hardening

- [ ] Images
  - [ ] WebP strategy for uploads and/or build assets
  - [ ] Lazy load non-critical images
  - [ ] Responsive sizing strategy (`srcset`) (optional)
- [ ] Laravel performance
  - [ ] Production caching strategy (config/route/view caches)
  - [ ] `php artisan optimize` as deploy step
- [ ] Query performance
  - [ ] Eager-load to prevent N+1
  - [ ] Ensure pagination everywhere it matters
- [ ] Application caching
  - [ ] Cache shared lists (categories, home sections)
  - [ ] Invalidate cache on admin updates
- [ ] CDN plan (Cloudflare)
  - [ ] Asset caching rules
  - [ ] Image caching strategy

Deliverable:

- [ ] Fast pages, minimal queries, optimized media, production caching plan in place.

---

## Phase 8 — QA + polish (release readiness)

- [ ] Responsiveness review (mobile/tablet/desktop)
- [ ] Form validation and error handling (contact + admin forms)
- [ ] Empty states for lists (no projects/blogs)
- [ ] 404/edge case handling (bad slugs)
- [ ] Visual polish: spacing, hover states, consistent typography
- [ ] Basic accessibility pass (focus states, color contrast)
- [ ] Smoke test: all admin CRUD paths + uploads

Deliverable:

- [ ] The site is stable, consistent, and ready for deployment.

---

## Phase 9 — Deployment

- [ ] Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Provision MySQL DB **`azee`** in production
- [ ] Run migrations
- [ ] Build frontend assets for production
- [ ] Enable caches (`optimize`, config/route/view caches)
- [ ] Storage permissions + `storage:link`
- [ ] Verify:
  - [ ] public pages
  - [ ] admin auth
  - [ ] uploads
  - [ ] `sitemap.xml` + `robots.txt`
- [ ] Connect Cloudflare (optional) + confirm caching rules

---

## Phase 10 — Optional enhancements (do after core is stable)

- [ ] Dark mode (Tailwind dark mode + toggle + persistence)
- [ ] Markdown blog editor + safe rendering + preview
- [ ] Advanced portfolio filters (multi-select + URL query syncing)
- [ ] Analytics integration
- [ ] Newsletter subscription (new table + opt-in flow)

