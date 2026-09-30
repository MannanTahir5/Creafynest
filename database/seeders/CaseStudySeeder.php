<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Support\CaseStudyPlaceholderImage;
use App\Support\ContentCache;
use App\Support\WebpDerivative;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CaseStudySeeder extends Seeder
{
    /**
     * Seed curated case studies with rich content, story sections, and hero images.
     */
    public function run(): void
    {
        $studies = [
            [
                'slug' => 'myntist',
                'title' => 'Myntist',
                'category' => 'Web',
                'client_name' => 'Myntist Labs',
                'industry' => 'Blockchain & Web3',
                'services' => ['Web Development', 'UI/UX Design', 'Smart Contracts'],
                'tech_stack' => ['MERN Stack', 'Terra Blockchain', 'Web3.js', 'Solidity'],
                'description' => "A platform built on the Terra blockchain, enabling artists and creators to mint and trade digital assets with a clear, trustworthy marketplace experience.\n\nThe product team needed fast onboarding, wallet flows, and responsive layouts for collectors on the go.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Myntist envisioned a streamlined NFT marketplace on the Terra ecosystem. Creators needed a simple way to mint, showcase, and auction digital artwork without complex crypto friction.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Handling web3 wallet connections, transaction latency, and responsive metadata display across mobile and desktop devices without degrading page speed.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>We built a modern MERN stack application connected to Terra smart contracts. We implemented optimistic UI updates, lazy-loaded media assets, and custom responsive UI cards.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Launched successfully with over 50,000+ digital assets minted in the first month and sub-second page transition speeds.</p>',
                'image' => 'myntist.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => 'https://github.com/example/myntist',
            ],
            [
                'slug' => 'ledgerhub',
                'title' => 'LedgerHub',
                'category' => 'Custom Software',
                'client_name' => 'LedgerHub Financial',
                'industry' => 'Financial SaaS',
                'services' => ['Full Stack Development', 'SaaS Architecture', 'UI/UX Design'],
                'tech_stack' => ['Laravel 11', 'Vue 3', 'Inertia.js', 'MySQL', 'Redis'],
                'description' => "A multi-tenant SaaS for finance teams to reconcile payouts, automate approvals, and export audit-ready ledgers.\n\nWe shipped role-based dashboards, CSV ingestion, and email digests so stakeholders always see up-to-date numbers without leaving the app.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Finance teams were spending hours manually reconciling multi-currency payouts and audit logs across disparate banking spreadsheets.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Ingesting millions of CSV rows with zero data corruption while maintaining interactive, multi-tenant dashboards with instant search and filtering.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Architected a modular Laravel 11 and Inertia.js (Vue 3) platform. Implemented asynchronous chunked CSV imports, background queue workers, and role-based permissions.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Reduced payout reconciliation times from days to minutes for 120+ enterprise finance teams.</p>',
                'image' => 'ledgerhub.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => 'https://github.com/example/ledgerhub',
            ],
            [
                'slug' => 'bloom-commerce',
                'title' => 'Bloom Commerce',
                'category' => 'E-commerce',
                'client_name' => 'Bloom Beauty Group',
                'industry' => 'D2C E-commerce',
                'services' => ['Headless Commerce', 'Vue 3 Storefront', 'SEO Optimization'],
                'tech_stack' => ['Vue 3', 'Vite', 'TailwindCSS', 'Stripe', 'WebP'],
                'description' => "A headless storefront and admin for a growing D2C brand, with inventory sync, promotions, and SEO-friendly product pages.\n\nPerformance budgets and image pipelines were tuned so mobile shoppers get instant category browsing and checkout in a few taps.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Bloom Commerce wanted to transition from a slow monolithic store to a lightning-fast headless D2C shopping experience.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Achieving 95+ Google Lighthouse performance scores while displaying high-resolution product galleries and dynamic promotional offers.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Developed a custom Vue 3 single page application with WebP automatic image optimization, edge caching, and seamless Stripe checkout integration.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Increased mobile conversion rate by 34% and reduced page load times under 800ms globally.</p>',
                'image' => 'bloom-commerce.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'pulseboard',
                'title' => 'Pulseboard',
                'category' => 'UI/UX',
                'client_name' => 'Pulseboard Analytics',
                'industry' => 'Marketing Analytics',
                'services' => ['Real-time Dashboards', 'UI/UX Design', 'API Integration'],
                'tech_stack' => ['Vue 3', 'Vite', 'TailwindCSS', 'WebSockets', 'Laravel'],
                'description' => "A real-time analytics workspace for marketing squads: funnels, cohorts, and scheduled reports in one cohesive interface.\n\nThe UI emphasizes scanability—dense data with calm typography, keyboard shortcuts, and dark mode for long review sessions.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Marketing agencies needed a unified workspace to track multi-channel funnels, user cohorts, and custom campaign metrics in real time.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Presenting dense data structures with calm typography, dark mode support, and keyboard shortcuts without cluttering the screen.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Designed and engineered an ultra-responsive analytics dashboard using Vue 3, Vite, Tailwind CSS, and WebSockets for live data stream updates.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Adopted by 40+ growth agencies, reducing daily client report generation time by 80%.</p>',
                'image' => 'pulseboard.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'relay-insights',
                'title' => 'Relay Insights',
                'category' => 'Custom Software',
                'client_name' => 'Relay Corp',
                'industry' => 'Product Research',
                'services' => ['Custom Software', 'Full-Text Search', 'Web Development'],
                'tech_stack' => ['Laravel', 'Vue 3', 'PostgreSQL', 'Full-Text Search'],
                'description' => "A customer research hub where product teams collect interviews, tag themes, and share insight libraries across squads.\n\nWe built searchable transcripts, permissioned spaces for sensitive notes, and exports that slot into existing wiki and roadmap tools.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Product managers needed a centralized hub to collect customer interview transcripts, tag recurring themes, and share research insights across teams.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Building fast full-text search across thousands of customer transcripts with strict privacy and granular team access controls.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Engineered a Laravel + Vue 3 platform with PostgreSQL full-text search indexing, secure team workspaces, and automatic export plugins.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Enabled product teams to turn raw interview transcripts into actionable product roadmap features 3x faster.</p>',
                'image' => 'relay-insights.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'fieldlink-mobile',
                'title' => 'FieldLink',
                'category' => 'Mobile Apps',
                'client_name' => 'FieldLink Solutions',
                'industry' => 'Field Operations & Logistics',
                'services' => ['Mobile App', 'Offline First Architecture', 'API Development'],
                'tech_stack' => ['Capacitor', 'Vue 3', 'TailwindCSS', 'Laravel', 'SQLite'],
                'description' => "A native-grade field operations app for inspections, work orders, and capture-heavy workflows—with offline queues and background sync when crews regain connectivity.\n\nTechnicians get large tap targets, GPS-aware views, and photo markup so managers see reality on the ground without laptop workflows.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Field technicians required a reliable app for work orders and equipment inspections, operating in remote areas with poor network coverage.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Ensuring seamless offline data queues and background synchronization when field crews regain internet connectivity.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Built a native-grade mobile application using Capacitor, Vue 3, and Tailwind CSS backed by a robust Laravel queue system.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Streamlined 10,000+ monthly field inspections with zero data loss reported during offline operation.</p>',
                'image' => 'fieldlink-mobile.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'northern-stack-erp',
                'title' => 'Northern Stack ERP',
                'category' => 'Custom Software',
                'client_name' => 'Northern Industrial',
                'industry' => 'Manufacturing & ERP',
                'services' => ['Custom ERP', 'Barcode Integration', 'Laravel Development'],
                'tech_stack' => ['Laravel', 'Vue 3', 'MySQL', 'Barcode API', 'REST APIs'],
                'description' => "Bespoke ERP modules for inventory, procurement, and shop-floor scheduling—integrated with accounting and barcode hardware.\n\nWe replaced spreadsheets with role-specific screens, audit trails, and APIs so the vendor can extend modules without breaking core workflows.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>A manufacturing plant needed to replace legacy paper logs and fragmented spreadsheets with automated shop-floor scheduling.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Integrating legacy accounting systems, barcode scanners, and live inventory tracking with role-based user interfaces.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Developed tailored ERP modules with real-time barcode scanning APIs, audit logs, and clear operational workflows.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Achieved 99.8% inventory accuracy and eliminated paper-based inventory auditing across 3 distribution warehouses.</p>',
                'image' => 'northern-stack-erp.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'atlas-ui-system',
                'title' => 'Atlas Design System',
                'category' => 'UI/UX',
                'client_name' => 'Atlas Enterprise',
                'industry' => 'Design Systems & UI/UX',
                'services' => ['Design System', 'Component Library', 'WCAG Accessibility'],
                'tech_stack' => ['Vue 3', 'TailwindCSS', 'Storybook', 'Figma', 'WCAG AA'],
                'description' => "An enterprise design system and Figma-aligned component library—tokens, accessibility patterns, and documentation for product squads shipping on a shared stack.\n\nWe standardized spacing, states, and data visualization so new features look cohesive from day one across web and embedded admin surfaces.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>An enterprise engineering team needed a unified component library to maintain design consistency across 12 product applications.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Ensuring strict WCAG AA accessibility compliance, flexible design tokens, and smooth Storybook integration.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Created a comprehensive Vue 3 & Tailwind CSS component library with automated accessibility test suites and full Figma token alignment.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Shortened frontend development cycles for new product features by 45% across all product squads.</p>',
                'image' => 'atlas-ui-system.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'swiftcare-patient-portal',
                'title' => 'SwiftCare Patient Portal',
                'category' => 'Web',
                'client_name' => 'SwiftCare Health',
                'industry' => 'HealthTech & Web',
                'services' => ['HIPAA Web Portal', 'UI/UX Design', 'Full Stack Development'],
                'tech_stack' => ['Laravel', 'Vue 3', 'MySQL', 'Twilio SMS', 'HIPAA'],
                'description' => "A secure web portal where patients book visits, complete intake forms, and see lab results—with reminders that respect HIPAA-friendly policies.\n\nWe focused on clarity for older adults, fast load on hospital Wi‑Fi, and an admin console care coordinators actually enjoy using.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>SwiftCare wanted an intuitive web portal for patients to schedule appointments, complete intake forms, and access lab results easily.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Designing an accessible interface suitable for elderly patients while adhering strictly to HIPAA security standards.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Developed an accessible, high-contrast Vue 3 portal with encrypted data transmission, automated appointment SMS reminders, and administrative triage views.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Processed over 100,000 patient appointments online and reduced clinic front-desk call volume by 50%.</p>',
                'image' => 'swiftcare-patient-portal.jpg',
                'image_seed' => 'ledgerhub.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'northwind-operations-hub',
                'title' => 'Northwind Operations Hub',
                'category' => 'Custom Software',
                'client_name' => 'Northwind Logistics',
                'industry' => 'Logistics & Operations',
                'services' => ['Operations Dashboard', 'Real-Time APIs', 'Vue 3 Development'],
                'tech_stack' => ['Laravel', 'Vue 3', 'Redis', 'WebSockets', 'MySQL'],
                'description' => "A single dashboard for regional managers: live sales, stock alerts, and delivery exceptions pulled from legacy ERP and warehouse APIs.\n\nWe added role-based views, scheduled exports, and anomaly flags so teams fix problems the same day instead of digging through spreadsheets.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Regional logistics managers needed a central hub to monitor live fleet sales, stock alerts, and delivery exception events.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Aggregating data from legacy ERP and warehouse systems into a single live dashboard without system lag.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Built a real-time dashboard powered by Laravel, Redis, and Vue 3 with automated anomaly detection flags and scheduled PDF exports.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Enabled operations teams to resolve 92% of delivery exceptions on the same day.</p>',
                'image' => 'northwind-operations-hub.jpg',
                'image_seed' => 'pulseboard.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
            [
                'slug' => 'aurora-booking-co',
                'title' => 'Aurora Booking Co.',
                'category' => 'E-commerce',
                'client_name' => 'Aurora Wellness',
                'industry' => 'E-commerce & Booking',
                'services' => ['Booking Engine', 'Stripe Integration', 'Custom CMS'],
                'tech_stack' => ['Laravel', 'Vue 3', 'Inertia.js', 'Stripe Billing', 'Twilio'],
                'description' => "A booking and payments experience for multi-location studios—packages, memberships, and staff calendars in one flow.\n\nCustomers get instant confirmations; owners get Stripe payouts, no-show rules, and a lightweight CMS for promos without calling a developer.",
                'how_it_started_title' => '01 — How it started',
                'how_it_started_text' => '<p>Aurora Booking Co. needed a seamless platform for customers to reserve wellness sessions, purchase packages, and manage memberships.</p>',
                'challenge_title' => '02 — The challenge',
                'challenge_text' => '<p>Managing complex multi-location staff calendars and automated recurring Stripe subscription payouts.</p>',
                'approach_title' => '03 — Our approach',
                'approach_text' => '<p>Created a custom Inertia + Vue 3 booking application integrated with Stripe Billing, staff schedule management, and instant SMS confirmations.</p>',
                'results_title' => '04 — Results & Impact',
                'results_text' => '<p>Increased online membership signups by 65% across 8 studio locations.</p>',
                'image' => 'aurora-booking-co.jpg',
                'image_seed' => 'bloom-commerce.jpg',
                'gallery' => null,
                'live_url' => 'https://example.com',
                'github_url' => null,
            ],
        ];

        foreach ($studies as $attrs) {
            $seedBasename = $attrs['image_seed'] ?? $attrs['image'];
            $storedFilename = $attrs['image'];
            unset($attrs['image_seed']);

            $attrs['image'] = self::publishSeedImage($seedBasename, $storedFilename);

            Project::query()->updateOrCreate(
                ['slug' => $attrs['slug']],
                $attrs
            );

            CaseStudyPlaceholderImage::ensure($attrs['slug'], $attrs['title']);
        }

        ContentCache::forget();
    }

    /**
     * Copy a seed JPEG into public storage. If the source file is missing (optional assets folder), the project is saved without an image.
     *
     * @return string|null Relative path on the public disk (e.g. projects/case-study/foo.jpg)
     */
    private static function publishSeedImage(string $seedBasename, ?string $storedFilename = null): ?string
    {
        $src = database_path('seeders/assets/case-study/'.$seedBasename);
        if (! is_file($src)) {
            return null;
        }

        $filename = $storedFilename ?? $seedBasename;
        $relative = 'projects/case-study/'.$filename;
        Storage::disk('public')->put($relative, (string) file_get_contents($src));
        WebpDerivative::encodeFromStoredPublicPath($relative);

        return $relative;
    }
}
