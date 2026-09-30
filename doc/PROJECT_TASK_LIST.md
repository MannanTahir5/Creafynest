# Azee Portfolio Website — Master Task List

This document is the **complete, end-to-end task list** for building a personal portfolio website similar to **ronnieridge.com** using:

- **Backend**: Laravel 11
- **Frontend**: Vue 3 + Inertia.js
- **Database**: MySQL (**DB name: `azee`**)
- **Styling**: Tailwind CSS
- **Font**: Open Sans
- **Icons**: Lucide Icons

---

## Project goals (definition of done)

- **Public site** includes pages: Home, About, Portfolio, Project Detail, Services, Blog, Blog Detail, Contact
- **Admin panel** at `/admin` includes: dashboard + CRUD for Projects, Blogs, Categories, Services, Testimonials, Contacts
- **SEO is implemented** (dynamic meta, Open Graph, clean URLs, sitemap.xml, robots.txt, JSON-LD schema)
- **Performance**: lazy-loaded images, WebP strategy, caching strategy, optimized queries
- **Responsive** across mobile/tablet/desktop using Tailwind breakpoints
- **Design**: clean/minimal, whitespace heavy, smooth animations, hover effects, Open Sans + Lucide

---

## 0) Repository + environment prerequisites

- [ ] **Local dependencies installed**
  - [ ] PHP (compatible with Laravel 11)
  - [ ] Composer
  - [ ] Node.js + npm
  - [ ] MySQL server running
- [ ] **Database created**
  - [ ] Create MySQL database named **`azee`**
  - [ ] Create MySQL user/credentials (or use existing) with permissions on `azee`
- [ ] **Project folder ready**
  - [ ] Confirm this workspace is the intended project root
  - [ ] Ensure `doc/` stays the canonical documentation location

---

## 1) Project setup (Laravel 11 + Inertia + Vue 3 + Tailwind)

- [ ] **Create Laravel 11 app**
  - [ ] Install Laravel 11 into project root
  - [ ] Confirm app boots: `php artisan serve`
- [ ] **Configure `.env` for MySQL**
  - [ ] Set:
    - [ ] `DB_CONNECTION=mysql`
    - [ ] `DB_DATABASE=azee`
    - [ ] `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`
  - [ ] Confirm DB connection works (migrate runs)
- [ ] **Install Inertia + Vue 3**
  - [ ] `composer require inertiajs/inertia-laravel`
  - [ ] `npm install vue@3 @inertiajs/vue3`
  - [ ] Configure Inertia middleware + root view
  - [ ] Verify a sample Inertia page renders
- [ ] **Install Tailwind CSS**
  - [ ] Tailwind + PostCSS config
  - [ ] Add base Tailwind directives to CSS entry
  - [ ] Confirm Tailwind classes compile and render
- [ ] **Install Lucide icons**
  - [ ] `npm install lucide-vue-next`
  - [ ] Verify an icon renders in a Vue component
- [ ] **Add Open Sans globally**
  - [ ] Choose method (recommended: Google Fonts in base layout OR self-hosted via assets)
  - [ ] Apply font globally via Tailwind theme + base styles
  - [ ] Verify font applies to all pages

---

## 2) Global UI foundations (layout + navigation + footer + design system)

- [ ] **Create global Inertia layout**
  - [ ] Base layout wraps all pages
  - [ ] Responsive padding + max width container
  - [ ] Global typography scale (Tailwind)
  - [ ] Global link + button styles
- [ ] **Navbar**
  - [ ] Links: Home, About, Portfolio, Services, Blog, Contact
  - [ ] Mobile menu (hamburger) for <640px
  - [ ] Active link state
  - [ ] Lucide icons if desired (minimal)
- [ ] **Footer**
  - [ ] Copyright
  - [ ] Social links (placeholders + configurable)
  - [ ] Optional quick links
- [ ] **Reusable UI primitives (recommended)**
  - [ ] Button (primary/secondary/ghost)
  - [ ] Card
  - [ ] Badge/Tag (for categories/skills/filters)
  - [ ] Section header (title + subtitle)
  - [ ] Container
  - [ ] Input / Textarea / Form error styling
  - [ ] Pagination component (for blog list)
- [ ] **Animations / UX**
  - [ ] Subtle hover effects (cards, links, buttons)
  - [ ] Optional scroll reveal transitions (lightweight)
  - [ ] Respect reduced motion preference (if implemented)

