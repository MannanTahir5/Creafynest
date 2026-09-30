<?php

namespace Database\Seeders;

use App\Models\DeliveryCategory;
use App\Support\ServicesCatalog;
use Illuminate\Database\Seeder;

/**
 * Upserts design delivery categories and menu line items from design-services-catalog.json.
 */
class DesignDeliveryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $baseOrder = (int) DeliveryCategory::query()->max('sort_order');

        foreach (ServicesCatalog::catalogCategories() as $offset => $payload) {
            $category = DeliveryCategory::query()->updateOrCreate(
                ['slug' => $payload['slug']],
                [
                    'title' => $payload['title'],
                    'subtitle' => $payload['subtitle'],
                    'feature_icon' => $payload['feature_icon'],
                    'sort_order' => $baseOrder + 1 + $offset,
                    'is_active' => true,
                ],
            );

            $category->items()->delete();

            foreach (array_values($payload['services'] ?? []) as $itemPosition => $item) {
                $category->items()->create([
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'icon' => $item['icon'],
                    'sort_order' => $itemPosition,
                ]);
            }
        }
    }
}
