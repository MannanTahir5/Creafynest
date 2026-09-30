<?php

namespace App\Support;

use App\Models\Blog;
use Illuminate\Support\Str;

final class BlogContent
{
    public static function toHtml(Blog $blog): string
    {
        if (($blog->content_format ?? 'html') === 'markdown') {
            return Str::markdown($blog->content, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
        }

        return $blog->content;
    }

    public static function plainTextExcerpt(Blog $blog, int $limit = 160): string
    {
        if (($blog->content_format ?? 'html') === 'markdown') {
            return Str::limit(strip_tags(self::toHtml($blog)), $limit);
        }

        return (string) str(strip_tags($blog->content))->limit($limit);
    }
}
