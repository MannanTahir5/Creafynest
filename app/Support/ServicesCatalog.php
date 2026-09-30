<?php

namespace App\Support;

/**
 * Unified services catalog — design, AI, video, and development mega-menu data.
 */
final class ServicesCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function designCategories(): array
    {
        return self::loadFile('design-services-catalog.json')['categories'] ?? [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function aiCategories(): array
    {
        return self::loadFile('ai-services-catalog.json')['categories'] ?? [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function videoCategories(): array
    {
        return self::loadFile('video-services-catalog.json')['categories'] ?? [];
    }

    /**
     * All seeded catalog categories (design + AI + video).
     *
     * @return list<array<string, mixed>>
     */
    public static function catalogCategories(): array
    {
        return array_merge(
            self::designCategories(),
            self::aiCategories(),
            self::videoCategories(),
        );
    }

    /** @deprecated Use {@see catalogCategories()} */
    public static function categories(): array
    {
        return self::catalogCategories();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function developmentMegaMenuColumns(): array
    {
        return self::loadFile('design-services-catalog.json')['development_categories'] ?? [];
    }

    /**
     * @return array<string, string>
     */
    public static function deliveryTitleToSlugMap(): array
    {
        $map = [];

        foreach (self::catalogCategories() as $category) {
            foreach ($category['services'] ?? [] as $service) {
                $map[mb_strtolower(trim((string) $service['title']))] = (string) $service['slug'];
            }
        }

        return $map;
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadFile(string $filename): array
    {
        static $cache = [];

        if (isset($cache[$filename])) {
            return $cache[$filename];
        }

        $path = database_path('data/'.$filename);
        $json = (string) file_get_contents($path);
        $decoded = json_decode($json, true);

        $cache[$filename] = is_array($decoded) ? $decoded : [];

        return $cache[$filename];
    }
}
