<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPageSection;
use App\Models\AboutPillar;
use App\Models\AboutValueItem;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AboutPageController extends Controller
{
    public function edit(): Response
    {
        $section = AboutPageSection::singleton()->load(['pillars', 'valueItems']);

        return Inertia::render('Admin/AboutPage/Edit', [
            'section' => [
                'id' => $section->id,

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

                'is_active' => (bool) $section->is_active,

                'pillars' => $section->pillars->map(fn (AboutPillar $p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'description' => $p->description,
                    'icon_class' => $p->icon_class,
                    'is_active' => (bool) $p->is_active,
                ])->all(),

                'value_items' => $section->valueItems->map(fn (AboutValueItem $v) => [
                    'id' => $v->id,
                    'title' => $v->title,
                    'description' => $v->description,
                    'icon' => $v->icon,
                    'is_active' => (bool) $v->is_active,
                ])->all(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_eyebrow' => ['nullable', 'string', 'max:120'],
            'hero_heading_lead' => ['nullable', 'string', 'max:160'],
            'hero_heading_accent' => ['nullable', 'string', 'max:160'],
            'hero_description' => ['nullable', 'string', 'max:1200'],
            'hero_primary_label' => ['nullable', 'string', 'max:80'],
            'hero_primary_href' => ['nullable', 'string', 'max:255'],
            'hero_secondary_label' => ['nullable', 'string', 'max:80'],
            'hero_secondary_href' => ['nullable', 'string', 'max:255'],
            'hero_tertiary_label' => ['nullable', 'string', 'max:80'],
            'hero_tertiary_href' => ['nullable', 'string', 'max:255'],

            'panel_eyebrow' => ['nullable', 'string', 'max:120'],
            'panel_description' => ['nullable', 'string', 'max:600'],
            'panel_node_one_label' => ['nullable', 'string', 'max:60'],
            'panel_node_two_label' => ['nullable', 'string', 'max:60'],
            'panel_node_three_label' => ['nullable', 'string', 'max:60'],
            'panel_center_label' => ['nullable', 'string', 'max:60'],
            'panel_footer_label' => ['nullable', 'string', 'max:120'],

            'who_heading' => ['nullable', 'string', 'max:160'],
            'who_description' => ['nullable', 'string', 'max:1200'],

            'why_heading' => ['nullable', 'string', 'max:160'],
            'why_description' => ['nullable', 'string', 'max:1200'],

            'cta_heading' => ['nullable', 'string', 'max:160'],
            'cta_description' => ['nullable', 'string', 'max:600'],
            'cta_button_label' => ['nullable', 'string', 'max:80'],
            'cta_button_href' => ['nullable', 'string', 'max:255'],
            'cta_is_active' => ['sometimes', 'boolean'],

            'is_active' => ['sometimes', 'boolean'],

            'pillars' => ['array', 'max:12'],
            'pillars.*.id' => ['nullable', 'integer'],
            'pillars.*.title' => ['required', 'string', 'max:160'],
            'pillars.*.description' => ['nullable', 'string', 'max:600'],
            'pillars.*.icon_class' => ['nullable', 'string', 'max:160'],
            'pillars.*.is_active' => ['sometimes', 'boolean'],

            'value_items' => ['array', 'max:12'],
            'value_items.*.id' => ['nullable', 'integer'],
            'value_items.*.title' => ['required', 'string', 'max:160'],
            'value_items.*.description' => ['nullable', 'string', 'max:600'],
            'value_items.*.icon' => ['nullable', 'string', 'max:60'],
            'value_items.*.is_active' => ['sometimes', 'boolean'],
        ]);

        $section = AboutPageSection::singleton();

        DB::transaction(function () use ($section, $data) {
            $section->update([
                'hero_eyebrow' => $data['hero_eyebrow'] ?? null,
                'hero_heading_lead' => $data['hero_heading_lead'] ?? null,
                'hero_heading_accent' => $data['hero_heading_accent'] ?? null,
                'hero_description' => $data['hero_description'] ?? null,
                'hero_primary_label' => $data['hero_primary_label'] ?? null,
                'hero_primary_href' => $data['hero_primary_href'] ?? null,
                'hero_secondary_label' => $data['hero_secondary_label'] ?? null,
                'hero_secondary_href' => $data['hero_secondary_href'] ?? null,
                'hero_tertiary_label' => $data['hero_tertiary_label'] ?? null,
                'hero_tertiary_href' => $data['hero_tertiary_href'] ?? null,

                'panel_eyebrow' => $data['panel_eyebrow'] ?? null,
                'panel_description' => $data['panel_description'] ?? null,
                'panel_node_one_label' => $data['panel_node_one_label'] ?? null,
                'panel_node_two_label' => $data['panel_node_two_label'] ?? null,
                'panel_node_three_label' => $data['panel_node_three_label'] ?? null,
                'panel_center_label' => $data['panel_center_label'] ?? null,
                'panel_footer_label' => $data['panel_footer_label'] ?? null,

                'who_heading' => $data['who_heading'] ?? null,
                'who_description' => $data['who_description'] ?? null,

                'why_heading' => $data['why_heading'] ?? null,
                'why_description' => $data['why_description'] ?? null,

                'cta_heading' => $data['cta_heading'] ?? null,
                'cta_description' => $data['cta_description'] ?? null,
                'cta_button_label' => $data['cta_button_label'] ?? null,
                'cta_button_href' => $data['cta_button_href'] ?? null,
                'cta_is_active' => $data['cta_is_active'] ?? true,

                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncPillars($section, $data['pillars'] ?? []);
            $this->syncValueItems($section, $data['value_items'] ?? []);
        });

        ContentCache::forget();

        return redirect()->route('admin.about-page.edit')
            ->with('success', 'About page updated.');
    }

    private function syncPillars(AboutPageSection $section, array $rows): void
    {
        $kept = [];

        foreach (array_values($rows) as $i => $row) {
            $payload = [
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'icon_class' => $row['icon_class'] ?? null,
                'sort_order' => $i,
                'is_active' => $row['is_active'] ?? true,
            ];

            if (! empty($row['id'])) {
                $existing = $section->pillars()->whereKey($row['id'])->first();
                if ($existing) {
                    $existing->update($payload);
                    $kept[] = $existing->id;
                    continue;
                }
            }
            $created = $section->pillars()->create($payload);
            $kept[] = $created->id;
        }

        $section->pillars()->whereNotIn('id', $kept ?: [0])->delete();
    }

    private function syncValueItems(AboutPageSection $section, array $rows): void
    {
        $kept = [];

        foreach (array_values($rows) as $i => $row) {
            $payload = [
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'icon' => $row['icon'] ?? null,
                'sort_order' => $i,
                'is_active' => $row['is_active'] ?? true,
            ];

            if (! empty($row['id'])) {
                $existing = $section->valueItems()->whereKey($row['id'])->first();
                if ($existing) {
                    $existing->update($payload);
                    $kept[] = $existing->id;
                    continue;
                }
            }
            $created = $section->valueItems()->create($payload);
            $kept[] = $created->id;
        }

        $section->valueItems()->whereNotIn('id', $kept ?: [0])->delete();
    }
}
