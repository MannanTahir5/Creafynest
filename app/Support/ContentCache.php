<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Short-lived caches for public lists. Cleared when admin mutates related content.
 */
final class ContentCache
{
    public const TTL = 3600;

    public const HOME = 'content.home';

    public const BLOG_CATEGORIES = 'content.blog.categories';

    public const PORTFOLIO_CATEGORIES = 'content.portfolio.categories';

    public const SITEMAP_XML = 'content.sitemap.xml';

    public const FOOTER = 'content.footer';

    public static function forget(): void
    {
        foreach ([
            self::HOME,
            self::BLOG_CATEGORIES,
            self::PORTFOLIO_CATEGORIES,
            self::SITEMAP_XML,
            self::FOOTER,
        ] as $key) {
            Cache::forget($key);
        }
    }

    public static function forgetFooter(): void
    {
        Cache::forget(self::FOOTER);
    }
}
