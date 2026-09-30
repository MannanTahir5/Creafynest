<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use App\Support\ImageRules;
use App\Support\WebSettingsData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FooterSettingController extends Controller
{
    private const TABS = ['branding', 'column1', 'columns', 'contact', 'legal', 'seo'];

    public function edit(Request $request): Response
    {
        $row = FooterSetting::query()->first();
        if ($row === null) {
            $site = (string) config('app.name', 'Azee');
            $row = FooterSetting::query()->create([
                ...FooterSetting::defaultScalars($site),
                'payload' => FooterSetting::defaultPayload(),
            ]);
            FooterSetting::forgetCache();
        }

        $simple = FooterSetting::payloadForEditForm($row->payload);

        return Inertia::render('Admin/Footer/Edit', [
            'activeTab' => $this->normalizeTab($request->query('tab', 'branding')),
            'tabs' => $this->tabDefinitions(),
            'footer' => [
                'logo_path' => $row->logo_path,
                'logo_url' => WebSettingsData::footerLogoUrl($row),
                'logo_alt' => $row->logo_alt,
                'home_aria_label' => $row->home_aria_label,
                'resources_heading' => $row->resources_heading,
                'contact_heading' => $row->contact_heading,
                'studio_label' => $row->studio_label,
                'studio_text' => $row->studio_text,
                'contact_email' => $row->contact_email,
                'connect_heading' => $row->connect_heading,
                'copyright_entity' => $row->copyright_entity,
                'organization_description' => $row->organization_description,
                'company_links' => $simple['company_links'],
                'resource_links' => $simple['resource_links'],
                'columns' => $simple['columns'],
                'socials' => $simple['socials'],
                'legal_links' => $simple['legal_links'],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'active_tab' => ['nullable', 'string', 'in:'.implode(',', self::TABS)],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'logo_alt' => ['nullable', 'string', 'max:160'],
            'home_aria_label' => ['nullable', 'string', 'max:120'],
            'resources_heading' => ['nullable', 'string', 'max:80'],
            'contact_heading' => ['nullable', 'string', 'max:80'],
            'studio_label' => ['nullable', 'string', 'max:80'],
            'studio_text' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'string', 'max:160'],
            'connect_heading' => ['nullable', 'string', 'max:80'],
            'copyright_entity' => ['nullable', 'string', 'max:120'],
            'organization_description' => ['nullable', 'string', 'max:2000'],
            'footer_logo' => ImageRules::lenient(5120),
            'footer_logo_remove' => ['sometimes', 'boolean'],

            'company_links' => ['present', 'array'],
            'company_links.*.label' => ['nullable', 'string', 'max:120'],
            'company_links.*.href' => ['nullable', 'string', 'max:500'],
            'company_links.*.icon' => ['nullable', 'string', 'max:60'],

            'resource_links' => ['present', 'array'],
            'resource_links.*.label' => ['nullable', 'string', 'max:120'],
            'resource_links.*.href' => ['nullable', 'string', 'max:500'],
            'resource_links.*.external' => ['sometimes', 'boolean'],
            'resource_links.*.icon' => ['nullable', 'string', 'max:60'],

            'columns' => ['required', 'array'],
            'columns.*.heading' => ['nullable', 'string', 'max:120'],
            'columns.*.section_icon' => ['nullable', 'string', 'max:60'],
            'columns.*.links' => ['present', 'array'],
            'columns.*.links.*.label' => ['nullable', 'string', 'max:120'],
            'columns.*.links.*.href' => ['nullable', 'string', 'max:500'],
            'columns.*.links.*.icon' => ['nullable', 'string', 'max:60'],

            'socials' => ['present', 'array'],
            'socials.*.label' => ['nullable', 'string', 'max:80'],
            'socials.*.href' => ['nullable', 'string', 'max:500'],

            'legal_links' => ['present', 'array'],
            'legal_links.*.label' => ['nullable', 'string', 'max:120'],
            'legal_links.*.href' => ['nullable', 'string', 'max:500'],
            'legal_links.*.icon' => ['nullable', 'string', 'max:60'],
        ]);

        $activeTab = $this->normalizeTab($data['active_tab'] ?? 'branding');

        $payload = FooterSetting::assemblePayloadFromForm([
            'company_links' => $data['company_links'],
            'resource_links' => $data['resource_links'],
            'columns' => $data['columns'],
            'socials' => $data['socials'],
            'legal_links' => $data['legal_links'],
        ]);

        $row = FooterSetting::query()->firstOrNew(['id' => 1]);

        if (! empty($data['footer_logo_remove']) && $row->logo_path && str_starts_with((string) $row->logo_path, 'branding/')) {
            Storage::disk('public')->delete($row->logo_path);
            $row->logo_path = null;
        }
        if ($request->hasFile('footer_logo')) {
            if ($row->logo_path && str_starts_with((string) $row->logo_path, 'branding/')) {
                Storage::disk('public')->delete($row->logo_path);
            }
            $row->logo_path = $request->file('footer_logo')->store('branding', 'public');
        } elseif (filled($data['logo_path'] ?? null) && ! $request->hasFile('footer_logo')) {
            $row->logo_path = $data['logo_path'];
        }

        $row->fill([
            'logo_alt' => $data['logo_alt'] ?? '',
            'home_aria_label' => $data['home_aria_label'] ?? '',
            'resources_heading' => $data['resources_heading'] ?? 'Resources',
            'contact_heading' => $data['contact_heading'] ?? 'Contact us',
            'studio_label' => $data['studio_label'] ?? 'Studio',
            'studio_text' => $data['studio_text'] ?? null,
            'contact_email' => $data['contact_email'] ?? '',
            'connect_heading' => $data['connect_heading'] ?? 'Connect with us',
            'copyright_entity' => $data['copyright_entity'] ?? '',
            'organization_description' => $data['organization_description'] ?? null,
            'payload' => $payload,
        ]);
        $row->save();

        FooterSetting::forgetCache();

        return redirect()->route('admin.footer.edit', ['tab' => $activeTab])->with('success', 'Footer saved.');
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    private function tabDefinitions(): array
    {
        return [
            ['key' => 'branding', 'label' => 'Logo & branding'],
            ['key' => 'column1', 'label' => 'Column 1'],
            ['key' => 'columns', 'label' => 'Link columns'],
            ['key' => 'contact', 'label' => 'Contact & social'],
            ['key' => 'legal', 'label' => 'Legal bar'],
            ['key' => 'seo', 'label' => 'SEO'],
        ];
    }

    private function normalizeTab(string $tab): string
    {
        return in_array($tab, self::TABS, true) ? $tab : 'branding';
    }
}
