<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\IndustriesSection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IndustriesSectionController extends Controller
{
    public function edit(): Response
    {
        $section = IndustriesSection::singleton()->load('industries');

        return Inertia::render('Admin/HomeSections/Industries/Edit', [
            'section' => [
                'id' => $section->id,
                'eyebrow' => $section->eyebrow,
                'heading_lead' => $section->heading_lead,
                'heading_highlight' => $section->heading_highlight,
                'description' => $section->description,
                'is_active' => $section->is_active,
                'industries' => $section->industries->map(fn (Industry $i) => [
                    'id' => $i->id,
                    'title' => $i->title,
                    'icon' => $i->icon,
                    'image_alt' => $i->image_alt,
                    'image_url' => $i->imageUrl(),
                    'is_active' => $i->is_active,
                ])->all(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'];

        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading_lead' => ['nullable', 'string', 'max:120'],
            'heading_highlight' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],

            'industries' => ['array', 'max:60'],
            'industries.*.id' => ['nullable', 'integer'],
            'industries.*.title' => ['required', 'string', 'max:120'],
            'industries.*.icon' => ['nullable', 'string', 'max:60'],
            'industries.*.image_alt' => ['nullable', 'string', 'max:255'],
            'industries.*.is_active' => ['sometimes', 'boolean'],
            'industries.*.image_remove' => ['sometimes', 'boolean'],

            'industries_images' => ['array'],
            'industries_images.*' => $imageRules,
        ]);

        $section = IndustriesSection::singleton();

        DB::transaction(function () use ($request, $section, $data) {
            $section->update([
                'eyebrow' => $data['eyebrow'] ?? null,
                'heading_lead' => $data['heading_lead'] ?? null,
                'heading_highlight' => $data['heading_highlight'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $kept = [];
            $images = $request->file('industries_images') ?? [];

            foreach (array_values($data['industries'] ?? []) as $i => $item) {
                $payload = [
                    'title' => $item['title'],
                    'icon' => $item['icon'] ?? null,
                    'image_alt' => $item['image_alt'] ?? null,
                    'sort_order' => $i,
                    'is_active' => $item['is_active'] ?? true,
                ];

                if (! empty($item['id'])) {
                    $existing = $section->industries()->whereKey($item['id'])->first();
                    if ($existing) {
                        $existing->update($payload);
                        $this->handleImage($existing, $item, $images[$i] ?? null);
                        $kept[] = $existing->id;
                        continue;
                    }
                }
                $created = $section->industries()->create($payload);
                $this->handleImage($created, $item, $images[$i] ?? null);
                $kept[] = $created->id;
            }

            foreach ($section->industries()->whereNotIn('id', $kept ?: [0])->get() as $stale) {
                if ($stale->image_path) {
                    Storage::disk('public')->delete($stale->image_path);
                }
                $stale->delete();
            }
        });

        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'industries',
            'Industries section updated.',
            'admin.home-sections.industries.edit',
        );
    }

    private function handleImage(Industry $item, array $payload, $file): void
    {
        if (! empty($payload['image_remove']) && $item->image_path) {
            Storage::disk('public')->delete($item->image_path);
            $item->forceFill(['image_path' => null])->save();
        }

        if ($file) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $stored = $file->store('industries/' . $item->id, 'public');
            $item->forceFill(['image_path' => $stored])->save();
        }
    }
}
