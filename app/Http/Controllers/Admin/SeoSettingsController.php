<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPageSetting;
use App\Models\SeoSiteSetting;
use App\Support\ImageRules;
use App\Support\SeoPageKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SeoSettingsController extends Controller
{
    public function edit(): Response
    {
        if (! SeoSiteSetting::query()->exists()) {
            $this->callSeeder();
        }

        $site = SeoSiteSetting::query()->firstOrFail();

        $pages = [];
        foreach (SeoPageKey::all() as $key) {
            $pages[$key] = SeoPageSetting::query()->firstOrCreate(
                ['page_key' => $key],
                ['title_stem' => null, 'meta_description' => null],
            );
        }

        return Inertia::render('Admin/SeoSettings/Edit', [
            'definitions' => SeoPageKey::definitions(),
            'site' => $this->serializeSite($site),
            'pages' => collect($pages)->map(fn (SeoPageSetting $p) => $this->serializePage($p))->keyBy('page_key')->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeSite(SeoSiteSetting $s): array
    {
        return [
            'default_og_image_url' => $s->defaultOgImageUrl(),
            'default_og_image_alt' => $s->default_og_image_alt,
            'twitter_site' => $s->twitter_site,
            'twitter_creator' => $s->twitter_creator,
            'og_locale' => $s->og_locale,
            'default_robots' => $s->default_robots,
            'theme_color' => $s->theme_color,
            'enable_blog_search_action' => (bool) $s->enable_blog_search_action,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializePage(SeoPageSetting $p): array
    {
        return [
            'page_key' => $p->page_key,
            'title_stem' => $p->title_stem,
            'meta_description' => $p->meta_description,
            'meta_keywords' => $p->meta_keywords,
            'og_title' => $p->og_title,
            'og_description' => $p->og_description,
            'og_image_url' => $p->ogImageUrl(),
            'og_image_alt' => $p->og_image_alt,
            'canonical_url' => $p->canonical_url,
            'noindex' => (bool) $p->noindex,
            'robots' => $p->robots,
        ];
    }

    public function update(Request $request): RedirectResponse
    {
        $pagesIn = $request->input('pages', []);
        if (is_array($pagesIn)) {
            foreach (SeoPageKey::all() as $key) {
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
            'site.twitter_site' => ['nullable', 'string', 'max:100'],
            'site.twitter_creator' => ['nullable', 'string', 'max:100'],
            'site.og_locale' => ['nullable', 'string', 'max:32'],
            'site.default_robots' => ['nullable', 'string', 'max:255'],
            'site.theme_color' => ['nullable', 'string', 'max:32'],
            'site.enable_blog_search_action' => ['sometimes', 'boolean'],
            'default_og_image' => ImageRules::lenient(),
            'default_og_image_remove' => ['sometimes', 'boolean'],
            'pages' => ['required', 'array'],
            'page_og_images' => ['nullable', 'array'],
            'page_og_remove' => ['nullable', 'array'],
        ];

        foreach (SeoPageKey::all() as $key) {
            $rules["pages.$key"] = ['nullable', 'array'];
            $rules["pages.$key.title_stem"] = ['nullable', 'string', 'max:190'];
            $rules["pages.$key.meta_description"] = ['nullable', 'string', 'max:500'];
            $rules["pages.$key.meta_keywords"] = ['nullable', 'string', 'max:255'];
            $rules["pages.$key.og_title"] = ['nullable', 'string', 'max:190'];
            $rules["pages.$key.og_description"] = ['nullable', 'string', 'max:500'];
            $rules["pages.$key.og_image_alt"] = ['nullable', 'string', 'max:255'];
            $rules["pages.$key.canonical_url"] = ['nullable', 'string', 'url', 'max:500'];
            $rules["pages.$key.noindex"] = ['sometimes', 'boolean'];
            $rules["pages.$key.robots"] = ['nullable', 'string', 'max:255'];
            $rules["page_og_images.$key"] = ImageRules::lenient();
            $rules["page_og_remove.$key"] = ['sometimes', 'boolean'];
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
            'twitter_site' => $siteRow['twitter_site'] ?? null,
            'twitter_creator' => $siteRow['twitter_creator'] ?? null,
            'og_locale' => $siteRow['og_locale'] ?: 'en_US',
            'default_robots' => $siteRow['default_robots'] ?? null,
            'theme_color' => $siteRow['theme_color'] ?? null,
            'enable_blog_search_action' => (bool) ($siteRow['enable_blog_search_action'] ?? false),
        ]);
        $site->save();

        $pagePayload = $data['pages'] ?? [];
        $pageOgRemove = $data['page_og_remove'] ?? [];

        foreach (SeoPageKey::all() as $key) {
            $p = $pagePayload[$key] ?? [];
            $row = SeoPageSetting::query()->firstOrCreate(['page_key' => $key]);

            if (! empty($pageOgRemove[$key]) && $row->og_image_path) {
                Storage::disk('public')->delete($row->og_image_path);
                $row->og_image_path = null;
            }

            if ($request->hasFile("page_og_images.$key")) {
                if ($row->og_image_path) {
                    Storage::disk('public')->delete($row->og_image_path);
                }
                $row->og_image_path = $request->file("page_og_images.$key")->store('seo/pages/'.$key, 'public');
            }

            $row->fill([
                'title_stem' => $p['title_stem'] ?? null,
                'meta_description' => $p['meta_description'] ?? null,
                'meta_keywords' => $p['meta_keywords'] ?? null,
                'og_title' => $p['og_title'] ?? null,
                'og_description' => $p['og_description'] ?? null,
                'og_image_alt' => $p['og_image_alt'] ?? null,
                'canonical_url' => $p['canonical_url'] ?? null,
                'noindex' => (bool) ($p['noindex'] ?? false),
                'robots' => $p['robots'] ?? null,
            ]);
            $row->save();
        }

        SeoSiteSetting::forgetCache();
        foreach (SeoPageKey::all() as $k) {
            SeoPageSetting::forgetPageCache($k);
        }

        return redirect()->route('admin.seo-settings.edit')->with('success', 'SEO settings saved.');
    }

    private function callSeeder(): void
    {
        (new \Database\Seeders\SeoSettingsSeeder)->run();
    }
}
