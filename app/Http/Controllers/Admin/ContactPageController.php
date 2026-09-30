<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessagingChannel;
use App\Models\ContactPageSection;
use App\Models\ContactSocialLink;
use App\Support\AdminWebSettingsRedirect;
use App\Support\ContactPagePayload;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ContactPageController extends Controller
{
    private const TABS = ['hero', 'info', 'methods', 'form', 'map', 'cta', 'social', 'newsletter'];

    public function edit(Request $request): Response
    {
        $section = ContactPageSection::singleton()->load(['messagingChannels', 'socialLinks']);

        return Inertia::render('Admin/ContactPage/Edit', [
            'activeTab' => $this->normalizeTab($request->query('tab', 'hero')),
            'tabs' => $this->tabDefinitions(),
            'section' => ContactPagePayload::forAdmin($section),
            'previewUrl' => '/contact',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate($this->validationRules());
        $activeTab = $this->normalizeTab($data['active_tab'] ?? 'hero');

        $data['chat_quick_replies'] = collect($data['chat_quick_replies'] ?? [])
            ->map(fn ($s) => is_string($s) ? trim($s) : '')
            ->filter()
            ->values()
            ->all() ?: null;

        $section = ContactPageSection::singleton();

        DB::transaction(function () use ($section, $data) {
            $section->update(ContactPagePayload::scalarAttributesFromValidated($data));
            $this->syncChannels($section, $data['messaging_channels'] ?? []);
            $this->syncSocials($section, $data['social_links'] ?? []);
        });

        ContentCache::forget();

        if ($request->input('return_to') === 'web-settings') {
            return AdminWebSettingsRedirect::afterSave($request, 'contact', 'Contact page updated.', 'admin.contact-page.edit');
        }

        return redirect()->route('admin.contact-page.edit', ['tab' => $activeTab])->with('success', 'Contact page updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validationRules(): array
    {
        return [
            'active_tab' => ['nullable', 'string', 'in:'.implode(',', self::TABS)],
            'hero_heading' => ['nullable', 'string', 'max:160'],
            'hero_subtitle' => ['nullable', 'string', 'max:600'],
            'methods_eyebrow' => ['nullable', 'string', 'max:120'],
            'methods_heading' => ['nullable', 'string', 'max:160'],
            'methods_description' => ['nullable', 'string', 'max:600'],
            'info_is_active' => ['sometimes', 'boolean'],
            'info_heading' => ['nullable', 'string', 'max:160'],
            'info_description' => ['nullable', 'string', 'max:600'],
            'contact_email' => ['nullable', 'string', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:80'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'office_hours' => ['nullable', 'string', 'max:160'],
            'form_is_active' => ['sometimes', 'boolean'],
            'form_heading' => ['nullable', 'string', 'max:160'],
            'form_subtitle' => ['nullable', 'string', 'max:600'],
            'form_name_label' => ['nullable', 'string', 'max:80'],
            'form_email_label' => ['nullable', 'string', 'max:80'],
            'form_message_label' => ['nullable', 'string', 'max:80'],
            'form_submit_label' => ['nullable', 'string', 'max:80'],
            'form_success_message' => ['nullable', 'string', 'max:255'],
            'form_message_max_length' => ['nullable', 'integer', 'min:500', 'max:10000'],
            'map_is_active' => ['sometimes', 'boolean'],
            'map_heading' => ['nullable', 'string', 'max:160'],
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
            'map_address_label' => ['nullable', 'string', 'max:255'],
            'chat_card_heading' => ['nullable', 'string', 'max:160'],
            'chat_card_description' => ['nullable', 'string', 'max:600'],
            'chat_card_button_label' => ['nullable', 'string', 'max:80'],
            'chat_card_button_href' => ['nullable', 'string', 'max:255'],
            'chat_card_is_active' => ['sometimes', 'boolean'],
            'chat_badge_label' => ['nullable', 'string', 'max:80'],
            'chat_team_name' => ['nullable', 'string', 'max:120'],
            'chat_status_text' => ['nullable', 'string', 'max:120'],
            'chat_footer_note' => ['nullable', 'string', 'max:160'],
            'chat_greeting' => ['nullable', 'string', 'max:300'],
            'chat_input_placeholder' => ['nullable', 'string', 'max:80'],
            'chat_quick_replies' => ['nullable', 'array', 'max:8'],
            'chat_quick_replies.*' => ['nullable', 'string', 'max:80'],
            'cta_heading' => ['nullable', 'string', 'max:160'],
            'cta_description' => ['nullable', 'string', 'max:600'],
            'cta_button_label' => ['nullable', 'string', 'max:80'],
            'cta_button_href' => ['nullable', 'string', 'max:255'],
            'cta_is_active' => ['sometimes', 'boolean'],
            'cta_eyebrow' => ['nullable', 'string', 'max:120'],
            'cta_secondary_label' => ['nullable', 'string', 'max:80'],
            'cta_secondary_href' => ['nullable', 'string', 'max:255'],
            'cta_response_note' => ['nullable', 'string', 'max:160'],
            'cta_trusted_label' => ['nullable', 'string', 'max:80'],
            'cta_quick_label' => ['nullable', 'string', 'max:80'],
            'social_heading' => ['nullable', 'string', 'max:160'],
            'social_is_active' => ['sometimes', 'boolean'],
            'newsletter_is_active' => ['sometimes', 'boolean'],
            'newsletter_heading' => ['nullable', 'string', 'max:120'],
            'newsletter_subtitle' => ['nullable', 'string', 'max:255'],
            'newsletter_placeholder' => ['nullable', 'string', 'max:120'],
            'newsletter_button_label' => ['nullable', 'string', 'max:40'],
            'is_active' => ['sometimes', 'boolean'],
            'messaging_channels' => ['array', 'max:12'],
            'messaging_channels.*.id' => ['nullable', 'integer'],
            'messaging_channels.*.name' => ['required', 'string', 'max:120'],
            'messaging_channels.*.handle' => ['nullable', 'string', 'max:160'],
            'messaging_channels.*.icon_slug' => ['nullable', 'string', 'max:60'],
            'messaging_channels.*.icon_bg' => ['nullable', 'string', 'max:120'],
            'messaging_channels.*.qr_data' => ['nullable', 'string', 'max:500'],
            'messaging_channels.*.is_active' => ['sometimes', 'boolean'],
            'social_links' => ['array', 'max:24'],
            'social_links.*.id' => ['nullable', 'integer'],
            'social_links.*.name' => ['required', 'string', 'max:120'],
            'social_links.*.slug' => ['nullable', 'string', 'max:60'],
            'social_links.*.href' => ['nullable', 'string', 'max:255'],
            'social_links.*.bg_class' => ['nullable', 'string', 'max:160'],
            'social_links.*.is_active' => ['sometimes', 'boolean'],
        ];
    }

    private function syncChannels(ContactPageSection $section, array $rows): void
    {
        $kept = [];
        foreach (array_values($rows) as $i => $row) {
            $payload = [
                'name' => $row['name'],
                'handle' => $row['handle'] ?? null,
                'icon_slug' => $row['icon_slug'] ?? null,
                'icon_bg' => $row['icon_bg'] ?? null,
                'qr_data' => $row['qr_data'] ?? null,
                'sort_order' => $i,
                'is_active' => $row['is_active'] ?? true,
            ];
            if (! empty($row['id']) && ($existing = $section->messagingChannels()->whereKey($row['id'])->first())) {
                $existing->update($payload);
                $kept[] = $existing->id;
            } else {
                $kept[] = $section->messagingChannels()->create($payload)->id;
            }
        }
        $section->messagingChannels()->whereNotIn('id', $kept ?: [0])->delete();
    }

    private function syncSocials(ContactPageSection $section, array $rows): void
    {
        $kept = [];
        foreach (array_values($rows) as $i => $row) {
            $payload = [
                'name' => $row['name'],
                'slug' => $row['slug'] ?? null,
                'href' => $row['href'] ?? null,
                'bg_class' => $row['bg_class'] ?? null,
                'sort_order' => $i,
                'is_active' => $row['is_active'] ?? true,
            ];
            if (! empty($row['id']) && ($existing = $section->socialLinks()->whereKey($row['id'])->first())) {
                $existing->update($payload);
                $kept[] = $existing->id;
            } else {
                $kept[] = $section->socialLinks()->create($payload)->id;
            }
        }
        $section->socialLinks()->whereNotIn('id', $kept ?: [0])->delete();
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    private function tabDefinitions(): array
    {
        return [
            ['key' => 'hero', 'label' => 'Hero'],
            ['key' => 'methods', 'label' => 'Methods & chat'],
            ['key' => 'info', 'label' => 'Contact info'],
            ['key' => 'form', 'label' => 'Contact form'],
            ['key' => 'map', 'label' => 'Map'],
            ['key' => 'cta', 'label' => 'CTA banner'],
            ['key' => 'social', 'label' => 'Social'],
            ['key' => 'newsletter', 'label' => 'Newsletter'],
        ];
    }

    private function normalizeTab(string $tab): string
    {
        return in_array($tab, self::TABS, true) ? $tab : 'hero';
    }
}
