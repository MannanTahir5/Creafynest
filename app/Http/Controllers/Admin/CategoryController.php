<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ContentCache;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()->orderBy('name')->paginate(20);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190'],
        ]);

        $slug = trim((string) ($data['slug'] ?? '')) !== ''
            ? UniqueSlug::for('categories', $data['name'], trim($data['slug']))
            : UniqueSlug::for('categories', $data['name']);

        Category::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        ContentCache::forget();

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190'],
        ]);

        $slug = trim((string) ($data['slug'] ?? '')) !== ''
            ? UniqueSlug::for('categories', $data['name'], trim($data['slug']), $category->id)
            : UniqueSlug::for('categories', $data['name'], $category->slug, $category->id);

        $category->update([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        ContentCache::forget();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->blogs()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete a category that still has blog posts.');
        }

        $category->delete();

        ContentCache::forget();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
