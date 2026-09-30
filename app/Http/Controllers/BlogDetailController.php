<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Support\BlogContent;
use App\Support\Seo;
use App\Support\WebpDerivative;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlogDetailController
{
    public function __invoke(Blog $blog): Response
    {
        $blog->load('category');

        $related = Blog::query()
            ->where('category_id', $blog->category_id)
            ->whereKeyNot($blog->getKey())
            ->where('noindex', false)
            ->latest()
            ->limit(3)
            ->get(['id', 'title', 'slug', 'created_at']);

        // Cover image (legacy `image` column)
        $hasStoredImage = $blog->image && Storage::disk('public')->exists($blog->image);
        $imageUrl = $hasStoredImage
            ? WebpDerivative::publicDiskRelativeUrl($blog->image)
            : null;
        $imageWebpUrl = $hasStoredImage
            ? WebpDerivative::publicWebpRelativeUrlIfExists($blog->image)
            : null;

        // Open Graph image (custom, falls back to cover)
        $hasOgImage = $blog->og_image_path && Storage::disk('public')->exists($blog->og_image_path);
        $ogImageRaw = $hasOgImage
            ? WebpDerivative::publicDiskRelativeUrl($blog->og_image_path)
            : ($imageWebpUrl ?? $imageUrl);

        $canonicalPath = 'blog/'.$blog->slug;
        $canonicalUrl = rtrim((string) config('app.url'), '/').'/'.$canonicalPath;

        $titleStem = $blog->meta_title ?: $blog->title;
        $description = $blog->meta_description
            ? (string) $blog->meta_description
            : BlogContent::plainTextExcerpt($blog, 160);

        $seoPayload = Seo::page(
            $titleStem,
            $description,
            $canonicalPath,
            $ogImageRaw,
            'article',
            [
                'og_title' => $blog->og_title,
                'og_description' => $blog->og_description,
                'keywords' => $blog->meta_keywords,
                'canonical_override' => $blog->canonical_url,
                'noindex' => (bool) $blog->noindex,
                'og_image_alt' => $blog->title,
                'article_published_time' => $blog->created_at?->toIso8601String(),
                'article_modified_time' => $blog->updated_at?->toIso8601String(),
                'article_section' => $blog->category?->name,
            ],
        );

        $base = rtrim((string) config('app.url'), '/');

        $schemas = [
            Seo::blogPostingSchema(
                $blog,
                $canonicalUrl,
                $seoPayload['image'] ?? null,
                $blog->schema_type,
            ),
            Seo::breadcrumbListSchema([
                ['name' => 'Home', 'url' => $base.'/'],
                ['name' => 'Blog', 'url' => $base.'/blog'],
                ['name' => $blog->title, 'url' => $canonicalUrl],
            ]),
        ];
        Seo::shareSchemas($schemas);

        return Inertia::render('Blog/Show', [
            'seo' => $seoPayload,
            'schemas' => $schemas,
            'post' => [
                'title' => $blog->title,
                'slug' => $blog->slug,
                'content_html' => BlogContent::toHtml($blog),
                'content_format' => $blog->content_format ?? 'html',
                'image_url' => $imageUrl,
                'image_webp_url' => $imageWebpUrl,
                'category' => $blog->category?->name,
                'published_at' => $blog->created_at?->format('M Y'),
                'meta_title' => $blog->meta_title,
                'meta_description' => $blog->meta_description,
            ],
            'relatedPosts' => $related->map(fn (Blog $relatedPost) => [
                'title' => $relatedPost->title,
                'slug' => $relatedPost->slug,
                'date' => $relatedPost->created_at?->format('M Y'),
            ]),
        ]);
    }
}
