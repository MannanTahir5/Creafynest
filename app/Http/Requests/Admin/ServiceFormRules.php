<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

/**
 * Shared validation rules for creating/updating {@see \App\Models\Service}.
 */
final class ServiceFormRules
{
    /**
     * @return array<string, mixed>
     */
    public static function validationRules(?int $ignoreServiceId): array
    {
        $slugRules = ['nullable', 'string', 'alpha_dash', 'max:160'];
        $uniqueSlug = Rule::unique('services', 'slug');
        if ($ignoreServiceId !== null) {
            $uniqueSlug = $uniqueSlug->ignore($ignoreServiceId);
        }
        $slugRules[] = $uniqueSlug;

        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,avif,gif', 'max:4096'];

        return [
            'delivery_category_id' => ['nullable', 'integer', 'exists:delivery_categories,id'],
            'slug' => $slugRules,
            'title' => ['required', 'string', 'max:160'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],

            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:180'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => $imageRules,
            'og_image_remove' => ['sometimes', 'boolean'],
            'noindex' => ['sometimes', 'boolean'],
            'canonical_url' => ['nullable', 'string', 'url', 'max:255'],
            'schema_type' => ['nullable', 'string', Rule::in(['', 'Service', 'ProfessionalService', 'WebSite', 'CreativeWork', 'Product'])],

            'hero_image' => $imageRules,
            'tech_image' => $imageRules,
            'outcomes_image' => $imageRules,
            'snapshot_image' => $imageRules,
            'hero_image_remove' => ['sometimes', 'boolean'],
            'tech_image_remove' => ['sometimes', 'boolean'],
            'outcomes_image_remove' => ['sometimes', 'boolean'],
            'snapshot_image_remove' => ['sometimes', 'boolean'],

            'content' => ['required', 'array'],

            'content.hero' => ['required', 'array'],
            'content.hero.eyebrow' => ['nullable', 'string', 'max:120'],
            'content.hero.heading_prefix' => ['required', 'string', 'max:160'],
            'content.hero.heading_highlight' => ['required', 'string', 'max:160'],
            'content.hero.subhead' => ['nullable', 'string', 'max:200'],
            'content.hero.description' => ['required', 'string', 'max:800'],
            'content.hero.primary_cta_label' => ['required', 'string', 'max:60'],
            'content.hero.primary_cta_href' => ['required', 'string', 'max:255'],
            'content.hero.secondary_cta_label' => ['nullable', 'string', 'max:60'],
            'content.hero.secondary_cta_href' => ['nullable', 'string', 'max:255'],
            'content.hero.image_alt' => ['nullable', 'string', 'max:255'],
            'content.hero.caption_eyebrow' => ['nullable', 'string', 'max:80'],
            'content.hero.caption_text' => ['nullable', 'string', 'max:200'],
            'content.hero.chips' => ['array', 'max:6'],
            'content.hero.chips.*.icon' => ['required_with:content.hero.chips.*.label', 'string', 'max:60'],
            'content.hero.chips.*.label' => ['required_with:content.hero.chips.*.icon', 'string', 'max:60'],

            'content.tech' => ['required', 'array'],
            'content.tech.eyebrow' => ['nullable', 'string', 'max:80'],
            'content.tech.heading_prefix' => ['required', 'string', 'max:120'],
            'content.tech.heading_highlight' => ['nullable', 'string', 'max:120'],
            'content.tech.description' => ['required', 'string', 'max:600'],
            'content.tech.cta_label' => ['nullable', 'string', 'max:60'],
            'content.tech.cta_href' => ['nullable', 'string', 'max:255'],
            'content.tech.image_alt' => ['nullable', 'string', 'max:255'],
            'content.tech.items' => ['array', 'max:18'],
            'content.tech.items.*.name' => ['required', 'string', 'max:60'],
            'content.tech.items.*.slug' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9_.-]+$/'],

            'content.outcomes' => ['required', 'array'],
            'content.outcomes.eyebrow' => ['nullable', 'string', 'max:80'],
            'content.outcomes.heading' => ['required', 'string', 'max:160'],
            'content.outcomes.description' => ['required', 'string', 'max:600'],
            'content.outcomes.cta_label' => ['nullable', 'string', 'max:60'],
            'content.outcomes.cta_href' => ['nullable', 'string', 'max:255'],
            'content.outcomes.image_alt' => ['nullable', 'string', 'max:255'],
            'content.outcomes.cards' => ['array', 'max:9'],
            'content.outcomes.cards.*.icon' => ['required', 'string', 'max:60'],
            'content.outcomes.cards.*.title' => ['required', 'string', 'max:120'],
            'content.outcomes.cards.*.description' => ['required', 'string', 'max:300'],

            'content.snapshot' => ['sometimes', 'array'],
            'content.snapshot.eyebrow' => ['nullable', 'string', 'max:80'],
            'content.snapshot.heading_prefix' => ['nullable', 'string', 'max:120'],
            'content.snapshot.heading_highlight' => ['nullable', 'string', 'max:80'],
            'content.snapshot.heading_suffix' => ['nullable', 'string', 'max:120'],
            'content.snapshot.paragraphs' => ['array', 'max:6'],
            'content.snapshot.paragraphs.*' => ['nullable', 'string', 'max:1200'],
            'content.snapshot.caption_title' => ['nullable', 'string', 'max:120'],
            'content.snapshot.caption_subtitle' => ['nullable', 'string', 'max:160'],
            'content.snapshot.image_alt' => ['nullable', 'string', 'max:255'],
        ];
    }
}
