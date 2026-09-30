<?php

namespace App\Support;

/**
 * Centralized image upload validation rules.
 *
 * `lenient()` accepts all common modern image formats including AVIF / HEIC / HEIF / TIFF / ICO,
 * which Laravel's built-in `image` rule rejects by default. Use this for content-managed images
 * where the editor may upload whatever their phone or camera produced.
 */
final class ImageRules
{
    /**
     * Accepts virtually every common image format. Set `$maxKilobytes` to control the
     * file-size cap (default ~10MB).
     *
     * @return array<int, string>
     */
    public static function lenient(int $maxKilobytes = 10240, bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'extensions:jpg,jpeg,jfif,png,gif,bmp,webp,svg,svgz,avif,heic,heif,tif,tiff,ico,apng',
            'max:'.$maxKilobytes,
        ];
    }
}