---

## 3) Public routes + controllers (Laravel)

Implement these routes and their controllers/actions:

- [ ] `GET /` → Home
- [ ] `GET /about` → About
- [ ] `GET /portfolio` → Portfolio listing
- [ ] `GET /portfolio/{slug}` → Project detail
- [ ] `GET /services` → Services
- [ ] `GET /blog` → Blog listing (with categories + search)
- [ ] `GET /blog/{slug}` → Blog detail
- [ ] `GET /contact` → Contact page
- [ ] `POST /contact` → Contact form submit (store + notify optional)

Route-level requirements:

- [ ] Clean URLs (already implied by the above)
- [ ] Route model binding where appropriate (Projects, Blogs, Categories)
- [ ] 404 handling for unknown slugs

---

## 4) Database schema (migrations) + Eloquent models

### 4.1 Users (auth)

- [ ] Ensure default `users` table exists and is used for admin authentication
  - [ ] Fields: `id`, `name`, `email`, `password` (+ defaults like timestamps)

### 4.2 Projects

- [ ] Create migration/table: `projects`
  - [ ] Fields:
    - [ ] `id`
    - [ ] `title`
    - [ ] `slug` (unique)
    - [ ] `description` (text/longtext)
    - [ ] `tech_stack` (string or JSON; decide and implement consistently)
    - [ ] `image` (string path)
    - [ ] `gallery` (JSON for multiple images or a related table; decide and implement)
    - [ ] `live_url` (nullable)
    - [ ] `github_url` (nullable)
    - [ ] timestamps
- [ ] Model: `Project`
  - [ ] Slug generation strategy (manual in admin or auto on save)
  - [ ] Accessors/helpers for gallery array and tech stack list

### 4.3 Categories (for blog)

- [ ] Create migration/table: `categories`
  - [ ] Fields: `id`, `name`, `slug` (unique), timestamps
- [ ] Model: `Category`
  - [ ] Relationship: hasMany `Blog`

### 4.4 Blogs

- [ ] Create migration/table: `blogs`
  - [ ] Fields:
    - [ ] `id`
    - [ ] `title`
    - [ ] `slug` (unique)
    - [ ] `content` (longtext)
    - [ ] `image` (string path)
    - [ ] `category_id` (FK)
    - [ ] `meta_title` (nullable)
    - [ ] `meta_description` (nullable)
    - [ ] timestamps
- [ ] Model: `Blog`
  - [ ] Relationship: belongsTo `Category`
  - [ ] Slug strategy (manual/auto)
  - [ ] Content format: HTML or Markdown (if optional feature enabled)

### 4.5 Services

- [ ] Create migration/table: `services`
  - [ ] Fields: `id`, `title`, `description`, `icon`, timestamps
- [ ] Model: `Service`
  - [ ] Icon uses Lucide icon name mapping strategy (store `icon` key)

### 4.6 Testimonials

- [ ] Create migration/table: `testimonials`
  - [ ] Fields: `id`, `name`, `feedback`, `image`, timestamps
- [ ] Model: `Testimonial`

### 4.7 Contacts (messages)

- [ ] Create migration/table: `contacts`
  - [ ] Fields: `id`, `name`, `email`, `message`, timestamps
- [ ] Model: `Contact`

### 4.8 Constraints + indexes

- [ ] Add unique indexes: `projects.slug`, `blogs.slug`, `categories.slug`
- [ ] Add indexes for performance where needed:
  - [ ] `blogs.category_id`
  - [ ] `blogs.created_at`
  - [ ] `projects.created_at`
- [ ] Add foreign key constraints and cascading behavior for `blogs.category_id`

---

## 5) Seed data (for development/demo)

- [ ] Create seeders/factories for:
  - [ ] Projects (including image + gallery placeholders)
  - [ ] Categories
  - [ ] Blogs
  - [ ] Services
  - [ ] Testimonials
- [ ] Seed at least:
  - [ ] 6–12 projects across multiple categories (if you add project categories)
  - [ ] 3+ blog categories
  - [ ] 10+ blog posts
  - [ ] 3–6 services
  - [ ] 3–6 testimonials

---

## 6) Frontend pages (Vue + Inertia)

### Shared requirements for all pages

