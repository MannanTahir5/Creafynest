<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.row';

    protected $fillable = [
        'app_name',
        'app_url',
        'timezone',
        'locale',
        'ga4_measurement_id',
        'header_logo_path',
        'header_logo_alt',
        'favicon_path',
        'gtm_container_id',
        'meta_pixel_id',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'mail_from_address',
        'mail_from_name',
        'sitemap_enabled',
        'robots_extra_disallow',
    ];

    protected function casts(): array
    {
        return [
            'sitemap_enabled' => 'boolean',
            'smtp_port' => 'integer',
        ];
    }

    public function headerLogoUrl(): ?string
    {
        return \App\Support\PublicStorageUrl::url($this->header_logo_path);
    }

    public function faviconUrl(): ?string
    {
        return \App\Support\PublicStorageUrl::url($this->favicon_path);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function cached(): ?self
    {
        return Cache::remember(self::CACHE_KEY, ContentCache::TTL, fn () => self::query()->first());
    }
}
