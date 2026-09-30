<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use App\Models\SeoPageSetting;
use App\Models\SeoSiteSetting;
use App\Models\SiteSetting;
use App\Support\AdminWebSettingsRedirect;
use App\Support\ContentCache;
use App\Support\ImageRules;
use App\Support\WebSettingsData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WebSettingsEditorController extends Controller
{
    private const TABS = [
        'branding',
        'contact',
        'contact-seo',
        'seo',
        'sitemap',
        'tracking',
        'smtp',
        'social',
        'other',
    ];

    public function edit(Request $request): Response
    {
        $tab = $this->normalizeTab($request->query('tab', 'branding'));

        return Inertia::render('Admin/WebSettings/Edit', [
            'activeTab' => $tab,
            'tabs' => $this->tabDefinitions(),
            'branding' => WebSettingsData::branding(),
            'contact' => WebSettingsData::contact(),
            'contactSeo' => WebSettingsData::contactSeo(),
            'seo' => WebSettingsData::seo(),
            'sitemap' => WebSettingsData::sitemap(),
            'tracking' => WebSettingsData::tracking(),
            'smtp' => WebSettingsData::smtp(),
            'social' => WebSettingsData::social(),
            'other' => WebSettingsData::other(),
        ]);
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:120'],
            'header_logo_alt' => ['nullable', 'string', 'max:160'],
            'footer_logo_alt' => ['nullable', 'string', 'max:160'],
            'theme_color' => ['nullable', 'string', 'max:32'],
            'header_logo' => ImageRules::lenient(5120),
            'header_logo_remove' => ['sometimes', 'boolean'],
            'footer_logo' => ImageRules::lenient(5120),
            'footer_logo_remove' => ['sometimes', 'boolean'],
            'favicon' => ImageRules::lenient(2048),
            'favicon_remove' => ['sometimes', 'boolean'],
        ]);

        $site = WebSettingsData::siteRow();

        if (! empty($data['header_logo_remove']) && $site->header_logo_path) {
            Storage::disk('public')->delete($site->header_logo_path);
            $site->header_logo_path = null;
        }
        if ($request->hasFile('header_logo')) {
            if ($site->header_logo_path) {
                Storage::disk('public')->delete($site->header_logo_path);
            }
            $site->header_logo_path = $request->file('header_logo')->store('branding', 'public');
        }

        if (! empty($data['favicon_remove']) && $site->favicon_path) {
            Storage::disk('public')->delete($site->favicon_path);
            $site->favicon_path = null;
        }
        if ($request->hasFile('favicon')) {
            if ($site->favicon_path) {
                Storage::disk('public')->delete($site->favicon_path);
            }
            $site->favicon_path = $request->file('favicon')->store('branding', 'public');
        }

        $site->fill([
            'app_name' => filled($data['app_name'] ?? null) ? trim($data['app_name']) : null,
            'header_logo_alt' => $data['header_logo_alt'] ?? null,
        ]);
        $site->save();
        SiteSetting::forgetCache();

        $footer = FooterSetting::query()->first();
        if ($footer === null) {
            $siteName = (string) config('app.name', 'Azee');
            $footer = FooterSetting::query()->create([
                ...FooterSetting::defaultScalars($siteName),
                'payload' => FooterSetting::defaultPayload(),
            ]);
        }

        if (! empty($data['footer_logo_remove']) && $footer->logo_path && str_starts_with($footer->logo_path, 'branding/')) {
            Storage::disk('public')->delete($footer->logo_path);
            $footer->logo_path = null;
        }
        if ($request->hasFile('footer_logo')) {
            if ($footer->logo_path && str_starts_with((string) $footer->logo_path, 'branding/')) {
                Storage::disk('public')->delete($footer->logo_path);
            }
            $footer->logo_path = $request->file('footer_logo')->store('branding', 'public');
        }

        $footer->logo_alt = $data['footer_logo_alt'] ?? $footer->logo_alt;
        $footer->save();
        FooterSetting::forgetCache();

        $seo = SeoSiteSetting::query()->first();
        if ($seo !== null) {
            $seo->theme_color = $data['theme_color'] ?? null;
            $seo->save();
            SeoSiteSetting::forgetCache();
        }

        ContentCache::forget();

        return AdminWebSettingsRedirect::afterSave($request, 'branding', 'Branding saved.', 'admin.web-settings.edit');
    }

    public function updateTracking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ga4_measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^G-[A-Z0-9]+$/'],
            'gtm_container_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^GTM-[A-Z0-9]+$/'],
            'meta_pixel_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^\d+$/'],
        ]);

        $site = WebSettingsData::siteRow();
        $site->fill([
            'ga4_measurement_id' => filled($data['ga4_measurement_id'] ?? null) ? trim($data['ga4_measurement_id']) : null,
            'gtm_container_id' => filled($data['gtm_container_id'] ?? null) ? trim($data['gtm_container_id']) : null,
            'meta_pixel_id' => filled($data['meta_pixel_id'] ?? null) ? trim($data['meta_pixel_id']) : null,
        ]);
        $site->save();
        SiteSetting::forgetCache();

        return AdminWebSettingsRedirect::afterSave($request, 'tracking', 'Tracking saved.', 'admin.web-settings.edit');
    }

    public function updateSmtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:500'],
            'smtp_encryption' => ['nullable', 'string', 'in:tls,ssl,'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
        ]);

        $site = WebSettingsData::siteRow();
        $site->smtp_host = filled($data['smtp_host'] ?? null) ? trim($data['smtp_host']) : null;
        $site->smtp_port = $data['smtp_port'] ?? null;
        $site->smtp_username = filled($data['smtp_username'] ?? null) ? trim($data['smtp_username']) : null;
        $site->smtp_encryption = filled($data['smtp_encryption'] ?? null) ? trim($data['smtp_encryption']) : null;
        $site->mail_from_address = filled($data['mail_from_address'] ?? null) ? trim($data['mail_from_address']) : null;
        $site->mail_from_name = filled($data['mail_from_name'] ?? null) ? trim($data['mail_from_name']) : null;

        if (filled($data['smtp_password'] ?? null)) {
            $site->smtp_password = Crypt::encryptString($data['smtp_password']);
        }

        $site->save();
        SiteSetting::forgetCache();

        return AdminWebSettingsRedirect::afterSave($request, 'smtp', 'SMTP settings saved.', 'admin.web-settings.edit');
    }

    public function updateSitemap(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sitemap_enabled' => ['sometimes', 'boolean'],
            'robots_extra_disallow' => ['nullable', 'string', 'max:2000'],
        ]);

        $site = WebSettingsData::siteRow();
        $site->sitemap_enabled = (bool) ($data['sitemap_enabled'] ?? true);
        $site->robots_extra_disallow = $data['robots_extra_disallow'] ?? null;
        $site->save();
        SiteSetting::forgetCache();
        ContentCache::forget();

        return AdminWebSettingsRedirect::afterSave($request, 'sitemap', 'Sitemap settings saved.', 'admin.web-settings.edit');
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'connect_heading' => ['nullable', 'string', 'max:80'],
            'twitter_site' => ['nullable', 'string', 'max:100'],
            'twitter_creator' => ['nullable', 'string', 'max:100'],
            'socials' => ['present', 'array'],
            'socials.*.label' => ['nullable', 'string', 'max:80'],
            'socials.*.href' => ['nullable', 'string', 'max:500'],
        ]);

        $footer = FooterSetting::query()->first();
        if ($footer === null) {
            $siteName = (string) config('app.name', 'Azee');
            $footer = FooterSetting::query()->create([
                ...FooterSetting::defaultScalars($siteName),
                'payload' => FooterSetting::defaultPayload(),
            ]);
        }

        $payload = FooterSetting::assemblePayloadFromForm([
            'company_links' => FooterSetting::payloadForEditForm($footer->payload)['company_links'] ?? [],
            'resource_links' => FooterSetting::payloadForEditForm($footer->payload)['resource_links'] ?? [],
            'columns' => FooterSetting::payloadForEditForm($footer->payload)['columns'] ?? [],
            'socials' => $data['socials'],
            'legal_links' => FooterSetting::payloadForEditForm($footer->payload)['legal_links'] ?? [],
        ]);

        $footer->connect_heading = $data['connect_heading'] ?? $footer->connect_heading;
        $footer->payload = $payload;
        $footer->save();
        FooterSetting::forgetCache();

        $seo = SeoSiteSetting::query()->first();
        if ($seo !== null) {
            $seo->twitter_site = $data['twitter_site'] ?? null;
            $seo->twitter_creator = $data['twitter_creator'] ?? null;
            $seo->save();
            SeoSiteSetting::forgetCache();
        }

        return AdminWebSettingsRedirect::afterSave($request, 'social', 'Social settings saved.', 'admin.web-settings.edit');
    }

    public function updateOther(Request $request): RedirectResponse
    {
        $nullableKeys = ['app_name', 'app_url', 'timezone', 'locale'];
        $trimmed = [];
        foreach ($nullableKeys as $key) {
            $v = $request->input($key);
            $trimmed[$key] = ($v === null || $v === '') ? null : (is_string($v) ? trim($v) : $v);
        }
        $request->merge($trimmed);

        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:120'],
            'app_url' => ['nullable', 'string', 'url', 'max:500'],
            'timezone' => ['nullable', 'timezone'],
            'locale' => ['nullable', 'string', 'max:16'],
        ]);

        $site = WebSettingsData::siteRow();
        $site->fill($data);
        $site->save();
        SiteSetting::forgetCache();
        ContentCache::forget();

        return AdminWebSettingsRedirect::afterSave($request, 'other', 'Settings saved.', 'admin.web-settings.edit');
    }

    public function updateContactSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title_stem' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:190'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image_alt' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'url', 'max:500'],
            'noindex' => ['sometimes', 'boolean'],
            'robots' => ['nullable', 'string', 'max:255'],
            'og_image' => ImageRules::lenient(),
            'og_image_remove' => ['sometimes', 'boolean'],
        ]);

        $row = SeoPageSetting::query()->firstOrCreate(['page_key' => 'contact']);

        if (! empty($data['og_image_remove']) && $row->og_image_path) {
            Storage::disk('public')->delete($row->og_image_path);
            $row->og_image_path = null;
        }
        if ($request->hasFile('og_image')) {
            if ($row->og_image_path) {
                Storage::disk('public')->delete($row->og_image_path);
            }
            $row->og_image_path = $request->file('og_image')->store('seo/pages/contact', 'public');
        }

        $row->fill([
            'title_stem' => $data['title_stem'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image_alt' => $data['og_image_alt'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'noindex' => (bool) ($data['noindex'] ?? false),
            'robots' => $data['robots'] ?? null,
        ]);
        $row->save();
        SeoPageSetting::forgetPageCache('contact');

        return AdminWebSettingsRedirect::afterSave($request, 'contact-seo', 'Contact SEO saved.', 'admin.web-settings.edit');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $pagesIn = $request->input('pages', []);
        if (is_array($pagesIn)) {
            foreach (\App\Support\SeoPageKey::all() as $key) {
                if (! isset($pagesIn[$key]) || ! is_array($pagesIn[$key])) {
                    continue;
                }
                $c = trim((string) ($pagesIn[$key]['canonical_url'] ?? ''));
                $pagesIn[$key]['canonical_url'] = $c === '' ? null : $c;
            }
            $request->merge(['pages' => $pagesIn]);
        }

        $rules = [
            'site' => ['required', 'array'],
            'site.default_og_image_alt' => ['nullable', 'string', 'max:255'],
            'site.og_locale' => ['nullable', 'string', 'max:32'],
            'site.default_robots' => ['nullable', 'string', 'max:255'],
            'site.theme_color' => ['nullable', 'string', 'max:32'],
            'site.enable_blog_search_action' => ['sometimes', 'boolean'],
            'default_og_image' => ImageRules::lenient(),
            'default_og_image_remove' => ['sometimes', 'boolean'],
            'pages' => ['required', 'array'],
        ];

        foreach (\App\Support\SeoPageKey::all() as $key) {
            $rules["pages.$key"] = ['nullable', 'array'];
            $rules["pages.$key.title_stem"] = ['nullable', 'string', 'max:190'];
            $rules["pages.$key.meta_description"] = ['nullable', 'string', 'max:500'];
            $rules["pages.$key.meta_keywords"] = ['nullable', 'string', 'max:255'];
            $rules["pages.$key.noindex"] = ['sometimes', 'boolean'];
        }

        $data = $request->validate($rules);

        $site = SeoSiteSetting::query()->firstOrFail();
        $siteRow = $data['site'];

        if (! empty($data['default_og_image_remove']) && $site->default_og_image_path) {
            Storage::disk('public')->delete($site->default_og_image_path);
            $site->default_og_image_path = null;
        }
        if ($request->hasFile('default_og_image')) {
            if ($site->default_og_image_path) {
                Storage::disk('public')->delete($site->default_og_image_path);
            }
            $site->default_og_image_path = $request->file('default_og_image')->store('seo', 'public');
        }

        $site->fill([
            'default_og_image_alt' => $siteRow['default_og_image_alt'] ?? null,
            'og_locale' => $siteRow['og_locale'] ?: 'en_US',
            'default_robots' => $siteRow['default_robots'] ?? null,
            'theme_color' => $siteRow['theme_color'] ?? null,
            'enable_blog_search_action' => (bool) ($siteRow['enable_blog_search_action'] ?? false),
        ]);
        $site->save();

        foreach (\App\Support\SeoPageKey::all() as $key) {
            $p = $data['pages'][$key] ?? [];
            $row = SeoPageSetting::query()->firstOrCreate(['page_key' => $key]);
            $row->fill([
                'title_stem' => $p['title_stem'] ?? null,
                'meta_description' => $p['meta_description'] ?? null,
                'meta_keywords' => $p['meta_keywords'] ?? null,
                'noindex' => (bool) ($p['noindex'] ?? false),
            ]);
            $row->save();
            SeoPageSetting::forgetPageCache($key);
        }

        SeoSiteSetting::forgetCache();

        return AdminWebSettingsRedirect::afterSave($request, 'seo', 'SEO settings saved.', 'admin.web-settings.edit');
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    private function tabDefinitions(): array
    {
        return [
            ['key' => 'branding', 'label' => 'Branding'],
            ['key' => 'contact', 'label' => 'Contact'],
            ['key' => 'contact-seo', 'label' => 'Contact SEO'],
            ['key' => 'seo', 'label' => 'SEO'],
            ['key' => 'sitemap', 'label' => 'Sitemap'],
            ['key' => 'tracking', 'label' => 'Tracking'],
            ['key' => 'smtp', 'label' => 'SMTP'],
            ['key' => 'social', 'label' => 'Social'],
            ['key' => 'other', 'label' => 'Other'],
        ];
    }

    private function normalizeTab(string $tab): string
    {
        return in_array($tab, self::TABS, true) ? $tab : 'branding';
    }
}
