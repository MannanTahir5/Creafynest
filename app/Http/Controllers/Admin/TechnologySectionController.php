<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Models\TechnologySection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TechnologySectionController extends Controller
{
    public function edit(): Response
    {
        $section = TechnologySection::singleton();
        $section->load('technologies');

        return Inertia::render('Admin/TechnologySection/Edit', [
            'section' => [
                'id' => $section->id,
                'eyebrow' => $section->eyebrow,
                'heading_lead' => $section->heading_lead,
                'heading_highlight' => $section->heading_highlight,
                'description' => $section->description,
                'is_active' => $section->is_active,
                'technologies' => $section->technologies->map(fn (Technology $t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'icon_slug' => $t->icon_slug,
                    'image_alt' => $t->image_alt,
                    'image_url' => $t->imageUrl(),
                    'row_index' => $t->row_index,
                    'sort_order' => $t->sort_order,
                    'is_active' => $t->is_active,
                ])->all(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $section = TechnologySection::singleton();

        DB::transaction(function () use ($request, $section, $data) {
            $section->update([
                'eyebrow' => $data['eyebrow'] ?? null,
                'heading_lead' => $data['heading_lead'] ?? null,
                'heading_highlight' => $data['heading_highlight'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncTechnologies($request, $section, $data['technologies'] ?? []);
        });

        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'technologies',
            'Technology section updated.',
            'admin.technology-section.edit',
        );
    }

    /** @return array<string,mixed> */
    private function validated(Request $request): array
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'];

        return $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading_lead' => ['nullable', 'string', 'max:120'],
            'heading_highlight' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],

            'technologies' => ['array', 'max:80'],
            'technologies.*.id' => ['nullable', 'integer'],
            'technologies.*.name' => ['required', 'string', 'max:60'],
            'technologies.*.icon_slug' => ['nullable', 'string', 'max:80'],
            'technologies.*.image_alt' => ['nullable', 'string', 'max:255'],
            'technologies.*.row_index' => ['required', 'integer', 'in:1,2'],
            'technologies.*.is_active' => ['sometimes', 'boolean'],
            'technologies.*.image_remove' => ['sometimes', 'boolean'],

            // Per-tech images keyed by row position: technologies_images.0, .1, ...
            'technologies_images' => ['array'],
            'technologies_images.*' => $imageRules,
        ]);
    }

    /** @param array<int,array<string,mixed>> $technologies */
    private function syncTechnologies(Request $request, TechnologySection $section, array $technologies): void
    {
        $keptIds = [];
        $images = $request->file('technologies_images') ?? [];

        // Track per-row sort within each row_index so reordering in the form sticks.
        $rowCounters = [1 => 0, 2 => 0];

        foreach (array_values($technologies) as $position => $tech) {
            $rowIndex = (int) ($tech['row_index'] ?? 1);
            if (! isset($rowCounters[$rowIndex])) {
                $rowCounters[$rowIndex] = 0;
            }
            $sortOrder = $rowCounters[$rowIndex]++;

            $payload = [
                'name' => $tech['name'],
                'icon_slug' => $tech['icon_slug'] ?? null,
                'image_alt' => $tech['image_alt'] ?? null,
                'row_index' => $rowIndex,
                'sort_order' => $sortOrder,
                'is_active' => $tech['is_active'] ?? true,
            ];

            if (! empty($tech['id'])) {
                $existing = $section->technologies()->whereKey($tech['id'])->first();
                if ($existing) {
                    $existing->update($payload);
                    $this->handleTechImage($existing, $tech, $images[$position] ?? null);
                    $keptIds[] = $existing->id;
                    continue;
                }
            }

            $created = $section->technologies()->create($payload);
            $this->handleTechImage($created, $tech, $images[$position] ?? null);
            $keptIds[] = $created->id;
        }

        $stale = $section->technologies()->whereNotIn('id', $keptIds ?: [0])->get();
        foreach ($stale as $s) {
            if ($s->image_path) {
                Storage::disk('public')->delete($s->image_path);
            }
            $s->delete();
        }
    }

    private function handleTechImage(Technology $tech, array $payload, $uploadedFile): void
    {
        if (! empty($payload['image_remove']) && $tech->image_path) {
            Storage::disk('public')->delete($tech->image_path);
            $tech->forceFill(['image_path' => null])->save();
        }

        if ($uploadedFile) {
            if ($tech->image_path) {
                Storage::disk('public')->delete($tech->image_path);
            }
            $stored = $uploadedFile->store('technologies/' . $tech->id, 'public');
            $tech->forceFill(['image_path' => $stored])->save();
        }
    }
}
