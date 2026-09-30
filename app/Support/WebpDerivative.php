<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Writes a sibling .webp next to originals on the public disk (GD).
 * Safe no-op when GD/WebP is unavailable or decoding fails.
 */
final class WebpDerivative
{
    public static function siblingPath(string $storedRelativePath): string
    {
        $storedRelativePath = str_replace('\\', '/', $storedRelativePath);
        $dir = dirname($storedRelativePath);
        $filename = pathinfo($storedRelativePath, PATHINFO_FILENAME);

        return ($dir === '.' ? '' : $dir.'/').$filename.'.webp';
    }

    public static function absoluteOriginal(string $storedRelativePath): string
    {
        return storage_path('app/public/'.ltrim($storedRelativePath, '/'));
    }

    public static function absoluteWebp(string $storedRelativePath): string
    {
        return storage_path('app/public/'.ltrim(self::siblingPath($storedRelativePath), '/'));
    }

    public static function encodeFromStoredPublicPath(?string $storedRelativePath): void
    {
        if ($storedRelativePath === null || $storedRelativePath === '') {
            return;
        }

        if (! function_exists('imagewebp')) {
            return;
        }

        $original = self::absoluteOriginal($storedRelativePath);
        if (! is_file($original)) {
            return;
        }

        $ext = Str::lower(pathinfo($original, PATHINFO_EXTENSION));
        if ($ext === 'webp') {
            return;
        }

        $binary = @file_get_contents($original);
        if ($binary === false) {
            return;
        }

        $image = @imagecreatefromstring($binary);
        if ($image === false) {
            return;
        }

        $webpPath = self::absoluteWebp($storedRelativePath);
        $dir = dirname($webpPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagewebp($image, $webpPath, 82);
        imagedestroy($image);
    }

    public static function deleteForOriginal(?string $storedRelativePath): void
    {
        if ($storedRelativePath === null || $storedRelativePath === '') {
            return;
        }

        $webp = self::absoluteWebp($storedRelativePath);
        if (is_file($webp)) {
            @unlink($webp);
        }
    }

    public static function publicWebpUrlIfExists(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null || $storedRelativePath === '') {
            return null;
        }

        if (! is_file(self::absoluteWebp($storedRelativePath))) {
            return null;
        }

        return Storage::disk('public')->url(self::siblingPath($storedRelativePath));
    }

    /**
     * Root-relative URL under /storage for use in img[src]. Avoids broken images when
     * APP_URL host (e.g. localhost) does not match how the app is opened (e.g. 127.0.0.1).
     */
    public static function publicDiskRelativeUrl(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null) {
            return null;
        }
        $trimmed = trim($storedRelativePath);
        if ($trimmed === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', ltrim($trimmed, '/'));

        return '/storage/'.$normalized;
    }

    public static function publicWebpRelativeUrlIfExists(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null || $storedRelativePath === '') {
            return null;
        }

        if (! is_file(self::absoluteWebp($storedRelativePath))) {
            return null;
        }

        return self::publicDiskRelativeUrl(self::siblingPath($storedRelativePath));
    }
}
