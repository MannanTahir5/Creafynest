<?php

namespace App\Models;

use App\Support\ContentCache;
use App\Support\SeoPageKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SeoPageSetting extends Model
{
    public const CACHE_KEY_PREFIX = 'seo.page_settings.';

    protected $fillable = [
        'page_key',
        'title_stem',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_path',
        'og_image_alt',
        'canonical_url',
        'noindex',
        'robots',
    ];

    protected function casts(): array
    {
        return [
            'noindex' => 'boolean',
        ];
    }

    public static function forgetPageCache(?string $pageKey = null): void
    {
        if ($pageKey !== null) {
            Cache::forget(self::CACHE_KEY_PREFIX.$pageKey);

            return;
        }
        foreach (SeoPageKey::all() as $key) {
            Cache::forget(self::CACHE_KEY_PREFIX.$key);
        }
    }

    public static function cachedFor(string $pageKey): ?self
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX.$pageKey,
            ContentCache::TTL,
            fn () => self::query()->where('page_key', $pageKey)->first(),
        );
    }

    public function ogImageUrl(): ?string
    {
        if (! $this->og_image_path || ! Storage::disk('public')->exists($this->og_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->og_image_path);
    }
}
