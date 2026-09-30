<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Support\ContentCache;
use Illuminate\Database\Seeder;

/**
 * Three editorial-style posts for the public /blog page (idempotent by slug).
 * Expects categories seeded with slugs: laravel, vue, seo (see DatabaseSeeder).
 */
class SampleBlogsSeeder extends Seeder
{
    public function run(): void
    {
        $laravelId = Category::query()->firstOrCreate(
            ['slug' => 'laravel'],
            ['name' => 'Laravel'],
        )->id;

        $vueId = Category::query()->firstOrCreate(
            ['slug' => 'vue'],
            ['name' => 'Vue'],
        )->id;

        $seoId = Category::query()->firstOrCreate(
            ['slug' => 'seo'],
            ['name' => 'SEO'],
        )->id;

        $posts = [
            [
                'slug' => 'shipping-faster-with-laravel-inertia',
                'title' => 'Shipping faster with Laravel and Inertia',
                'category_id' => $laravelId,
                'meta_title' => 'Shipping faster with Laravel and Inertia | Azee',
                'meta_description' => 'How we structure Laravel backends with Inertia and Vue for predictable releases, fewer regressions, and a single deployment story.',
                'content' => $this->html(
                    'Shipping faster with Laravel and Inertia',
                    [
                        'Inertia sits in a sweet spot: server-driven routing and validation with a modern Vue front end. Teams ship features without maintaining two separate API layers for every screen.',
                        'We start with clear boundaries: form requests for input, policies for authorization, and thin controllers that return Inertia responses. That keeps pages easy to test and refactor.',
                        'On the client, we prefer composables for shared behaviour and keep page components focused on layout and wiring. Lazy-loaded props keep first paint snappy on heavy dashboards.',
                    ],
                    [
                        'Use deferred props for non-critical data on large pages.',
                        'Standardize error bags and flash messages so UX stays consistent.',
                        'Invest in a small set of UI primitives instead of one-off styles.',
                    ]
                ),
            ],
            [
                'slug' => 'vue-3-patterns-for-reusable-ui',
                'title' => 'Vue 3 patterns we reach for in product builds',
                'category_id' => $vueId,
                'meta_title' => 'Vue 3 patterns for reusable UI | Azee',
                'meta_description' => 'Composition API habits, component boundaries, and accessibility defaults that keep Vue 3 codebases maintainable as products grow.',
                'content' => $this->html(
                    'Vue 3 patterns we reach for in product builds',
                    [
                        'The Composition API shines when behaviour is reused across routes. We extract stateful logic into composables and leave SFCs responsible for markup and local UI state.',
                        'Props stay narrow and explicit; emits document the parent contract. That makes refactors safer when designers iterate on flows.',
                        'We bake in accessibility early: focus management for dialogs, semantic headings, and keyboard paths for custom controls—not as a polish pass at the end.',
                    ],
                    [
                        'Prefer defineModel and typed props when using script setup.',
                        'Co-locate small presentational components next to the feature that owns them.',
                        'Use Suspense sparingly; pair it with clear loading and error UI.',
                    ]
                ),
            ],
            [
                'slug' => 'technical-seo-for-inertia-sites',
                'title' => 'Technical SEO basics for Inertia and SPA-style Laravel apps',
                'category_id' => $seoId,
                'meta_title' => 'Technical SEO for Inertia sites | Azee',
                'meta_description' => 'Canonical URLs, meta tags rendered for crawlers, and performance habits that help Inertia apps rank without a separate marketing site.',
                'content' => $this->html(
                    'Technical SEO basics for Inertia and SPA-style Laravel apps',
                    [
                        'Search engines can execute JavaScript, but we still render critical metadata on the server via the Blade root and shared Inertia head helpers. Consistent titles and descriptions beat clever client-only tricks.',
                        'Clean URLs, stable canonicals, and honest redirects matter more than keyword stuffing. We map each public route to one canonical URL and avoid duplicate content across filters.',
                        'Core Web Vitals are part of SEO: optimize images, trim unused JS on marketing pages, and measure with real devices—not just Lighthouse in ideal conditions.',
                    ],
                    [
                        'Ship an XML sitemap and keep it in sync with published routes.',
                        'Use structured data where it reflects real page content.',
                        'Monitor Search Console after major releases for crawl anomalies.',
                    ]
                ),
            ],
        ];

        foreach ($posts as $row) {
            Blog::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'category_id' => $row['category_id'],
                    'content' => $row['content'],
                    'content_format' => 'html',
                    'image' => null,
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_keywords' => null,
                    'og_title' => null,
                    'og_description' => null,
                    'og_image_path' => null,
                    'noindex' => false,
                    'canonical_url' => null,
                    'schema_type' => 'BlogPosting',
                ]
            );
        }

        ContentCache::forget();
    }

    /**
     * @param  list<string>  $paragraphs
     * @param  list<string>  $bullets
     */
    private function html(string $h1, array $paragraphs, array $bullets): string
    {
        $parts = ['<h2>'.e($h1).'</h2>'];
        foreach ($paragraphs as $p) {
            $parts[] = '<p>'.e($p).'</p>';
        }
        $parts[] = '<h3>'.e('Takeaways').'</h3><ul>';
        foreach ($bullets as $li) {
            $parts[] = '<li>'.e($li).'</li>';
        }
        $parts[] = '</ul>';
        $parts[] = '<p>'.e('Questions about how this applies to your stack? Reach out—we’re happy to compare notes.').'</p>';

        return implode("\n", $parts);
    }
}
