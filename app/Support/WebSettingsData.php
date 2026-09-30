<?php

namespace App\Support;

use App\Models\ContactMessagingChannel;
use App\Models\ContactPageSection;
use App\Models\ContactSocialLink;
use App\Models\FooterSetting;
use App\Models\SeoPageSetting;
use App\Models\SeoSiteSetting;
use App\Models\SiteSetting;

final class WebSettingsData
{
    public static function siteRow(): SiteSetting
    {
        $row = SiteSetting::query()->first();
        if ($row !== null) {
            return $row;
        }

        $row = SiteSetting::query()->create([]);
        SiteSetting::forgetCache();

        return $row;
    }

    /**
     * @return array<string, mixed>
     */
    public static function branding(): array
    {
        $site = self::siteRow();
        $footer = FooterSetting::query()->first();

        return [
            'app_name' => $site->app_name ?? '',
            'header_logo_url' => PublicStorageUrl::url($site->header_logo_path),
            'header_logo_alt' => $site->header_logo_alt ?? '',
            'footer_logo_url' => self::footerLogoUrl($footer),
            'footer_logo_alt' => $footer?->logo_alt ?? '',
            'favicon_url' => PublicStorageUrl::url($site->favicon_path),
            'theme_color' => SeoSiteSetting::query()->first()?->theme_color ?? '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function tracking(): array
    {
        $site = self::siteRow();

        return [
            'ga4_measurement_id' => $site->ga4_measurement_id ?? '',
            'gtm_container_id' => $site->gtm_container_id ?? '',
            'meta_pixel_id' => $site->meta_pixel_id ?? '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function smtp(): array
    {
        $site = self::siteRow();

        return [
            'smtp_host' => $site->smtp_host ?? '',
            'smtp_port' => $site->smtp_port ?? '',
            'smtp_username' => $site->smtp_username ?? '',
            'smtp_password' => '',
            'smtp_encryption' => $site->smtp_encryption ?? '',
            'mail_from_address' => $site->mail_from_address ?? '',
            'mail_from_name' => $site->mail_from_name ?? '',
            'has_password' => filled($site->smtp_password),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function sitemap(): array
    {
        $site = self::siteRow();

        return [
            'sitemap_enabled' => $site->sitemap_enabled ?? true,
            'robots_extra_disallow' => $site->robots_extra_disallow ?? '',
            'sitemap_url' => url('/sitemap.xml'),
            'robots_url' => url('/robots.txt'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function other(): array
    {
        $site = self::siteRow();

        return [
            'app_name' => $site->app_name ?? '',
            'app_url' => $site->app_url ?? '',
            'timezone' => $site->timezone ?? '',
            'locale' => $site->locale ?? '',
            'effective' => [
                'app_name' => (string) config('app.name'),
                'app_url' => (string) config('app.url'),
                'timezone' => (string) config('app.timezone'),
                'locale' => (string) config('app.locale'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function social(): array
    {
        $footer = FooterSetting::query()->first();
        $seo = SeoSiteSetting::query()->first();
        $simple = $footer ? FooterSetting::payloadForEditForm($footer->payload) : ['socials' => []];

        return [
            'socials' => $simple['socials'] ?? [],
            'twitter_site' => $seo?->twitter_site ?? '',
            'twitter_creator' => $seo?->twitter_creator ?? '',
            'connect_heading' => $footer?->connect_heading ?? 'Connect with us',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function contact(): array
    {
        $section = ContactPageSection::singleton()->load(['messagingChannels', 'socialLinks']);

        return [
            'hero_heading' => $section->hero_heading,
            'hero_subtitle' => $section->hero_subtitle,
            'methods_eyebrow' => $section->methods_eyebrow,
            'methods_heading' => $section->methods_heading,
            'methods_description' => $section->methods_description,
            'chat_card_heading' => $section->chat_card_heading,
            'chat_card_description' => $section->chat_card_description,
            'chat_card_button_label' => $section->chat_card_button_label,
            'chat_card_button_href' => $section->chat_card_button_href,
            'chat_card_is_active' => (bool) $section->chat_card_is_active,
            'cta_heading' => $section->cta_heading,
            'cta_description' => $section->cta_description,
            'cta_button_label' => $section->cta_button_label,
            'cta_button_href' => $section->cta_button_href,
            'cta_is_active' => (bool) $section->cta_is_active,
            'social_heading' => $section->social_heading,
            'social_is_active' => (bool) $section->social_is_active,
            'is_active' => (bool) $section->is_active,
            'messaging_channels' => $section->messagingChannels->map(fn (ContactMessagingChannel $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'handle' => $c->handle,
                'icon_slug' => $c->icon_slug,
                'icon_bg' => $c->icon_bg,
                'qr_data' => $c->qr_data,
                'is_active' => (bool) $c->is_active,
            ])->all(),
            'social_links' => $section->socialLinks->map(fn (ContactSocialLink $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'href' => $s->href,
                'bg_class' => $s->bg_class,
                'is_active' => (bool) $s->is_active,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function contactSeo(): array
    {
        $page = SeoPageSetting::query()->firstOrCreate(
            ['page_key' => 'contact'],
            ['title_stem' => null, 'meta_description' => null],
        );

        return [
            'page_key' => 'contact',
            'title_stem' => $page->title_stem,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'og_title' => $page->og_title,
            'og_description' => $page->og_description,
            'og_image_url' => $page->ogImageUrl(),
            'og_image_alt' => $page->og_image_alt,
            'canonical_url' => $page->canonical_url,
            'noindex' => (bool) $page->noindex,
            'robots' => $page->robots,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function footerLogoUrl(?FooterSetting $footer): ?string
    {
        if ($footer === null || ! filled($footer->logo_path)) {
            return null;
        }

        $path = trim((string) $footer->logo_path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $storageUrl = PublicStorageUrl::url($path);
        if ($storageUrl !== null) {
            return $storageUrl;
        }

        return '/'.ltrim($path, '/');
    }

    public static function seo(): array
    {
        if (! SeoSiteSetting::query()->exists()) {
            (new \Database\Seeders\SeoSettingsSeeder)->run();
        }

        $site = SeoSiteSetting::query()->firstOrFail();
        $pages = [];
        foreach (\App\Support\SeoPageKey::all() as $key) {
            $p = SeoPageSetting::query()->firstOrCreate(
                ['page_key' => $key],
                ['title_stem' => null, 'meta_description' => null],
            );
            $pages[$key] = [
                'page_key' => $p->page_key,
                'title_stem' => $p->title_stem,
                'meta_description' => $p->meta_description,
                'meta_keywords' => $p->meta_keywords,
                'noindex' => (bool) $p->noindex,
            ];
        }

        return [
            'definitions' => \App\Support\SeoPageKey::definitions(),
            'site' => [
                'default_og_image_url' => $site->defaultOgImageUrl(),
                'default_og_image_alt' => $site->default_og_image_alt,
                'og_locale' => $site->og_locale,
                'default_robots' => $site->default_robots,
                'theme_color' => $site->theme_color,
                'enable_blog_search_action' => (bool) $site->enable_blog_search_action,
            ],
            'pages' => $pages,
        ];
    }
}
