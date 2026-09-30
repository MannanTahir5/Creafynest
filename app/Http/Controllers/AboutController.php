<?php

namespace App\Http\Controllers;

use App\Models\AboutPageSection;
use App\Models\AboutPillar;
use App\Models\AboutValueItem;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use Inertia\Inertia;
use Inertia\Response;

class AboutController
{
    public function __invoke(): Response
    {
        $section = AboutPageSection::query()
            ->where('is_active', true)
            ->with([
                'pillars' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'valueItems' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ])
            ->orderBy('id')
            ->first();

        return Inertia::render('About', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::ABOUT,
                'about',
                'About',
                'Creafynest builds maintainable Laravel, Vue, and Inertia products—clear UX, pragmatic delivery, and partnerships that last beyond launch.',
            ),
            'aboutPage' => $section ? [
                'hero_eyebrow' => $section->hero_eyebrow,
                'hero_heading_lead' => $section->hero_heading_lead,
                'hero_heading_accent' => $section->hero_heading_accent,
                'hero_description' => $section->hero_description,
                'hero_primary_label' => $section->hero_primary_label,
                'hero_primary_href' => $section->hero_primary_href,
                'hero_secondary_label' => $section->hero_secondary_label,
                'hero_secondary_href' => $section->hero_secondary_href,
                'hero_tertiary_label' => $section->hero_tertiary_label,
                'hero_tertiary_href' => $section->hero_tertiary_href,

                'panel_eyebrow' => $section->panel_eyebrow,
                'panel_description' => $section->panel_description,
                'panel_node_one_label' => $section->panel_node_one_label,
                'panel_node_two_label' => $section->panel_node_two_label,
                'panel_node_three_label' => $section->panel_node_three_label,
                'panel_center_label' => $section->panel_center_label,
                'panel_footer_label' => $section->panel_footer_label,

                'who_heading' => $section->who_heading,
                'who_description' => $section->who_description,

                'why_heading' => $section->why_heading,
                'why_description' => $section->why_description,

                'cta_heading' => $section->cta_heading,
                'cta_description' => $section->cta_description,
                'cta_button_label' => $section->cta_button_label,
                'cta_button_href' => $section->cta_button_href,
                'cta_is_active' => (bool) $section->cta_is_active,

                'pillars' => $section->pillars->map(fn (AboutPillar $p) => [
                    'title' => $p->title,
                    'description' => $p->description,
                    'icon_class' => $p->icon_class,
                ])->values(),

                'value_items' => $section->valueItems->map(fn (AboutValueItem $v) => [
                    'title' => $v->title,
                    'description' => $v->description,
                    'icon' => $v->icon,
                ])->values(),
            ] : null,
        ]);
    }
}
