<?php

namespace App\Support;

/**
 * @see BrandedPlaceholderImage
 */
final class CaseStudyPlaceholderImage
{
    public static function ensure(string $slug, string $title): void
    {
        $dir = public_path('images/case-studies');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir.DIRECTORY_SEPARATOR.$slug.'.jpg';

        BrandedPlaceholderImage::ensureFile(
            $path,
            $slug,
            $title,
            BrandedPlaceholderImage::STYLE_CASE_STUDY,
        );
    }
}
