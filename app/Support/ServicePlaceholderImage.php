<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;

final class ServicePlaceholderImage
{
    public static function ensure(string $slug, string $title, ?string $categoryTitle = null, bool $force = false): ?string
    {
        $service = Service::query()->where('slug', $slug)->with('category:id,title,slug')->first();

        return ServiceHeroSvgGenerator::ensure(
            $slug,
            $title,
            $categoryTitle,
            $service?->category?->slug,
            self::descriptionFor($service),
            $force,
        );
    }

    public static function ensureForService(Service $service, bool $force = false): ?string
    {
        return ServiceHeroSvgGenerator::ensureForService($service, $force);
    }

    public static function ensureStoredPath(string $relativePath, string $seed, string $title, ?string $subtitle = null): bool
    {
        if (Storage::disk('public')->exists($relativePath)) {
            return true;
        }

        $absolute = storage_path('app/public/'.ltrim($relativePath, '/'));
        $ok = BrandedPlaceholderImage::ensureFile(
            $absolute,
            $seed,
            $title,
            BrandedPlaceholderImage::STYLE_CARD,
            $subtitle,
        );

        if ($ok) {
            WebpDerivative::encodeFromStoredPublicPath($relativePath);
        }

        return $ok;
    }

    private static function descriptionFor(?Service $service): ?string
    {
        if (! $service) {
            return null;
        }

        $service->loadMissing('category:id,title,slug');
        $fromContent = is_array($service->content) ? ($service->content['hero']['description'] ?? null) : null;
        if (is_string($fromContent) && trim($fromContent) !== '') {
            return $fromContent;
        }

        return $service->description;
    }
}
