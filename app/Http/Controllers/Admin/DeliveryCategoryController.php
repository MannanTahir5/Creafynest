<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCategory;
use App\Models\DeliveryItem;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = DeliveryCategory::query()
            ->withCount('items')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->through(fn (DeliveryCategory $c) => [
                'id' => $c->id,
                'slug' => $c->slug,
                'title' => $c->title,
                'subtitle' => $c->subtitle,
                'sort_order' => $c->sort_order,
                'is_active' => $c->is_active,
                'items_count' => $c->items_count,
                'image_url' => $c->imageUrl(),
            ]);

        return Inertia::render('Admin/DeliveryCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/DeliveryCategories/Create', [
            'nextSortOrder' => (int) (DeliveryCategory::query()->max('sort_order') ?? 0) + 1,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $category = DB::transaction(function () use ($request, $data) {
            $category = DeliveryCategory::query()->create([
                'slug' => $data['slug'] ?: $this->makeUniqueSlug($data['title']),
                'title' => $data['title'],
                'subtitle' => $data['subtitle'],
                'feature_icon' => $data['feature_icon'],
                'image_alt' => $data['image_alt'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->handleCategoryImage($request, $category);
            $this->syncItems($request, $category, $data['items'] ?? []);

            return $category;
        });

        ContentCache::forget();

        return redirect()->route('admin.delivery-categories.index')
            ->with('success', "Category “{$category->title}” created.");
    }

    public function edit(DeliveryCategory $deliveryCategory): Response
    {
        $deliveryCategory->load('items');

        return Inertia::render('Admin/DeliveryCategories/Edit', [
            'category' => [
                'id' => $deliveryCategory->id,
                'slug' => $deliveryCategory->slug,
                'title' => $deliveryCategory->title,
                'subtitle' => $deliveryCategory->subtitle,
                'feature_icon' => $deliveryCategory->feature_icon,
                'image_alt' => $deliveryCategory->image_alt,
                'image_url' => $deliveryCategory->imageUrl(),
                'sort_order' => $deliveryCategory->sort_order,
                'is_active' => $deliveryCategory->is_active,
                'items' => $deliveryCategory->items->map(fn (DeliveryItem $i) => [
                    'id' => $i->id,
                    'title' => $i->title,
                    'description' => $i->description,
                    'icon' => $i->icon,
                    'image_alt' => $i->image_alt,
                    'image_url' => $i->imageUrl(),
                ])->all(),
            ],
        ]);
    }

    public function update(Request $request, DeliveryCategory $deliveryCategory): RedirectResponse
    {
        $data = $this->validated($request, $deliveryCategory->id);

        DB::transaction(function () use ($request, $deliveryCategory, $data) {
            $deliveryCategory->update([
                'slug' => $data['slug'] ?: $this->makeUniqueSlug($data['title'], $deliveryCategory->id),
                'title' => $data['title'],
                'subtitle' => $data['subtitle'],
                'feature_icon' => $data['feature_icon'],
                'image_alt' => $data['image_alt'] ?? null,
                'sort_order' => $data['sort_order'] ?? $deliveryCategory->sort_order,
                'is_active' => $data['is_active'] ?? $deliveryCategory->is_active,
            ]);

            $this->handleCategoryImage($request, $deliveryCategory);
            $this->syncItems($request, $deliveryCategory, $data['items'] ?? []);
        });

        ContentCache::forget();

        return redirect()->route('admin.delivery-categories.index')
            ->with('success', "Category “{$deliveryCategory->title}” updated.");
    }

    public function destroy(DeliveryCategory $deliveryCategory): RedirectResponse
    {
        if ($deliveryCategory->image_path) {
            Storage::disk('public')->delete($deliveryCategory->image_path);
        }
        foreach ($deliveryCategory->items as $item) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
        }

        $deliveryCategory->delete();

        ContentCache::forget();

        return redirect()->route('admin.delivery-categories.index')->with('success', 'Category deleted.');
    }

    /** @return array<string,mixed> */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = ['nullable', 'string', 'alpha_dash', 'max:120'];
        $slugRule[] = 'unique:delivery_categories,slug' . ($ignoreId !== null ? ',' . $ignoreId : '');

        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'];

        return $request->validate([
            'slug' => $slugRule,
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['required', 'string', 'max:300'],
            'feature_icon' => ['required', 'string', 'max:60'],
            'image' => $imageRules,
            'image_alt' => ['nullable', 'string', 'max:255'],
            'image_remove' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],

            'items' => ['array', 'max:24'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.title' => ['required', 'string', 'max:120'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.icon' => ['required', 'string', 'max:60'],
            'items.*.image_alt' => ['nullable', 'string', 'max:255'],
            'items.*.image_remove' => ['sometimes', 'boolean'],

            // Per-item images come in as files keyed by their ROW INDEX:
            //   items_images.0, items_images.1, ...
            'items_images' => ['array'],
            'items_images.*' => $imageRules,
        ]);
    }

    private function handleCategoryImage(Request $request, DeliveryCategory $category): void
    {
        if ($request->boolean('image_remove') && $category->image_path) {
            Storage::disk('public')->delete($category->image_path);
            $category->forceFill(['image_path' => null])->save();
        }

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $stored = $request->file('image')->store('delivery-categories/' . $category->id, 'public');
            $category->forceFill(['image_path' => $stored])->save();
        }
    }

    /** @param array<int,array<string,mixed>> $items */
    private function syncItems(Request $request, DeliveryCategory $category, array $items): void
    {
        $keptIds = [];
        $itemsImages = $request->file('items_images') ?? [];

        foreach (array_values($items) as $position => $item) {
            $payload = [
                'title' => $item['title'],
                'description' => $item['description'],
                'icon' => $item['icon'],
                'image_alt' => $item['image_alt'] ?? null,
                'sort_order' => $position,
            ];

            if (!empty($item['id'])) {
                $existing = $category->items()->whereKey($item['id'])->first();
                if ($existing) {
                    $existing->update($payload);
                    $this->handleItemImageOps($existing, $item, $itemsImages[$position] ?? null);
                    $keptIds[] = $existing->id;
                    continue;
                }
            }

            $created = $category->items()->create($payload);
            $this->handleItemImageOps($created, $item, $itemsImages[$position] ?? null);
            $keptIds[] = $created->id;
        }

        $stale = $category->items()->whereNotIn('id', $keptIds ?: [0])->get();
        foreach ($stale as $s) {
            if ($s->image_path) {
                Storage::disk('public')->delete($s->image_path);
            }
            $s->delete();
        }
    }

    private function handleItemImageOps(DeliveryItem $item, array $payload, $uploadedFile): void
    {
        if (!empty($payload['image_remove']) && $item->image_path) {
            Storage::disk('public')->delete($item->image_path);
            $item->forceFill(['image_path' => null])->save();
        }

        if ($uploadedFile) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $stored = $uploadedFile->store('delivery-items/' . $item->id, 'public');
            $item->forceFill(['image_path' => $stored])->save();
        }
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'category';
        $slug = $base;
        $i = 2;

        while (DeliveryCategory::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
