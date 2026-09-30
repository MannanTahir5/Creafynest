<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\HeroAvatar;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HeroController extends Controller
{
    /** Allowed gradient theme IDs (kept here so validation + UI stay in sync). */
    public const GRADIENTS = [
        'cyan-violet-fuchsia',
        'violet-indigo-blue',
        'amber-orange-rose',
        'emerald-teal-cyan',
        'rose-pink-fuchsia',
        'sky-indigo-violet',
        'slate-mono',
    ];

    public function index(): Response
    {
        $heroes = Hero::query()
            ->withCount('avatars')
            ->orderByDesc('is_active')
            ->latest('updated_at')
            ->paginate(20)
            ->through(fn (Hero $h) => array_merge($h->toArray(), [
                'image_url' => $h->imageUrl(),
                'avatars_count' => $h->avatars_count,
            ]));

        return Inertia::render('Admin/Heroes/Index', [
            'heroes' => $heroes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Heroes/Create', [
            'gradients' => self::GRADIENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $hero = Hero::query()->create($this->heroPayload($data));
            $this->handleImage($request, $hero);
            $this->syncAvatars($request, $hero, $data['avatars'] ?? []);
            if ($hero->is_active) {
                $hero->activate();
            }
        });

        ContentCache::forget();

        return redirect()->route('admin.heroes.index')->with('success', 'Hero created.');
    }

    public function edit(Hero $hero): Response
    {
        $hero->load('avatars');

        return Inertia::render('Admin/Heroes/Edit', [
            'gradients' => self::GRADIENTS,
            'hero' => array_merge($hero->toArray(), [
                'image_url' => $hero->imageUrl(),
                'avatars' => $hero->avatars->map(fn (HeroAvatar $a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'image_alt' => $a->image_alt,
                    'image_url' => $a->imageUrl(),
                    'is_active' => $a->is_active,
                ])->all(),
            ]),
        ]);
    }

    public function update(Request $request, Hero $hero): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $hero, $data) {
            $hero->update($this->heroPayload($data));
            $this->handleImage($request, $hero);
            $this->syncAvatars($request, $hero, $data['avatars'] ?? []);
            if ($hero->is_active) {
                $hero->activate();
            }
        });

        ContentCache::forget();

        if ($request->input('return_to') === 'home-page') {
            return AdminHomeEditorRedirect::afterSave(
                $request,
                'hero',
                'Hero updated.',
                'admin.heroes.index',
            );
        }

        return redirect()->route('admin.heroes.index')->with('success', 'Hero updated.');
    }

    public function destroy(Hero $hero): RedirectResponse
    {
        if ($hero->image_path) {
            Storage::disk('public')->delete($hero->image_path);
        }
        foreach ($hero->avatars as $avatar) {
            if ($avatar->image_path) {
                Storage::disk('public')->delete($avatar->image_path);
            }
        }

        $hero->delete();

        ContentCache::forget();

        return redirect()->route('admin.heroes.index')->with('success', 'Hero deleted.');
    }

    public function activate(Hero $hero): RedirectResponse
    {
        $hero->activate();

        ContentCache::forget();

        return redirect()->route('admin.heroes.index')->with('success', 'Hero activated.');
    }

    /** @param array<string,mixed> $data */
    private function heroPayload(array $data): array
    {
        $payload = Arr::only($data, [
            'eyebrow',
            'trust_count',
            'trust_text',
            'heading_line_one',
            'heading_line_two',
            'heading_gradient',
            'description',
            'image_alt',
            'primary_cta_label',
            'primary_cta_href',
            'secondary_cta_label',
            'secondary_cta_href',
            'is_active',
        ]);

        $lines = $data['feature_lines'] ?? [];
        $clean = array_values(array_filter(array_map(
            fn ($v) => is_string($v) ? trim($v) : null,
            is_array($lines) ? $lines : []
        )));
        $payload['feature_lines'] = $clean ?: null;

        return $payload;
    }

    private function handleImage(Request $request, Hero $hero): void
    {
        if ($request->boolean('image_remove') && $hero->image_path) {
            Storage::disk('public')->delete($hero->image_path);
            $hero->forceFill(['image_path' => null])->save();
        }

        if ($request->hasFile('image')) {
            if ($hero->image_path) {
                Storage::disk('public')->delete($hero->image_path);
            }
            $stored = $request->file('image')->store('heroes/' . $hero->id, 'public');
            $hero->forceFill(['image_path' => $stored])->save();
        }
    }

    /** @param array<int,array<string,mixed>> $avatars */
    private function syncAvatars(Request $request, Hero $hero, array $avatars): void
    {
        $kept = [];
        $images = $request->file('avatars_images') ?? [];

        foreach (array_values($avatars) as $i => $av) {
            $payload = [
                'name' => $av['name'] ?? null,
                'image_alt' => $av['image_alt'] ?? null,
                'sort_order' => $i,
                'is_active' => $av['is_active'] ?? true,
            ];

            if (! empty($av['id'])) {
                $existing = $hero->avatars()->whereKey($av['id'])->first();
                if ($existing) {
                    $existing->update($payload);
                    $this->handleAvatarImage($existing, $av, $images[$i] ?? null);
                    $kept[] = $existing->id;
                    continue;
                }
            }

            $created = $hero->avatars()->create($payload);
            $this->handleAvatarImage($created, $av, $images[$i] ?? null);
            $kept[] = $created->id;
        }

        foreach ($hero->avatars()->whereNotIn('id', $kept ?: [0])->get() as $stale) {
            if ($stale->image_path) {
                Storage::disk('public')->delete($stale->image_path);
            }
            $stale->delete();
        }
    }

    private function handleAvatarImage(HeroAvatar $avatar, array $payload, $file): void
    {
        if (! empty($payload['image_remove']) && $avatar->image_path) {
            Storage::disk('public')->delete($avatar->image_path);
            $avatar->forceFill(['image_path' => null])->save();
        }

        if ($file) {
            if ($avatar->image_path) {
                Storage::disk('public')->delete($avatar->image_path);
            }
            $stored = $file->store('hero-avatars/' . $avatar->hero_id, 'public');
            $avatar->forceFill(['image_path' => $stored])->save();
        }
    }

    /** @return array<string,mixed> */
    private function validated(Request $request): array
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'];

        return $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'trust_count' => ['nullable', 'string', 'max:30'],
            'trust_text' => ['nullable', 'string', 'max:160'],
            'heading_line_one' => ['required', 'string', 'max:160'],
            'heading_line_two' => ['required', 'string', 'max:160'],
            'heading_gradient' => ['nullable', 'string', 'in:' . implode(',', self::GRADIENTS)],
            'description' => ['required', 'string', 'max:600'],
            'feature_lines' => ['nullable', 'array', 'max:8'],
            'feature_lines.*' => ['nullable', 'string', 'max:160'],
            'image' => $imageRules,
            'image_alt' => ['nullable', 'string', 'max:255'],
            'image_remove' => ['sometimes', 'boolean'],
            'primary_cta_label' => ['required', 'string', 'max:60'],
            'primary_cta_href' => ['required', 'string', 'max:255'],
            'secondary_cta_label' => ['nullable', 'string', 'max:60'],
            'secondary_cta_href' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],

            'avatars' => ['array', 'max:12'],
            'avatars.*.id' => ['nullable', 'integer'],
            'avatars.*.name' => ['nullable', 'string', 'max:120'],
            'avatars.*.image_alt' => ['nullable', 'string', 'max:255'],
            'avatars.*.is_active' => ['sometimes', 'boolean'],
            'avatars.*.image_remove' => ['sometimes', 'boolean'],

            'avatars_images' => ['array'],
            'avatars_images.*' => $imageRules,
        ]);
    }
}