- [ ] Uses global layout (Navbar + Footer)
- [ ] Mobile-first responsive design
- [ ] Consistent spacing and typography
- [ ] Images: responsive, lazy loaded, and prepared for WebP usage
- [ ] Each page supports dynamic SEO props (title/description/OG when applicable)

### 6.1 Home page `/`

Sections:

- [ ] **Hero section** (intro + CTA)
  - [ ] Primary CTA (e.g., “View Work” → `/portfolio`)
  - [ ] Secondary CTA (e.g., “Contact” → `/contact`)
- [ ] **About preview**
  - [ ] Short summary + link to `/about`
- [ ] **Featured work / portfolio**
  - [ ] Show a curated subset of Projects
  - [ ] Link to `/portfolio`
- [ ] **Services overview**
  - [ ] Top services + link to `/services`
- [ ] **Testimonials**
  - [ ] Carousel or grid (lightweight)
- [ ] **Contact CTA**
  - [ ] Short CTA + button to `/contact`

### 6.2 About page `/about`

Sections:

- [ ] **Bio / introduction**
- [ ] **Experience timeline**
  - [ ] Data-driven structure (static for now or stored later)
- [ ] **Skills (with icons)**
  - [ ] Use Lucide icons for categories/skills
- [ ] **Tools/Technologies**
  - [ ] Badge list / icon grid

### 6.3 Portfolio page `/portfolio`

Sections:

- [ ] **Project grid**
  - [ ] Card layout with image, title, short excerpt, tags
- [ ] **Filters (category-based)**
  - [ ] Decide category source for projects:
    - [ ] Option A: `projects` has a `category` field (simple)
    - [ ] Option B: add `project_categories` + pivot (advanced)
  - [ ] Implement UI filter toggles + query params
- [ ] Pagination or infinite scroll (optional; choose one)
- [ ] Each project card links to `/portfolio/{slug}`

### 6.4 Project detail `/portfolio/{slug}`

Sections:

- [ ] Title + hero image
- [ ] Description
- [ ] Tech stack list (from `tech_stack`)
- [ ] Gallery (from `gallery`)
  - [ ] Lightbox (optional)
- [ ] Live demo link (if `live_url`)
- [ ] GitHub link (if `github_url`)
- [ ] “Back to Portfolio” navigation

### 6.5 Services page `/services`

Sections:

- [ ] Service cards (from `services`)
- [ ] Pricing section (optional)
- [ ] CTA (contact)

### 6.6 Blog page `/blog`

Sections:

- [ ] Blog listing grid/list
- [ ] Category filter (from `categories`)
- [ ] Search input
  - [ ] Search by title/content (server-side query)
- [ ] Pagination
- [ ] BlogCard links to `/blog/{slug}`

### 6.7 Blog detail `/blog/{slug}`

Requirements:

- [ ] SEO-optimized article layout
- [ ] Cover image
- [ ] Render content (HTML or Markdown-rendered)
- [ ] Show category + published date
- [ ] Social share (optional)
- [ ] Related posts (optional; same category)

### 6.8 Contact page `/contact`

Features:

- [ ] Contact form (name, email, message)
  - [ ] Client-side validation (basic)
  - [ ] Server-side validation (required)
  - [ ] Store into `contacts`
  - [ ] Success state + error display
- [ ] Google Map embed (configurable)
- [ ] Social links (configurable)

---

## 7) Frontend components (Vue)

Create and use reusable components:

- [ ] `Hero.vue`
- [ ] `ProjectCard.vue`
- [ ] `BlogCard.vue`
- [ ] `ServiceCard.vue`
- [ ] `TestimonialCard.vue`
- [ ] `ContactForm.vue`

Recommended additional components (to avoid repetition):

- [ ] `Navbar.vue`
- [ ] `Footer.vue`
- [ ] `SectionHeading.vue`
- [ ] `Tag.vue` / `Badge.vue`
- [ ] `Pagination.vue`
- [ ] `EmptyState.vue`
- [ ] `Image.vue` wrapper (handles lazy loading + srcset + placeholders)

---

## 8) Admin panel `/admin` (auth + CRUD + uploads)

### 8.1 Authentication

- [ ] Install Laravel Breeze with Inertia + Vue 3
- [ ] Confirm:
  - [ ] Login
  - [ ] Logout
  - [ ] Password hashing
  - [ ] Middleware protection on `/admin` routes

### 8.2 Admin routing + layout

- [ ] Admin layout with:
  - [ ] Sidebar navigation
  - [ ] Topbar (optional)
  - [ ] Responsive handling for mobile (drawer)
