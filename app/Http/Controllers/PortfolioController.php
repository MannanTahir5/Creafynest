<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\ContentCache;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController
{
    public function __invoke(Request $request): Response
    {
        $selectedCategories = self::normalizedCategories($request);

        $categories = Cache::remember(ContentCache::PORTFOLIO_CATEGORIES, ContentCache::TTL, function () {
            return Project::query()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->values();
        });

        $projects = Project::query()
            ->when(count($selectedCategories) > 0, fn ($q) => $q->whereIn('category', $selectedCategories))
            ->orderByRaw("case when image is not null and image != '' then 0 else 1 end")
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(function (Project $project) {
                $images = Project::caseStudyCardImages($project);

                return [
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'category' => $project->category,
                    'excerpt' => str($project->description)->limit(220),
                    'tech' => $project->tech_stack ?? [],
                    'image_url' => $images['url'],
                    'image_webp_url' => $images['webp_url'],
                ];
            });

        return Inertia::render('Portfolio/Index', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::PORTFOLIO,
                'portfolio',
                'Portfolio',
                'Browse case studies and shipped work. Filter by category to explore Laravel, Vue, and full-stack projects.',
            ),
            'filters' => [
                'categories' => $selectedCategories,
            ],
            'categories' => $categories,
            'projects' => $projects,
        ]);
    }

    /**
     * @return list<string>
     */
    private static function normalizedCategories(Request $request): array
    {
        $raw = $request->query('categories');
        if (is_array($raw)) {
            $list = array_values(array_filter(array_map('strval', $raw)));

            return array_values(array_unique($list));
        }

        if (is_string($raw) && $raw !== '') {
            $list = array_map('trim', explode(',', $raw));

            return array_values(array_unique(array_filter($list)));
        }

        $legacy = (string) $request->query('category', '');
        if ($legacy !== '') {
            return [$legacy];
        }

        return [];
    }
}
