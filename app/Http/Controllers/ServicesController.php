<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCategory;
use App\Models\Project;
use App\Models\Service;
use App\Support\DeliveryServiceLinker;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use Inertia\Inertia;
use Inertia\Response;

class ServicesController
{
    public function __invoke(): Response
    {
        $servicesByCategory = Service::query()
            ->where('is_active', true)
            ->whereNotNull('delivery_category_id')
            ->get(['id', 'delivery_category_id', 'title', 'slug'])
            ->groupBy('delivery_category_id');

        $allServicesBySlug = Service::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['id', 'delivery_category_id', 'title', 'slug'])
            ->keyBy('slug');

        $deliveryCategories = DeliveryCategory::query()
            ->where('is_active', true)
            ->with('items')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (DeliveryCategory $c) use ($servicesByCategory, $allServicesBySlug) {
                $inCategory = $servicesByCategory->get($c->id) ?? collect();

                return [
                    'slug' => $c->slug,
                    'title' => $c->title,
                    'subtitle' => $c->subtitle,
                    'featureIcon' => $c->feature_icon,
                    'image' => $c->imageUrl(),
                    'imageAlt' => $c->image_alt,
                    'items' => $c->items->map(function ($i) use ($inCategory, $allServicesBySlug) {
                        $service = DeliveryServiceLinker::resolveForItem($i, $allServicesBySlug, $inCategory);

                        return [
                            'id' => $i->id,
                            'title' => $i->title,
                            'description' => $i->description,
                            'icon' => $i->icon,
                            'image' => $i->imageUrl(),
                            'imageAlt' => $i->image_alt,
                            'service_slug' => $service?->slug,
                        ];
                    })->all(),
                ];
            })
            ->all();

        return Inertia::render('Services', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::SERVICES,
                'services',
                'Services',
                'Services offered for Laravel backends, Vue frontends, Inertia apps, and pragmatic delivery from discovery to launch.',
            ),
            'recentCaseStudies' => Project::recentCaseStudies(),
            'deliveryCategories' => $deliveryCategories,
        ]);
    }
}
