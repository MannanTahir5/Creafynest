<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Project;
use App\Models\SeoSiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

final class Seo
{
    /**
     * Build a SEO payload for the SeoHead component.
     *
     * Optional `$options` keys:
     *  - og_title: override Open Graph / Twitter title
     *  - og_description: override Open Graph / Twitter description
     *  - keywords: comma-separated keywords meta
     *  - canonical_override: full URL to use instead of the auto-generated canonical
     *  - noindex: force `noindex, nofollow` robots (overrides `robots` when true)
     *  - robots: explicit robots directive when not noindex (e.g. max-snippet)
     *  - twitter_site, twitter_creator: handles without @
     *  - og_locale: Open Graph locale (default applied in Blade if missing)
     *  - og_image_alt: description for og:image / twitter:image
     *  - theme_color: for meta theme-color
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public static function page(
        string $title,
        string $description,
        string $path = '/',
        ?string $image = null,
        string $ogType = 'website',
        array $options = [],
    ): array {
        $base = rtrim((string) config('app.url'), '/');
        $path = trim($path, '/');
        $autoCanonical = $path === '' ? $base.'/' : $base.'/'.$path;

        $canonicalOverride = trim((string) ($options['canonical_override'] ?? ''));
        $canonical = $canonicalOverride !== '' ? $canonicalOverride : $autoCanonical;

        $siteName = (string) config('app.name', 'Azee');
        $fullTitle = Str::lower($title) === Str::lower($siteName)
            ? $siteName
            : "{$title} | {$siteName}";

        $ogImage = null;
        if ($image !== null && $image !== '') {
            $ogImage = Str::startsWith($image, ['http://', 'https://'])
                ? $image
                : $base.'/'.ltrim($image, '/');
        }

        $site = SeoSiteSetting::cached();
        if ($ogImage === null && $site !== null) {
            $def = $site->defaultOgImageUrl();
            if (is_string($def) && $def !== '') {
                $ogImage = Str::startsWith($def, ['http://', 'https://'])
                    ? $def
                    : $base.'/'.ltrim($def, '/');
            }
        }

        $ogTitle = trim((string) ($options['og_title'] ?? '')) ?: $fullTitle;
        $ogDescription = trim((string) ($options['og_description'] ?? '')) ?: $description;
        $keywords = trim((string) ($options['keywords'] ?? '')) ?: null;

        $robots = null;
        if (! empty($options['noindex'])) {
            $robots = 'noindex, nofollow';
        } else {
            $robotsOpt = trim((string) ($options['robots'] ?? ''));
            $robots = $robotsOpt !== '' ? $robotsOpt : null;
        }

        $twitterSite = trim((string) ($options['twitter_site'] ?? ''));
        $twitterSite = $twitterSite !== '' ? ltrim($twitterSite, '@') : null;
        $twitterCreator = trim((string) ($options['twitter_creator'] ?? ''));
        $twitterCreator = $twitterCreator !== '' ? ltrim($twitterCreator, '@') : null;
        $ogLocale = trim((string) ($options['og_locale'] ?? ''));
        $ogLocale = $ogLocale !== '' ? $ogLocale : null;
        $ogImageAlt = trim((string) ($options['og_image_alt'] ?? ''));
        $ogImageAlt = $ogImageAlt !== '' ? $ogImageAlt : null;
        $themeColor = trim((string) ($options['theme_color'] ?? ''));
        $themeColor = $themeColor !== '' ? $themeColor : null;

        if ($site !== null) {
            if ($twitterSite === null) {
                $ts = trim((string) $site->twitter_site);
                if ($ts !== '') {
                    $twitterSite = ltrim($ts, '@');
                }
            }
            if ($twitterCreator === null) {
                $tc = trim((string) $site->twitter_creator);
                if ($tc !== '') {
                    $twitterCreator = ltrim($tc, '@');
                }
            }
            if ($ogLocale === null) {
                $ol = trim((string) $site->og_locale);
                $ogLocale = $ol !== '' ? $ol : null;
            }
            if ($themeColor === null) {
                $tc = trim((string) $site->theme_color);
                $themeColor = $tc !== '' ? $tc : null;
            }
            if ($ogImageAlt === null) {
                $oa = trim((string) $site->default_og_image_alt);
                $ogImageAlt = $oa !== '' ? $oa : null;
            }
            if ($robots === null && empty($options['noindex'])) {
                $dr = trim((string) $site->default_robots);
                if ($dr !== '') {
                    $robots = $dr;
                }
            }
        }

        $payload = [
            'title' => $fullTitle,
            'description' => $description,
            'canonical' => $canonical,
            'og_type' => $ogType,
            'image' => $ogImage,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'keywords' => $keywords,
            'robots' => $robots,
            'twitter_site' => $twitterSite,
            'twitter_creator' => $twitterCreator,
            'og_locale' => $ogLocale,
            'og_image_alt' => $ogImageAlt,
            'theme_color' => $themeColor,
        ];

        foreach (['article_published_time', 'article_modified_time', 'article_section'] as $articleKey) {
            $v = $options[$articleKey] ?? null;
            if (is_string($v) && trim($v) !== '') {
                $payload[$articleKey] = trim($v);
            }
        }

        // Make the SEO payload available to the Blade root view so meta tags are
        // rendered server-side (crawler-friendly) in addition to client-side
        // hydration via the Inertia Head component.
        View::share('seoMeta', $payload);

        return $payload;
    }

    /**
     * Make JSON-LD schemas available to the Blade root view.
     *
     * @param  array<int,array<string,mixed>>  $schemas
     */
    public static function shareSchemas(array $schemas): void
    {
        View::share('pageSchemas', $schemas);
    }

