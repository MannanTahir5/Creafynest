<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\ContentCache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SitemapController
{
    public function __invoke(): Response
    {
        if (Schema::hasTable('site_settings')) {
            $row = SiteSetting::cached();
            if ($row !== null && $row->sitemap_enabled === false) {
                return response('Not Found', 404)->header('Content-Type', 'text/plain; charset=UTF-8');
            }
        }

        $xml = Cache::remember(ContentCache::SITEMAP_XML, ContentCache::TTL, function () {
            $base = rtrim((string) config('app.url'), '/');

            $urls = [];

            foreach (['', 'about', 'portfolio', 'services', 'blog', 'contact'] as $segment) {
                $loc = $segment === '' ? $base.'/' : $base.'/'.$segment;
                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => now()->toAtomString(),
                ];
            }

            // Auto-include all live, indexable services
            foreach (
                Service::query()
                    ->where('is_active', true)
                    ->where('noindex', false)
                    ->orderBy('slug')
                    ->get(['slug', 'updated_at'])
                as $service
            ) {
                $urls[] = [
                    'loc' => $base.'/services/'.$service->slug,
                    'lastmod' => $service->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            }

            foreach (Project::query()->orderBy('slug')->get(['slug', 'updated_at']) as $project) {
                $urls[] = [
                    'loc' => $base.'/portfolio/'.$project->slug,
                    'lastmod' => $project->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            }

            // Skip noindex blog posts
            foreach (
                Blog::query()
                    ->where('noindex', false)
                    ->orderBy('slug')
                    ->get(['slug', 'updated_at'])
                as $blog
            ) {
                $urls[] = [
                    'loc' => $base.'/blog/'.$blog->slug,
                    'lastmod' => $blog->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            }

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
