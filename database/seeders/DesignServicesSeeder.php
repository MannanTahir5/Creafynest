<?php

namespace Database\Seeders;

use App\Models\DeliveryCategory;
use App\Models\Service;
use App\Support\ServicesCatalog;
use App\Support\ServiceContentNormalizer;
use App\Support\ServicePageContentFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds all design catalog services with detail-page content.
 *
 * Run: php artisan db:seed --class=DesignServicesSeeder
 */
class DesignServicesSeeder extends Seeder
{
    public function run(): void
    {
        $catBySlug = DeliveryCategory::query()
            ->whereIn('slug', collect(ServicesCatalog::catalogCategories())->pluck('slug'))
            ->pluck('id', 'slug');

        $position = (int) Service::query()->max('sort_order');

        foreach (ServicesCatalog::catalogCategories() as $category) {
            $catId = $catBySlug->get($category['slug']);

            if (! $catId) {
                continue;
            }

            foreach ($category['services'] ?? [] as $service) {
                $position++;
                $slug = (string) $service['slug'];
                $title = (string) $service['title'];
                $summary = (string) $service['description'];
                $icon = (string) ($service['icon'] ?? 'Sparkles');
                $categoryTitle = (string) $category['title'];

                $rawContent = ServicePageContentFactory::forCategorySlug(
                    (string) $category['slug'],
                    $title,
                    $categoryTitle,
                    $icon,
                    $summary,
                );

                $defaults = ServiceContentNormalizer::defaultStructure();
                $content = [
                    'hero' => array_replace($defaults['hero'], $rawContent['hero']),
                    'tech' => array_replace($defaults['tech'], $rawContent['tech']),
                    'outcomes' => array_replace($defaults['outcomes'], $rawContent['outcomes']),
                    'snapshot' => array_replace($defaults['snapshot'], $rawContent['snapshot']),
                ];

                $metaTitle = Str::limit("{$title} Services | {$categoryTitle}", 60, '');
                $metaDescription = Str::limit($summary, 160, '');

                Service::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'delivery_category_id' => $catId,
                        'title' => $title,
                        'description' => $summary,
                        'icon' => $icon,
                        'sort_order' => $position,
                        'is_active' => true,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => implode(', ', [
                            mb_strtolower($title),
                            mb_strtolower($categoryTitle),
                            'design services',
                            'creative agency',
                        ]),
                        'og_title' => $title,
                        'og_description' => $summary,
                        'noindex' => false,
                        'canonical_url' => null,
                        'schema_type' => 'Service',
                        'hero_image_path' => 'services/'.$slug.'/hero.jpg',
                        'tech_image_path' => null,
                        'outcomes_image_path' => null,
                        'snapshot_image_path' => null,
                        'og_image_path' => null,
                        'content' => $content,
                    ],
                );
            }
        }
    }
}
