<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AboutPageSection extends Model
{
    protected $fillable = [
        'hero_eyebrow',
        'hero_heading_lead',
        'hero_heading_accent',
        'hero_description',
        'hero_primary_label',
        'hero_primary_href',
        'hero_secondary_label',
        'hero_secondary_href',
        'hero_tertiary_label',
        'hero_tertiary_href',

        'panel_eyebrow',
        'panel_description',
        'panel_node_one_label',
        'panel_node_two_label',
        'panel_node_three_label',
        'panel_center_label',
        'panel_footer_label',

        'who_heading',
        'who_description',

        'why_heading',
        'why_description',

        'cta_heading',
        'cta_description',
        'cta_button_label',
        'cta_button_href',
        'cta_is_active',

        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cta_is_active' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function pillars(): HasMany
    {
        return $this->hasMany(AboutPillar::class)->orderBy('sort_order');
    }

    public function valueItems(): HasMany
    {
        return $this->hasMany(AboutValueItem::class)->orderBy('sort_order');
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'hero_eyebrow' => 'About Creafynest',
            'hero_heading_lead' => 'We build software',
            'hero_heading_accent' => 'you can grow into',
            'hero_description' => 'Creafynest is a product-minded engineering studio. We help teams ship reliable web and mobile software—clear UX, maintainable code, and delivery you can plan around.',
            'hero_primary_label' => 'Get in touch',
            'hero_primary_href' => '/contact',
            'hero_secondary_label' => 'See case studies',
            'hero_secondary_href' => '/portfolio',
            'hero_tertiary_label' => 'View services',
            'hero_tertiary_href' => '/services',

            'panel_eyebrow' => 'How we work',
            'panel_description' => 'Strategy, engineering, and continuity in one studio—so launches stick and your team stays unblocked.',
            'panel_node_one_label' => 'Craft',
            'panel_node_two_label' => 'Strategy',
            'panel_node_three_label' => 'Engineering',
            'panel_center_label' => 'Creafynest',
            'panel_footer_label' => 'One studio · long arcs',

            'who_heading' => 'Who we are',
            'who_description' => 'We combine Laravel, Vue, and modern cloud tooling with pragmatic product thinking. Whether you need a customer-facing app, an internal platform, or AI features that fit your roadmap, we focus on outcomes: performance, security, and teams that can own what we build.',

            'why_heading' => 'Why work with us',
            'why_description' => 'We care about the boring parts—security headers, caching, migrations, and release hygiene—because that is what keeps products stable in production.',

            'cta_heading' => 'Ready to talk about your next build?',
            'cta_description' => 'Tell us about timelines, constraints, and what success looks like—we will respond with honest next steps.',
            'cta_button_label' => 'Contact Creafynest',
            'cta_button_href' => '/contact',
            'cta_is_active' => true,

            'is_active' => true,
        ]);
    }
}