- [ ] Admin landing page: dashboard with quick stats

### 8.3 CRUD: Projects

- [ ] List view (table + search optional)
- [ ] Create form
- [ ] Edit form
- [ ] Delete action (soft delete optional)
- [ ] Slug management (auto or manual)
- [ ] Image upload for `image`
- [ ] Gallery upload for `gallery` (multiple images)
- [ ] Validate URLs for `live_url` and `github_url`

### 8.4 CRUD: Blog categories

- [ ] List categories
- [ ] Create category
- [ ] Edit category
- [ ] Delete category
- [ ] Slug management
- [ ] Prevent deleting category with blogs OR reassign (choose strategy)

### 8.5 CRUD: Blogs

- [ ] List view with filters (category) + search
- [ ] Create blog (title, slug, category, content, image, meta_title, meta_description)
- [ ] Edit blog
- [ ] Delete blog
- [ ] Image upload for `image`
- [ ] Content editor:
  - [ ] Basic textarea (minimum)
  - [ ] Optional Markdown editor (see Optional Features)

### 8.6 CRUD: Services

- [ ] List services
- [ ] Create service (title, description, icon)
- [ ] Edit service
- [ ] Delete service
- [ ] Icon picker (optional; map Lucide names)

### 8.7 CRUD: Testimonials

- [ ] List testimonials
- [ ] Create testimonial (name, feedback, image)
- [ ] Edit testimonial
- [ ] Delete testimonial
- [ ] Image upload for `image`

### 8.8 Contacts (messages)

- [ ] List contact submissions
- [ ] View detail (read-only)
- [ ] Delete (optional)
- [ ] Mark as read/unread (optional enhancement)

### 8.9 File storage (uploads)

- [ ] Configure Laravel Storage for uploads
  - [ ] Choose disk: `public`
  - [ ] Ensure `storage:link` is used
- [ ] Validate uploads (mime types, size limits)
- [ ] Store paths in DB
- [ ] Ensure images are served correctly

---

## 9) SEO optimization (VERY IMPORTANT)

### 9.1 Dynamic meta tags via Inertia props

- [ ] For each page, pass a `meta` object from controller:
  - [ ] `title`
  - [ ] `description`
  - [ ] (optional) `image` for OG
  - [ ] (optional) `canonical`
- [ ] In global layout (or a shared head component), set:
  - [ ] `<title>`
  - [ ] `<meta name="description">`
  - [ ] Open Graph tags:
    - [ ] `og:title`
    - [ ] `og:description`
    - [ ] `og:image`
    - [ ] `og:url`
    - [ ] `og:type`
  - [ ] Twitter card tags (recommended)

### 9.2 Sitemap

- [ ] Add `sitemap.xml`
  - [ ] Includes:
    - [ ] `/`, `/about`, `/portfolio`, `/services`, `/blog`, `/contact`
    - [ ] All projects `/portfolio/{slug}`
    - [ ] All blogs `/blog/{slug}`
  - [ ] Keep it up to date as content changes

### 9.3 robots.txt

- [ ] Add `robots.txt`
  - [ ] Allow public pages
  - [ ] Disallow admin paths `/admin`
  - [ ] Reference sitemap location

### 9.4 Schema markup (JSON-LD)

- [ ] Site-wide schema (Organization/Person + Website)
- [ ] Blog post schema for `/blog/{slug}`
- [ ] Project schema for `/portfolio/{slug}` (if applicable)

### 9.5 Clean URLs (confirmed)

- [ ] Ensure routes remain clean:
  - [ ] `/portfolio/project-name`
  - [ ] `/blog/article-name`

---

## 10) Performance optimization

- [ ] **Image optimization**
  - [ ] Use WebP where possible
  - [ ] Generate thumbnails/variants (optional)
  - [ ] Add `loading="lazy"` on non-critical images
  - [ ] Use responsive sizes / `srcset` strategy (optional)
- [ ] **Laravel optimization**
  - [ ] Run/enable `php artisan optimize` as part of deploy process
  - [ ] Route caching for production
  - [ ] Config caching for production
- [ ] **Database query optimization**
  - [ ] Avoid N+1 queries (use eager loading)
  - [ ] Add indexes (already listed)
  - [ ] Paginate lists (portfolio/blog/admin tables)
