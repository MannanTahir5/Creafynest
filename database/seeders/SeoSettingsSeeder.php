<?php

namespace Database\Seeders;

use App\Models\SeoPageSetting;
use App\Models\SeoSiteSetting;
use App\Support\SeoPageKey;
use Illuminate\Database\Seeder;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (SeoSiteSetting::query()->exists()) {
            return;
        }

        SeoSiteSetting::query()->create([
            'default_og_image_path' => null,
            'default_og_image_alt' => null,
            'twitter_site' => null,
            'twitter_creator' => null,
            'og_locale' => 'en_US',
            'default_robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
            'theme_color' => null,
            'enable_blog_search_action' => true,
        ]);

        $pages = [
            SeoPageKey::HOME => [
                'title_stem' => 'Home',
                'meta_description' => 'Explore featured projects, services, and writing from a Laravel and Vue-focused developer portfolio.',
            ],
            SeoPageKey::SERVICES => [
                'title_stem' => 'Services',
                'meta_description' => 'Services offered for Laravel backends, Vue frontends, Inertia apps, and pragmatic delivery from discovery to launch.',
            ],
            SeoPageKey::PORTFOLIO => [
                'title_stem' => 'Portfolio',
                'meta_description' => 'Browse case studies and shipped work. Filter by category to explore Laravel, Vue, and full-stack projects.',
            ],
            SeoPageKey::BLOG => [
                'title_stem' => 'Blog',
                'meta_description' => 'Notes on Laravel, Vue, Inertia, and shipping maintainable products. Search posts and browse by category.',
            ],
            SeoPageKey::ABOUT => [
                'title_stem' => 'About',
                'meta_description' => 'Creafynest builds maintainable Laravel, Vue, and Inertia products—clear UX, pragmatic delivery, and partnerships that last beyond launch.',
            ],
            SeoPageKey::CONTACT => [
                'title_stem' => 'Contact',
                'meta_description' => 'Reach out for collaborations, freelance work, or questions about Laravel, Vue, and Inertia projects.',
            ],
        ];

        foreach ($pages as $key => $attrs) {
            SeoPageSetting::query()->create(array_merge([
                'page_key' => $key,
                'meta_keywords' => null,
                'og_title' => null,
                'og_description' => null,
                'og_image_path' => null,
                'og_image_alt' => null,
                'canonical_url' => null,
                'noindex' => false,
                'robots' => null,
            ], $attrs));
        }

        SeoSiteSetting::forgetCache();
        foreach (SeoPageKey::all() as $k) {
            SeoPageSetting::forgetPageCache($k);
        }
    }
}
