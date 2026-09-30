<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\ServiceContentNormalizer;
use App\Support\Seo;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceDetailController
{
    public function __invoke(Service $service): Response
    {
        if (!$service->is_active) {
            abort(404);
        }

        $service->loadMissing('category:id,title,slug');
        $service->ensureStoredImages();

        $content = ServiceContentNormalizer::mergeWithDefaults((array) ($service->content ?? []));

        $title = $service->meta_title ?: $service->title;
        $description = $service->meta_description ?: ($content['hero']['description'] ?? '');
        $path = 'services/' . $service->slug;
        $ogImageUrl = $service->imageUrl('og_image_path') ?: $service->imageUrl('hero_image_path');

        $seo = Seo::page(
            $title,
            (string) Str::of($description)->limit(300, '')->trim(),
            $path,
            $ogImageUrl,
            'website',
            [
                'og_title' => $service->og_title,
                'og_description' => $service->og_description,
                'keywords' => $service->meta_keywords,
                'canonical_override' => $service->canonical_url,
                'noindex' => (bool) $service->noindex,
                'og_image_alt' => $service->title,
            ],
        );

        $canonicalUrl = $seo['canonical'];
        $base = rtrim((string) config('app.url'), '/');

        $schemas = [
            Seo::serviceSchema(
                $service->title,
                (string) Str::of($description)->limit(500, '')->trim(),
                $canonicalUrl,
                $ogImageUrl,
                $service->schema_type,
            ),
            Seo::breadcrumbListSchema([
                ['name' => 'Home', 'url' => $base.'/'],
                ['name' => 'Services', 'url' => $base.'/services'],
                ['name' => $service->title, 'url' => $canonicalUrl],
            ]),
        ];

        Seo::shareSchemas($schemas);

        return Inertia::render('Services/ServiceDetail', [
            'service' => [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'category' => $service->category ? [
                    'id' => $service->category->id,
                    'title' => $service->category->title,
                    'slug' => $service->category->slug,
                ] : null,
                'hero_image_url' => $service->imageUrl('hero_image_path'),
                'tech_image_url' => $service->imageUrl('tech_image_path'),
                'outcomes_image_url' => $service->imageUrl('outcomes_image_path'),
                'snapshot_image_url' => $service->imageUrl('snapshot_image_path'),
                'content' => $content,
            ],
            'seo' => $seo,
            'schemas' => $schemas,
        ]);
    }

}
