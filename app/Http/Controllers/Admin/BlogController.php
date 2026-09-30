<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Support\ContentCache;
use App\Support\ImageRules;
use App\Support\UniqueSlug;
use App\Support\WebpDerivative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    /**
     * Maps logical image field name (request input) → DB path column on Blog.
     * For legacy reasons the cover image is stored on the `image` column.
     */
    public const IMAGE_FIELDS = [
        'image' => 'image',
        'og_image' => 'og_image_path',
    ];

    public function index(Request $request): Response
    {
        $q = (string) $request->query('q', '');
        $categoryId = (int) $request->query('category', 0);

        $blogs = Blog::query()
            ->with('category:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('title', 'like', '%'.$q.'%')
                        ->orWhere('slug', 'like', '%'.$q.'%');
                });
            })
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Blog $b) => [
                'id' => $b->id,
                'title' => $b->title,
                'slug' => $b->slug,
                'created_at' => $b->created_at?->toIso8601String(),
                'category' => $b->category ? [
                    'id' => $b->category->id,
                    'name' => $b->category->name,
                ] : null,
                'image_url' => $b->imageUrl('image'),
                'noindex' => (bool) $b->noindex,
                'has_meta_title' => filled($b->meta_title),
                'has_meta_description' => filled($b->meta_description),
                'has_og_image' => filled($b->og_image_path),
            ]);

        return Inertia::render('Admin/Blogs/Index', [
            'filters' => [
                'q' => $q,
                'category' => $categoryId ?: null,
            ],
            'blogs' => $blogs,
            'categories' => $this->categoriesOption(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Blogs/Create', [
            'categories' => $this->categoriesOption(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedBlog($request);

        $slug = $data['slug'] !== ''
            ? UniqueSlug::for('blogs', $data['title'], $data['slug'])
            : UniqueSlug::for('blogs', $data['title']);

        $blog = Blog::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'content_format' => $data['content_format'],
            'category_id' => $data['category_id'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keywords' => $data['meta_keywords'],
            'og_title' => $data['og_title'],
            'og_description' => $data['og_description'],
            'noindex' => $data['noindex'],
            'canonical_url' => $data['canonical_url'],
            'schema_type' => $data['schema_type'],
        ]);

        foreach (self::IMAGE_FIELDS as $field => $column) {
            $this->handleImageUpload($request, $blog, $field, $column);
        }

        ContentCache::forget();

        return redirect()->route('admin.blogs.index')->with('success', "Blog post “{$blog->title}” created.");
    }

    public function edit(Blog $blog): Response
    {
        $blog->load('category');

        return Inertia::render('Admin/Blogs/Edit', [
            'blog' => $this->serializeBlog($blog),
            'categories' => $this->categoriesOption(),
        ]);
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $this->validatedBlog($request);

        $slug = $data['slug'] !== ''
            ? UniqueSlug::for('blogs', $data['title'], $data['slug'], $blog->id)
            : UniqueSlug::for('blogs', $data['title'], $blog->slug, $blog->id);

        $blog->update([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'content_format' => $data['content_format'],
            'category_id' => $data['category_id'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keywords' => $data['meta_keywords'],
            'og_title' => $data['og_title'],
            'og_description' => $data['og_description'],
            'noindex' => $data['noindex'],
            'canonical_url' => $data['canonical_url'],
            'schema_type' => $data['schema_type'],
        ]);

        foreach (self::IMAGE_FIELDS as $field => $column) {
            if ($request->boolean($field.'_remove')) {
                $this->removeImage($blog, $column);
            }
            if ($request->hasFile($field)) {
                $this->handleImageUpload($request, $blog, $field, $column);
            }
        }

        ContentCache::forget();

        return redirect()->route('admin.blogs.index')->with('success', "Blog post “{$blog->title}” updated.");
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        foreach (self::IMAGE_FIELDS as $column) {
            if ($blog->{$column}) {
                WebpDerivative::deleteForOriginal($blog->{$column});
                Storage::disk('public')->delete($blog->{$column});
            }
        }

        $blog->delete();

        ContentCache::forget();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted.');
    }

    private function serializeBlog(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'content' => $blog->content,
            'content_format' => $blog->content_format ?? 'html',
            'category_id' => $blog->category_id,
            'meta_title' => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'meta_keywords' => $blog->meta_keywords,
            'og_title' => $blog->og_title,
            'og_description' => $blog->og_description,
            'og_image_url' => $blog->imageUrl('og_image_path'),
            'noindex' => (bool) $blog->noindex,
            'canonical_url' => $blog->canonical_url,
            'schema_type' => $blog->schema_type,
            'image_url' => $blog->imageUrl('image'),
        ];
    }

    /** @return array<int,array{id:int,name:string}> */
    private function categoriesOption(): array
    {
        return Category::query()->orderBy('name')->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->all();
    }

    /**
     * @return array{
     *   title:string, slug:string, content:string, content_format:string, category_id:int,
     *   meta_title:?string, meta_description:?string, meta_keywords:?string,
     *   og_title:?string, og_description:?string, noindex:bool,
     *   canonical_url:?string, schema_type:?string
     * }
     */
    private function validatedBlog(Request $request): array
    {
        $this->prepareBlogRequest($request);

        $allowedSchemaTypes = ['', 'BlogPosting', 'Article', 'NewsArticle', 'TechArticle'];

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190'],
            'content' => ['required', 'string'],
            'content_format' => ['required', 'string', 'in:markdown,html'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:190'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'noindex' => ['sometimes', 'boolean'],
            'canonical_url' => ['nullable', 'string', 'url', 'max:255'],
            'schema_type' => ['nullable', 'string', 'in:'.implode(',', $allowedSchemaTypes)],

            'image' => ImageRules::lenient(),
            'image_remove' => ['sometimes', 'boolean'],
            'og_image' => ImageRules::lenient(),
            'og_image_remove' => ['sometimes', 'boolean'],
        ]);

        return [
            'title' => $validated['title'],
            'slug' => trim((string) ($validated['slug'] ?? '')),
            'content' => $validated['content'],
            'content_format' => $validated['content_format'],
            'category_id' => (int) $validated['category_id'],
            'meta_title' => $validated['meta_title'] ?? null ?: null,
            'meta_description' => $validated['meta_description'] ?? null ?: null,
            'meta_keywords' => $validated['meta_keywords'] ?? null ?: null,
            'og_title' => $validated['og_title'] ?? null ?: null,
            'og_description' => $validated['og_description'] ?? null ?: null,
            'noindex' => (bool) ($validated['noindex'] ?? false),
            'canonical_url' => $validated['canonical_url'] ?? null ?: null,
            'schema_type' => $validated['schema_type'] ?? null ?: null,
        ];
    }

    private function prepareBlogRequest(Request $request): void
    {
        foreach (['canonical_url', 'schema_type'] as $maybeNull) {
            if ($request->input($maybeNull) === '') {
                $request->merge([$maybeNull => null]);
            }
        }
    }

    private function handleImageUpload(Request $request, Blog $blog, string $field, string $column): void
    {
        if (! $request->hasFile($field)) {
            return;
        }

        if ($blog->{$column}) {
            WebpDerivative::deleteForOriginal($blog->{$column});
            Storage::disk('public')->delete($blog->{$column});
        }

        $stored = $request->file($field)->store('blogs', 'public');
        WebpDerivative::encodeFromStoredPublicPath($stored);
        $blog->forceFill([$column => $stored])->save();
    }

    private function removeImage(Blog $blog, string $column): void
    {
        if ($blog->{$column}) {
            WebpDerivative::deleteForOriginal($blog->{$column});
            Storage::disk('public')->delete($blog->{$column});
            $blog->forceFill([$column => null])->save();
        }
    }
}