    /**
     * Build a Schema.org JSON-LD object for a Service.
     *
     * @return array<string,mixed>
     */
    public static function serviceSchema(
        string $title,
        string $description,
        string $url,
        ?string $imageUrl = null,
        ?string $type = null,
    ): array {
        $type = $type !== null && $type !== '' ? $type : 'Service';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $title,
            'description' => $description,
            'url' => $url,
            'provider' => [
                '@type' => 'Organization',
                'name' => (string) config('app.name', 'Azee'),
                'url' => rtrim((string) config('app.url'), '/').'/',
            ],
        ];

        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }

        return $schema;
    }

    /**
     * Site-wide JSON-LD (rendered in the Blade root view for crawler-visible markup).
     *
     * @param  array{description?:?string, same_as?:array<int, string>, email?:?string}  $organization
     * @return array<int, array<string, mixed>>
     */
    public static function globalSchemas(array $organization = [], ?string $blogSearchTargetTemplate = null): array
    {
        $name = (string) config('app.name', 'Azee');
        $url = rtrim((string) config('app.url'), '/').'/';

        $description = isset($organization['description']) ? trim((string) $organization['description']) : '';
        $sameAs = $organization['same_as'] ?? [];
        if (! is_array($sameAs)) {
            $sameAs = [];
        }
        $sameAs = array_values(array_filter($sameAs, fn ($u) => is_string($u) && $u !== ''));
        $email = isset($organization['email']) ? trim((string) $organization['email']) : '';

        $orgNode = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => $url,
        ];

        if ($description !== '') {
            $orgNode['description'] = $description;
        }

        if ($sameAs !== []) {
            $orgNode['sameAs'] = $sameAs;
        }

        if ($email !== '') {
            $orgNode['contactPoint'] = [
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => $email,
                'areaServed' => 'Worldwide',
                'availableLanguage' => ['English'],
            ];
        }

        $webSite = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $name,
            'url' => $url,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $name,
                'url' => $url,
            ],
        ];

        $searchTarget = $blogSearchTargetTemplate !== null ? trim($blogSearchTargetTemplate) : '';
        if ($searchTarget !== '') {
            $webSite['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => $searchTarget,
                'query-input' => 'required name=search_term_string',
            ];
        }

        return [
            $webSite,
            $orgNode,
        ];
    }

    /**
     * @param  list<array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbListSchema(array $items): array
    {
        $elements = [];
        foreach ($items as $i => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function blogPostingSchema(Blog $blog, string $url, ?string $imageUrl, ?string $type = null): array
    {
        $description = $blog->meta_description
            ? (string) $blog->meta_description
            : BlogContent::plainTextExcerpt($blog, 300);

        $resolvedType = $type !== null && $type !== '' ? $type : 'BlogPosting';

        $siteName = (string) config('app.name', 'Azee');
        $siteUrl = rtrim((string) config('app.url'), '/').'/';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $resolvedType,
            'headline' => $blog->title,
            'description' => $description,
            'datePublished' => $blog->created_at?->toIso8601String(),
            'dateModified' => $blog->updated_at?->toIso8601String(),
            'url' => $url,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'author' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $siteUrl,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $siteUrl,
            ],
        ];

        if ($imageUrl) {
            $schema['image'] = [$imageUrl];
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public static function creativeWorkSchema(Project $project, string $url, ?string $imageUrl): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => Str::limit((string) $project->description, 300),
            'url' => $url,
        ];

        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }

        return $schema;
    }
}
