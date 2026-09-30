<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceCard;
use App\Models\AiServiceSection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AiServiceSectionController extends Controller
{
    public function edit(): Response
    {
        $section = AiServiceSection::singleton()->load('cards');

        return Inertia::render('Admin/HomeSections/AiServices/Edit', [
            'section' => [
                'id' => $section->id,
                'eyebrow' => $section->eyebrow,
                'heading' => $section->heading,
                'description' => $section->description,
                'is_active' => $section->is_active,
                'cards' => $section->cards->map(fn (AiServiceCard $c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'description' => $c->description,
                    'href' => $c->href,
                    'icon' => $c->icon,
                    'color_theme' => $c->color_theme,
                    'image_alt' => $c->image_alt,
                    'image_url' => $c->imageUrl(),
                    'is_active' => $c->is_active,
                ])->all(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'];

        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],

            'cards' => ['array', 'max:24'],
            'cards.*.id' => ['nullable', 'integer'],
            'cards.*.title' => ['required', 'string', 'max:160'],
            'cards.*.description' => ['nullable', 'string', 'max:800'],
            'cards.*.href' => ['nullable', 'string', 'max:255'],
            'cards.*.icon' => ['nullable', 'string', 'max:60'],
            'cards.*.color_theme' => ['nullable', 'string', 'max:24'],
            'cards.*.image_alt' => ['nullable', 'string', 'max:255'],
            'cards.*.is_active' => ['sometimes', 'boolean'],
            'cards.*.image_remove' => ['sometimes', 'boolean'],

            'cards_images' => ['array'],
            'cards_images.*' => $imageRules,
        ]);

        $section = AiServiceSection::singleton();

        DB::transaction(function () use ($request, $section, $data) {
            $section->update([
                'eyebrow' => $data['eyebrow'] ?? null,
                'heading' => $data['heading'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $kept = [];
            $images = $request->file('cards_images') ?? [];

            foreach (array_values($data['cards'] ?? []) as $i => $card) {
                $payload = [
                    'title' => $card['title'],
                    'description' => $card['description'] ?? null,
                    'href' => $card['href'] ?? null,
                    'icon' => $card['icon'] ?? null,
                    'color_theme' => $card['color_theme'] ?? 'amber',
                    'image_alt' => $card['image_alt'] ?? null,
                    'sort_order' => $i,
                    'is_active' => $card['is_active'] ?? true,
                ];

                if (! empty($card['id'])) {
                    $existing = $section->cards()->whereKey($card['id'])->first();
                    if ($existing) {
                        $existing->update($payload);
                        $this->handleImage($existing, $card, $images[$i] ?? null);
                        $kept[] = $existing->id;
                        continue;
                    }
                }
                $created = $section->cards()->create($payload);
                $this->handleImage($created, $card, $images[$i] ?? null);
                $kept[] = $created->id;
            }

            foreach ($section->cards()->whereNotIn('id', $kept ?: [0])->get() as $stale) {
                if ($stale->image_path) {
                    Storage::disk('public')->delete($stale->image_path);
                }
                $stale->delete();
            }
        });

        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'ai',
            'AI services section updated.',
            'admin.home-sections.ai-services.edit',
        );
    }

    private function handleImage(AiServiceCard $card, array $payload, $file): void
    {
        if (! empty($payload['image_remove']) && $card->image_path) {
            Storage::disk('public')->delete($card->image_path);
            $card->forceFill(['image_path' => null])->save();
        }

        if ($file) {
            if ($card->image_path) {
                Storage::disk('public')->delete($card->image_path);
            }
            $stored = $file->store('ai-service-cards/' . $card->id, 'public');
            $card->forceFill(['image_path' => $stored])->save();
        }
    }
}
