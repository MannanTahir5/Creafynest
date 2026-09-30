<?php

namespace App\Support;

use App\Models\SeoPageSetting;
use App\Models\SeoSiteSetting;

/**
 * Builds {@see Seo::page()} payloads for public index routes using admin-managed {@see SeoPageSetting}.
 * Site-wide Twitter, locale, default OG image, and robots fallbacks are merged inside {@see Seo::page()}.
 */
final class PublicPageSeo
{
    /**
     * @param  array<string, mixed>  $extraOptions  Passed through to {@see Seo::page()} `$options`.
     * @return array<string, mixed>
     */
    public static function page(
        string $pageKey,
        string $path,
        string $fallbackTitleStem,
        string $fallbackDescription,
        string $ogType = 'website',
        array $extraOptions = [],
    ): array {
        $page = SeoPageSetting::cachedFor($pageKey);

        $titleStem = self::nonEmpty($page?->title_stem) ?? $fallbackTitleStem;
        $description = self::nonEmpty($page?->meta_description) ?? $fallbackDescription;

        $imageUrl = self::nonEmpty($page?->ogImageUrl());
        if ($imageUrl === null) {
            $site = SeoSiteSetting::cached();
            $imageUrl = self::nonEmpty($site?->defaultOgImageUrl());
        }

        $base = [
            'og_title' => self::nonEmpty($page?->og_title),
            'og_description' => self::nonEmpty($page?->og_description),
            'keywords' => self::nonEmpty($page?->meta_keywords),
            'canonical_override' => self::nonEmpty($page?->canonical_url),
            'noindex' => (bool) ($page?->noindex ?? false),
            'robots' => self::nonEmpty($page?->robots),
            'og_image_alt' => self::nonEmpty($page?->og_image_alt),
        ];

        $merged = array_merge($base, $extraOptions);

        return Seo::page($titleStem, $description, $path, $imageUrl, $ogType, $merged);
    }

    private static function nonEmpty(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : $t;
    }
}
