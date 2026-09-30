<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Root-relative /storage URLs that only resolve when the file exists on the public disk.
 */
final class PublicStorageUrl
{
    public const FALLBACK_SERVICE_HERO = '/images/fallbacks/service-hero.svg';

    public const FALLBACK_CARD = '/images/fallbacks/card.svg';

    public const FALLBACK_AVATAR = '/images/fallbacks/avatar.svg';

    public const FALLBACK_LOGO = '/images/fallbacks/logo.svg';

    /**
     * @return array{url: ?string, webp_url: ?string}
     */
    public static function imagePair(?string $storedRelativePath): array
    {
        $url = self::url($storedRelativePath);

        if ($url === null) {
            return ['url' => null, 'webp_url' => null];
        }

        return [
            'url' => $url,
            'webp_url' => self::webpUrl($storedRelativePath),
        ];
    }

    public static function url(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null || trim($storedRelativePath) === '') {
            return null;
        }

        if (! self::isWebAccessible($storedRelativePath)) {
            return null;
        }

        return WebpDerivative::publicDiskRelativeUrl($storedRelativePath);
    }

    /**
     * File must exist on the public disk and be reachable under public/storage (symlink or copy).
     */
    public static function isWebAccessible(string $storedRelativePath): bool
    {
        if (! Storage::disk('public')->exists($storedRelativePath)) {
            return false;
        }

        $normalized = str_replace('\\', '/', ltrim($storedRelativePath, '/'));
        $publicFile = public_path('storage/'.$normalized);

        return file_exists($publicFile) || Storage::disk('public')->exists($storedRelativePath);
    }

    public static function webpUrl(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null || trim($storedRelativePath) === '') {
            return null;
        }

        if (! self::isWebAccessible($storedRelativePath)) {
            return null;
        }

        return WebpDerivative::publicWebpRelativeUrlIfExists($storedRelativePath);
    }

    public static function urlOrFallback(?string $storedRelativePath, string $fallbackPublicPath): string
    {
        return self::url($storedRelativePath) ?? $fallbackPublicPath;
    }
}
