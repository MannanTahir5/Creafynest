<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactPageSection extends Model
{
    protected $fillable = [
        'hero_heading', 'hero_subtitle',
        'methods_eyebrow', 'methods_heading', 'methods_description',
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

    protected function casts(): array
    {
        return [
            'info_is_active' => 'boolean',
            'form_is_active' => 'boolean',
            'map_is_active' => 'boolean',
            'chat_card_is_active' => 'boolean',
            'cta_is_active' => 'boolean',
            'social_is_active' => 'boolean',
            'newsletter_is_active' => 'boolean',
            'is_active' => 'boolean',
            'form_message_max_length' => 'integer',
            'chat_quick_replies' => 'array',
        ];
    }

    public function messagingChannels(): HasMany
    {
        return $this->hasMany(ContactMessagingChannel::class)->orderBy('sort_order');
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(ContactSocialLink::class)->orderBy('sort_order');
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], self::defaultAttributes());
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultAttributes(): array
    {
        return [
            'hero_heading' => 'Contact Creafynest',
            'hero_subtitle' => "Get in touch with our team — we'd love to hear about your project, timeline, and goals.",
            'methods_heading' => 'Choose Your Preferred Contact Method',
            'methods_description' => "We're here to help with your project, timeline, and goals.",
            'info_is_active' => false,
            'form_is_active' => false,
            'map_is_active' => false,
            'chat_card_heading' => 'Chat with Us Now',
            'chat_card_description' => 'Get instant answers to your questions through our live chat.',
            'chat_card_button_label' => 'Open Live Chat',
            'chat_card_button_href' => '#contact-channels',
            'chat_card_is_active' => true,
            'chat_badge_label' => 'Live Chat',
            'chat_team_name' => 'Creafynest Team',
            'chat_status_text' => 'Online · Replies fast',
            'chat_footer_note' => 'Fast response · Replies usually within 5 minutes',
            'chat_greeting' => 'Hi! How can we help with your project today?',
            'chat_input_placeholder' => 'Type a message…',
            'chat_quick_replies' => ['Get a quote', 'Book a call', 'WhatsApp', 'WeChat'],
            'cta_heading' => 'Ready to Get Started?',
            'cta_description' => 'Tell us about your project, timeline, and goals — our team will get back to you within one business day.',
            'cta_button_label' => 'Get Started',
            'cta_button_href' => '#contact-channels',
            'cta_is_active' => true,
            'cta_eyebrow' => "Let's build together",
            'cta_secondary_label' => 'Chat with us',
            'cta_secondary_href' => '#contact-channels',
            'cta_response_note' => 'Average response within 24 hours',
            'cta_trusted_label' => 'Trusted partner',
            'cta_quick_label' => 'Quick reply',
            'social_heading' => 'Follow us for the latest updates',
            'social_is_active' => true,
            'newsletter_is_active' => true,
            'newsletter_heading' => 'Newsletter',
            'newsletter_subtitle' => 'Get Global Updates. Subscribe Now.',
            'newsletter_placeholder' => 'Enter your email',
            'newsletter_button_label' => 'Submit',
            'is_active' => true,
        ];
    }
}
