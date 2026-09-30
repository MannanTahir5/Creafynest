<?php

namespace Database\Seeders;

use App\Models\DeliveryCategory;
use App\Models\Service;
use App\Support\ServiceContentNormalizer;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $webCategory = DeliveryCategory::query()
            ->where('slug', 'web-development')
            ->first();

        $mobileCategory = DeliveryCategory::query()
            ->where('slug', 'mobile-development')
            ->first();

        $baseContent = function (string $label, string $highlight): array {
            $d = ServiceContentNormalizer::defaultStructure();
            $d['hero']['eyebrow'] = $label;
            $d['hero']['heading_prefix'] = 'We build';
            $d['hero']['heading_highlight'] = $highlight;
            $d['hero']['description'] = 'Clear, production-minded delivery with measurable outcomes and a handoff your team can own.';
            $d['hero']['chips'] = [
                ['icon' => 'Layers', 'label' => 'Ship-ready'],
                ['icon' => 'Shield', 'label' => 'Secure by default'],
            ];
            $d['tech']['heading_prefix'] = 'Stack';
            $d['tech']['description'] = 'Pragmatic tool choices and patterns that match your environment.';
            $d['tech']['items'] = [
                ['name' => 'Laravel', 'slug' => 'laravel'],
                ['name' => 'Vue.js', 'slug' => 'vuedotjs'],
            ];
            $d['outcomes']['heading'] = 'What we ship';
            $d['outcomes']['description'] = 'Structured milestones, visible progress, and docs your engineers expect.';
            $d['outcomes']['cards'] = [
                [
                    'icon' => 'LayoutTemplate',
                    'title' => 'Product UX',
                    'description' => 'Flows and interfaces tuned for clarity and conversion.',
                ],
                [
                    'icon' => 'Database',
                    'title' => 'Solid foundations',
                    'description' => 'Schemas, APIs, and conventions that scale with your roadmap.',
                ],
            ];

            return $d;
        };

        Service::query()->updateOrCreate(
            ['slug' => 'website-ui-ux'],
            [
                'delivery_category_id' => $webCategory?->id,
                'title' => 'Website UI/UX',
                'description' => 'Research-backed UI, accessible components, and a handoff your engineers can ship with confidence.',
                'icon' => 'Sparkles',
                'sort_order' => 0,
                'is_active' => true,
                'meta_title' => 'Website UI/UX',
                'meta_description' => 'We structure ideas into clear, high-converting interfaces—design systems, flows, and craft for web products that feel as good as they look.',
                'hero_image_path' => 'services/website-ui-ux/hero.png',
                'tech_image_path' => null,
                'outcomes_image_path' => null,
                'content' => [
                    'hero' => [
                        'eyebrow' => 'Website UI / UX',
                        'heading_prefix' => 'We structure',
                        'heading_highlight' => 'ideas',
                        'subhead' => 'into clear, beautiful products',
                        'description' => 'Research, flows, design systems, and refined visual craft—so the first interaction with your product feels intentional, accessible, and premium.',
                        'chips' => [
                            ['icon' => 'Layers', 'label' => 'Systems & tokens'],
                            ['icon' => 'Palette', 'label' => 'Visual craft'],
                            ['icon' => 'Accessibility', 'label' => 'Accessible patterns'],
                        ],
                        'primary_cta_label' => 'Start a project',
                        'primary_cta_href' => '/contact',
                        'secondary_cta_label' => 'All services',
                        'secondary_cta_href' => '/services',
                        'image_alt' => 'Isometric 3D illustration of a laptop, tablet, and smartphone with dashboards, code, and data visualizations.',
                        'caption_eyebrow' => 'Interface layer',
                        'caption_text' => 'Production-ready UI — clarity, motion, and trust.',
                    ],
                    'tech' => [
                        'eyebrow' => 'Web stack',
                        'heading_prefix' => 'Web design',
                        'heading_highlight' => '& development',
                        'description' => 'Full-cycle delivery—design, build, hosting, and ongoing care—using modern stacks our teams ship with every day. Reliable, scalable products built with the tools your roadmap already speaks.',
                        'items' => [
                            ['name' => 'Node.js', 'slug' => 'nodedotjs'],
                            ['name' => 'Sass', 'slug' => 'sass'],
                            ['name' => 'PHP', 'slug' => 'php'],
                            ['name' => 'Python', 'slug' => 'python'],
                            ['name' => '.NET', 'slug' => 'dotnet'],
                            ['name' => 'React', 'slug' => 'react'],
                            ['name' => 'Java', 'slug' => 'java'],
                            ['name' => 'Vue.js', 'slug' => 'vuedotjs'],
                            ['name' => 'Angular', 'slug' => 'angular'],
                        ],
                        'cta_label' => 'Let’s talk about your project',
                        'cta_href' => '/contact',
                        'image_alt' => '',
                    ],
                    'outcomes' => [
                        'eyebrow' => 'Outcomes',
                        'heading' => 'What we deliver',
                        'description' => 'Research-backed UI, accessible components, and a handoff your engineers can ship with confidence.',
                        'cards' => [
                            [
                                'icon' => 'LayoutTemplate',
                                'title' => 'Product & marketing sites',
                                'description' => 'IA, key screens, and responsive layouts that match your brand and performance budget.',
                            ],
                            [
                                'icon' => 'MousePointer2',
                                'title' => 'Design systems',
                                'description' => 'Tokens, components, and documentation so every new feature stays on-brand.',
                            ],
                            [
                                'icon' => 'Palette',
                                'title' => 'UX refinement',
                                'description' => 'Flow fixes, usability passes, and measurable improvements to completion and retention.',
                            ],
                        ],
                        'cta_label' => 'Discuss your UI / UX',
                        'cta_href' => '/contact',
                        'image_alt' => '',
                    ],
                ],
            ]
        );

        Service::query()->updateOrCreate(
            ['slug' => 'custom-web-applications'],
            [
                'delivery_category_id' => $webCategory?->id,
                'title' => 'Custom Web Applications',
                'description' => 'Dashboards, internal tools, and multi-tenant products with predictable releases.',
                'icon' => 'Box',
                'sort_order' => 1,
                'is_active' => true,
                'meta_title' => 'Custom Web Applications',
                'meta_description' => 'Bespoke web apps with Laravel, Vue, and pragmatic architecture—auth, roles, observability, and deploy pipelines included.',
                'content' => $baseContent('Custom apps', 'reliable software'),
            ]
        );

        Service::query()->updateOrCreate(
            ['slug' => 'mobile-app-ui-ux'],
            [
                'delivery_category_id' => $mobileCategory?->id,
                'title' => 'Mobile App UI/UX',
                'description' => 'Gesture-aware flows, performance-first layouts, and polish for App Store–ready products.',
                'icon' => 'Smartphone',
                'sort_order' => 0,
                'is_active' => true,
                'meta_title' => 'Mobile App UI/UX',
                'meta_description' => 'Product-grade mobile UX—motion, accessibility, and visual systems tuned for small screens.',
                'content' => $baseContent('Mobile UX', 'delightful apps'),
            ]
        );
    }
}
