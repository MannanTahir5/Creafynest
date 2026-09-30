<?php

namespace Database\Seeders;

use App\Support\ContentCache;
use App\Support\SiteAssets;
use Illuminate\Database\Seeder;

/**
 * Generates or publishes missing images for services, portfolio, home sections, blogs, and SEO.
 *
 * Run: php artisan db:seed --class=SiteImagesSeeder
 * Or:  php artisan site:ensure-images
 */
class SiteImagesSeeder extends Seeder
{
    public function run(): void
    {
        SiteAssets::ensureAll();
        ContentCache::forget();
    }
}
