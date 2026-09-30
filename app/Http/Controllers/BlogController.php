<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Support\BlogContent;
use App\Support\ContentCache;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use App\Support\WebpDerivative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class BlogController
{
    public function __invoke(Request $request): Response
    {
        $q = (string) $request->query('q', '');
        $categorySlug = (string) $request->query('category', '');

        $categories = Cache::remember(ContentCache::BLOG_CATEGORIES, ContentCache::TTL, function () {
            return Category::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);
        });

        $hasFilters = $q !== '' || $categorySlug !== '';
        $page = max(1, (int) $request->query('page', 1));

        $featured = null;
        if (! $hasFilters && $page === 1) {
            $featured = Blog::query()
                ->with('category')
                ->latest()
                ->first();
        }

        $excludeFeaturedId = $featured?->getKey();

        $blogs = Blog::query()
            ->with('category')
            ->when($excludeFeaturedId, fn ($query) => $query->whereKeyNot($excludeFeaturedId))
            ->when($categorySlug !== '', function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('title', 'like', '%'.$q.'%')
                        ->orWhere('content', 'like', '%'.$q.'%');
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Blog $blog) => $this->serializePost($blog));

        $totalPosts = Blog::query()->count();

        $latestPosts = Blog::query()
            ->with('category')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Blog $blog) => $this->serializeSidebarPost($blog))
            ->all();

        return Inertia::render('Blog/Index', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::BLOG,
                'blog',
                'Blog',
                'Notes on Laravel, Vue, Inertia, and shipping maintainable products. Search posts and browse by category.',
            ),
            'filters' => [
                'q' => $q,
                'category' => $categorySlug,
            ],
            'categories' => $categories,
            'posts' => $blogs,
            'featured' => $featured ? $this->serializePost($featured, 220) : null,
            'latestPosts' => $latestPosts,
            'blogByline' => 'By Creafynest',
            'totals' => [
                'posts' => $totalPosts,
                'categories' => $categories->count(),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeSidebarPost(Blog $blog): array
    {
        $imagePath = $blog->image;
        $imageUrl = WebpDerivative::publicDiskRelativeUrl($imagePath);
        $webpUrl = WebpDerivative::publicWebpRelativeUrlIfExists($imagePath);

        return [
            'title' => $blog->title,
            'slug' => $blog->slug,
            'image_url' => $imageUrl,
            'image_webp_url' => $webpUrl,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function serializePost(Blog $blog, int $excerptLimit = 160): array
    {
        $imagePath = $blog->image;
        $imageUrl = WebpDerivative::publicDiskRelativeUrl($imagePath);
        $webpUrl = WebpDerivative::publicWebpRelativeUrlIfExists($imagePath);

        return [
            'title' => $blog->title,
            'slug' => $blog->slug,
            'category' => $blog->category?->name,
            'category_slug' => $blog->category?->slug,
            'date' => $blog->created_at?->format('M j, Y'),
            'date_iso' => $blog->created_at?->toIso8601String(),
            'excerpt' => BlogContent::plainTextExcerpt($blog, $excerptLimit),
            'image_url' => $imageUrl,
            'image_webp_url' => $webpUrl,
            'reading_minutes' => $this->estimateReadingMinutes($blog),
        ];
    }

    private function estimateReadingMinutes(Blog $blog): int
    {
        $plain = BlogContent::plainTextExcerpt($blog, 100000);
        $words = $plain === '' ? 0 : str_word_count($plain);

        return (int) max(1, ceil($words / 220));
    }
}