- [ ] **Caching**
  - [ ] Cache expensive queries (e.g., home featured content, blog categories list)
  - [ ] Define cache invalidation on CRUD updates
- [ ] **CDN**
  - [ ] Plan for Cloudflare (DNS + caching + image caching rules)

---

## 11) Responsiveness plan (Tailwind)

Apply consistent breakpoints:

- [ ] Mobile: `<640px`
- [ ] Tablet: `>=768px`
- [ ] Desktop: `>=1024px+`

Layout patterns:

- [ ] Use grids like `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` where appropriate
- [ ] Ensure navbar collapses on mobile
- [ ] Ensure cards stack cleanly on small screens
- [ ] Ensure spacing and typography remain readable on all sizes

---

## 12) Content + configuration strategy

- [ ] Decide what is admin-managed vs static:
  - [ ] Projects (admin-managed)
  - [ ] Blogs + categories (admin-managed)
  - [ ] Services (admin-managed)
  - [ ] Testimonials (admin-managed)
  - [ ] About timeline/skills/tools (static now or admin-managed later)
  - [ ] Social links + map embed (config file or DB settings; choose)

---

## 13) Quality gates (required checks)

- [ ] Public pages render without errors
- [ ] Admin login works and protects all `/admin` routes
- [ ] All CRUD operations function (create/edit/delete + validations)
- [ ] Uploads work and images display on public + admin pages
- [ ] Slugs are unique and resolve correctly
- [ ] Search and filters work (blog search, blog categories, portfolio filters)
- [ ] SEO head tags render correctly per page (including OG)
- [ ] `sitemap.xml` and `robots.txt` are accessible and correct
- [ ] Site is responsive on mobile/tablet/desktop
- [ ] Lighthouse-style performance pass (best effort): avoid uncompressed images, avoid blocking assets

---

## 14) Cursor-driven prompt execution plan (milestones)

Use these milestones as sequential build prompts:

- [ ] **Prompt 1 — Project Setup**
  - [ ] Laravel 11 + Vue 3 + Inertia
  - [ ] Tailwind CSS
  - [ ] MySQL DB set to `azee`
  - [ ] Lucide + Open Sans globally
- [ ] **Prompt 2 — Auth System**
  - [ ] Breeze auth with Inertia + Vue 3
- [ ] **Prompt 3 — Global Layout**
  - [ ] Responsive Navbar + Footer + layout shell
- [ ] **Prompt 4 — Pages**
  - [ ] Home, About, Portfolio, Project Detail, Services, Blog, Blog Detail, Contact
  - [ ] Reusable components
- [ ] **Prompt 5 — Database & Models**
  - [ ] Migrations/models/controllers: Projects, Blogs, Categories, Services, Testimonials, Contacts
- [ ] **Prompt 6 — Admin Panel**
  - [ ] Admin dashboard + CRUD for all models + protected routes
  - [ ] Image uploads using Storage
- [ ] **Prompt 7 — SEO**
  - [ ] Dynamic meta + OG
  - [ ] sitemap.xml + robots.txt
  - [ ] JSON-LD schema
- [ ] **Prompt 8 — Performance**
  - [ ] Lazy images, WebP strategy, caching, query optimization

---

## 15) Optional but powerful enhancements

- [ ] **Dark mode**
  - [ ] Toggle in UI
  - [ ] Persist preference (localStorage)
  - [ ] Tailwind dark mode configuration
- [ ] **Blog CMS editor (Markdown)**
  - [ ] Store Markdown in DB (or convert to HTML)
  - [ ] Render safely on frontend
  - [ ] Admin preview
- [ ] **Advanced project filtering**
  - [ ] Multi-select filters
  - [ ] URL query param syncing
- [ ] **Analytics**
  - [ ] Google Analytics (or privacy-friendly alternative)
  - [ ] Ensure no impact on core performance
- [ ] **Newsletter subscription**
  - [ ] Signup form
  - [ ] Store subscribers table (new migration)
  - [ ] Double opt-in (optional)

---

## 16) Deployment checklist (when you’re ready)

- [ ] Production `.env` configured
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Run migrations on production DB `azee`
- [ ] Run `php artisan optimize` (and caches)
- [ ] Configure storage + permissions
- [ ] Set up queue/mail if contact notifications are added
- [ ] Verify `sitemap.xml` + `robots.txt` in production
- [ ] Connect CDN (Cloudflare) and set caching rules

