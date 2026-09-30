<?php

namespace App\Support;

use App\Models\ContactMessagingChannel;
use App\Models\ContactPageSection;
use App\Models\ContactSocialLink;

final class ContactPagePayload
{
    /**
     * @return array<string, mixed>
     */
    public static function forAdmin(ContactPageSection $section): array
    {
        $section->loadMissing(['messagingChannels', 'socialLinks']);

        return self::serialize($section, true);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function forPublic(?ContactPageSection $section): ?array
    {
        if ($section === null || ! $section->is_active) {
            return null;
        }

        $section->loadMissing([
            'messagingChannels' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'socialLinks' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        $data = self::serialize($section, false);
        unset($data['id'], $data['is_active']);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private static function serialize(ContactPageSection $section, bool $admin): array
    {
        $channels = $section->messagingChannels->map(fn (ContactMessagingChannel $c) => [
            'id' => $c->id,
            'name' => $c->name,
            'handle' => $c->handle,
            'icon_slug' => $c->icon_slug,
            'icon_bg' => $c->icon_bg,
            'qr_data' => $c->qr_data,
            'is_active' => (bool) $c->is_active,
        ]);

        if (! $admin) {
            $channels = $channels->filter(fn ($c) => $c['is_active'])->map(fn ($c) => collect($c)->except('is_active', 'id')->all())->values();
        }

        $socials = $section->socialLinks->map(fn (ContactSocialLink $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'href' => $s->href,
            'bg_class' => $s->bg_class,
            'is_active' => (bool) $s->is_active,
        ]);

        if (! $admin) {
            $socials = $socials->filter(fn ($s) => $s['is_active'])->map(fn ($s) => collect($s)->except('is_active', 'id')->all())->values();
        }

        return [
            'id' => $section->id,
            'hero_heading' => $section->hero_heading,
            'hero_subtitle' => $section->hero_subtitle,
            'methods_eyebrow' => $section->methods_eyebrow,
            'methods_heading' => $section->methods_heading,
            'methods_description' => $section->methods_description,
            'info_is_active' => (bool) $section->info_is_active,
            'info_heading' => $section->info_heading,
            'info_description' => $section->info_description,
            'contact_email' => $section->contact_email,
            'contact_phone' => $section->contact_phone,
            'contact_address' => $section->contact_address,
            'office_hours' => $section->office_hours,
            'form_is_active' => (bool) $section->form_is_active,
            'form_heading' => $section->form_heading,
            'form_subtitle' => $section->form_subtitle,
            'form_name_label' => $section->form_name_label,
            'form_email_label' => $section->form_email_label,
            'form_message_label' => $section->form_message_label,
            'form_submit_label' => $section->form_submit_label,
            'form_success_message' => $section->form_success_message,
            'form_message_max_length' => (int) ($section->form_message_max_length ?: 5000),
            'map_is_active' => (bool) $section->map_is_active,
            'map_heading' => $section->map_heading,
            'map_embed_url' => $section->map_embed_url,
            'map_address_label' => $section->map_address_label,
            'chat_card_heading' => $section->chat_card_heading,
            'chat_card_description' => $section->chat_card_description,
            'chat_card_button_label' => $section->chat_card_button_label,
            'chat_card_button_href' => $section->chat_card_button_href,
            'chat_card_is_active' => (bool) $section->chat_card_is_active,
            'chat_badge_label' => $section->chat_badge_label,
            'chat_team_name' => $section->chat_team_name,
            'chat_status_text' => $section->chat_status_text,
            'chat_footer_note' => $section->chat_footer_note,
            'chat_greeting' => $section->chat_greeting,
            'chat_input_placeholder' => $section->chat_input_placeholder,
            'chat_quick_replies' => $section->chat_quick_replies ?? [],
            'cta_heading' => $section->cta_heading,
            'cta_description' => $section->cta_description,
            'cta_button_label' => $section->cta_button_label,
            'cta_button_href' => $section->cta_button_href,
            'cta_is_active' => (bool) $section->cta_is_active,
            'cta_eyebrow' => $section->cta_eyebrow,
            'cta_secondary_label' => $section->cta_secondary_label,
            'cta_secondary_href' => $section->cta_secondary_href,
            'cta_response_note' => $section->cta_response_note,
            'cta_trusted_label' => $section->cta_trusted_label,
            'cta_quick_label' => $section->cta_quick_label,
            'social_heading' => $section->social_heading,
            'social_is_active' => (bool) $section->social_is_active,
            'newsletter_is_active' => (bool) $section->newsletter_is_active,
            'newsletter_heading' => $section->newsletter_heading,
            'newsletter_subtitle' => $section->newsletter_subtitle,
            'newsletter_placeholder' => $section->newsletter_placeholder,
            'newsletter_button_label' => $section->newsletter_button_label,
            'is_active' => (bool) $section->is_active,
            'messaging_channels' => $channels->all(),
            'social_links' => $socials->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function scalarAttributesFromValidated(array $data): array
    {
        $keys = [
            'hero_heading', 'hero_subtitle', 'methods_eyebrow', 'methods_heading', 'methods_description',
            'info_is_active', 'info_heading', 'info_description', 'contact_email', 'contact_phone', 'contact_address', 'office_hours',
            'form_is_active', 'form_heading', 'form_subtitle', 'form_name_label', 'form_email_label', 'form_message_label',
            'form_submit_label', 'form_success_message', 'form_message_max_length',
            'map_is_active', 'map_heading', 'map_embed_url', 'map_address_label',
            'chat_card_heading', 'chat_card_description', 'chat_card_button_label', 'chat_card_button_href', 'chat_card_is_active',
            'chat_badge_label', 'chat_team_name', 'chat_status_text', 'chat_footer_note', 'chat_greeting', 'chat_input_placeholder', 'chat_quick_replies',
            'cta_heading', 'cta_description', 'cta_button_label', 'cta_button_href', 'cta_is_active',
            'cta_eyebrow', 'cta_secondary_label', 'cta_secondary_href', 'cta_response_note', 'cta_trusted_label', 'cta_quick_label',
            'social_heading', 'social_is_active',
            'newsletter_is_active', 'newsletter_heading', 'newsletter_subtitle', 'newsletter_placeholder', 'newsletter_button_label',
            'is_active',
        ];

        $out = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $out[$key] = $data[$key];
            }
        }

        return $out;
    }
}
