<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SeoSiteSetting extends Model
{
    public const CACHE_KEY = 'seo.site_settings.row';

    protected $fillable = [
        'default_og_image_path',
        'default_og_image_alt',
        'twitter_site',
        'twitter_creator',
        'og_locale',
        'default_robots',
        'theme_color',
        'enable_blog_search_action',
    ];

    protected function casts(): array
    {
        return [
            'enable_blog_search_action' => 'boolean',
        ];
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function cached(): ?self
    {
        return Cache::remember(self::CACHE_KEY, ContentCache::TTL, fn () => self::query()->first());
    }

    public function defaultOgImageUrl(): ?string
    {
        if (! $this->default_og_image_path || ! Storage::disk('public')->exists($this->default_og_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->default_og_image_path);
    }
}
