<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\DeliveryCategory;
use App\Models\Service;
use App\Support\ContentCache;
use App\Support\ServiceContentNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public const IMAGE_FIELDS = ['hero_image', 'tech_image', 'outcomes_image', 'snapshot_image', 'og_image'];

    public function index(\Illuminate\Http\Request $request): Response
    {
        $services = Service::query()
            ->with('category:id,title,slug')
            ->when($request->integer('category'), fn ($q, $id) => $q->where('delivery_category_id', $id))
            ->when($request->string('q')->toString(), function ($q, $term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('title', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->orderBy('delivery_category_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Service $s) => [
                'id' => $s->id,
                'title' => $s->title,
                'slug' => $s->slug,
                'sort_order' => $s->sort_order,
                'is_active' => $s->is_active,
                'noindex' => $s->noindex,
                'category' => $s->category ? [
                    'id' => $s->category->id,
                    'title' => $s->category->title,
                ] : null,
                'hero_image_url' => $s->imageUrl('hero_image_path'),
                'has_meta_title' => filled($s->meta_title),
                'has_meta_description' => filled($s->meta_description),
                'has_og_image' => filled($s->og_image_path),
            ]);

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'categories' => $this->categoriesOption(),
            'filters' => [
                'category' => $request->integer('category') ?: null,
                'q' => $request->string('q')->toString() ?: '',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Services/Create', [
            'categories' => $this->categoriesOption(),
            'defaults' => ServiceContentNormalizer::defaultStructure(),
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $service = Service::query()->create([
            'delivery_category_id' => $data['delivery_category_id'] ?? null,
            'slug' => $data['slug'] ?: $this->makeUniqueSlug($data['title']),
            'title' => $data['title'],
            'description' => $data['meta_description'] ?? '',
            'icon' => $this->listingIcon($data),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'noindex' => (bool) ($data['noindex'] ?? false),
            'canonical_url' => $data['canonical_url'] ?? null,
            'schema_type' => $data['schema_type'] ?? null,
            'content' => $data['content'],
        ]);

        foreach (self::IMAGE_FIELDS as $field) {
            $this->handleImageUpload($request, $service, $field);
        }

        ContentCache::forget();

        return redirect()->route('admin.services.index')
            ->with('success', "Service “{$service->title}” created.");
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => $this->serializeService($service),
            'categories' => $this->categoriesOption(),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->validated();

        $service->update([
            'delivery_category_id' => $data['delivery_category_id'] ?? null,
            'slug' => $data['slug'] ?: $this->makeUniqueSlug($data['title'], $service->id),
            'title' => $data['title'],
            'description' => $data['meta_description'] ?? $service->description,
            'icon' => $this->listingIcon($data, $service->icon),
            'sort_order' => $data['sort_order'] ?? $service->sort_order,
            'is_active' => $data['is_active'] ?? $service->is_active,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'noindex' => (bool) ($data['noindex'] ?? false),
            'canonical_url' => $data['canonical_url'] ?? null,
            'schema_type' => $data['schema_type'] ?? null,
            'content' => $data['content'],
        ]);

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->boolean($field.'_remove')) {
                $this->removeImage($service, $field);
            }
            if ($request->hasFile($field)) {
                $this->handleImageUpload($request, $service, $field);
            }
        }

        ContentCache::forget();

        return redirect()->route('admin.services.index')
            ->with('success', "Service “{$service->title}” updated.");
    }

    public function destroy(Service $service): RedirectResponse
    {
        foreach (['hero_image_path', 'tech_image_path', 'outcomes_image_path', 'snapshot_image_path', 'og_image_path'] as $field) {
            if ($service->{$field}) {
                Storage::disk('public')->delete($service->{$field});
            }
        }

        $service->delete();

        ContentCache::forget();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    /** @return array<int, array{id: int, title: string}> */
    private function categoriesOption(): array
    {
        return DeliveryCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title'])
            ->map(fn ($c) => ['id' => $c->id, 'title' => $c->title])
            ->all();
    }

    private function serializeService(Service $service): array
    {
        $content = ServiceContentNormalizer::mergeWithDefaults($service->content ?? []);

        return [
            'id' => $service->id,
            'delivery_category_id' => $service->delivery_category_id,
            'slug' => $service->slug,
            'title' => $service->title,
            'icon' => $service->icon,
            'sort_order' => $service->sort_order,
            'is_active' => $service->is_active,
            'meta_title' => $service->meta_title,
            'meta_description' => $service->meta_description,
            'meta_keywords' => $service->meta_keywords,
            'og_title' => $service->og_title,
            'og_description' => $service->og_description,
            'og_image_url' => $service->imageUrl('og_image_path'),
            'noindex' => (bool) $service->noindex,
            'canonical_url' => $service->canonical_url,
            'schema_type' => $service->schema_type,
            'hero_image_url' => $service->imageUrl('hero_image_path'),
            'tech_image_url' => $service->imageUrl('tech_image_path'),
            'outcomes_image_url' => $service->imageUrl('outcomes_image_path'),
            'snapshot_image_url' => $service->imageUrl('snapshot_image_path'),
            'content' => $content,
        ];
    }

    /** @param  array<string, mixed>  $data */
    private function listingIcon(array $data, ?string $fallback = null): string
    {
        $raw = trim((string) ($data['icon'] ?? ''));

        return $raw !== '' ? $raw : ($fallback ?: 'Sparkles');
    }

    private function handleImageUpload(\Illuminate\Http\Request $request, Service $service, string $field): void
    {
        if (! $request->hasFile($field)) {
            return;
        }

        $pathField = $field.'_path';
        if ($service->{$pathField}) {
            Storage::disk('public')->delete($service->{$pathField});
        }

        $stored = $request->file($field)->store('services/'.$service->slug, 'public');
        $service->forceFill([$pathField => $stored])->save();
    }

    private function removeImage(Service $service, string $field): void
    {
        $pathField = $field.'_path';
        if ($service->{$pathField}) {
            Storage::disk('public')->delete($service->{$pathField});
            $service->forceFill([$pathField => null])->save();
        }
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $i = 2;

        while (Service::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
