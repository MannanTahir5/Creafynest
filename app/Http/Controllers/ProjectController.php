<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo;
use App\Support\WebpDerivative;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController
{
    public function __invoke(Project $project): Response
    {
        $previousProject = Project::query()
            ->where('id', '<', $project->id)
            ->latest('id')
            ->first(['title', 'slug']);

        $nextProject = Project::query()
            ->where('id', '>', $project->id)
            ->oldest('id')
            ->first(['title', 'slug']);

        $gallery = $project->gallery ?? [];
        $galleryItems = collect($gallery)
            ->filter()
            ->map(fn (string $path) => [
                'url' => Storage::disk('public')->url($path),
                'webp_url' => WebpDerivative::publicWebpUrlIfExists($path),
            ])
            ->values()
            ->all();

        $sectionImage = function (?string $path): ?array {
            if (! $path) {
                return null;
            }

            return [
                'url' => Storage::disk('public')->url($path),
                'webp_url' => WebpDerivative::publicWebpUrlIfExists($path),
            ];
        };

        $videoUrls = collect($project->video_urls ?? [])
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->map(function (string $value) {
                if (preg_match('/^https?:\/\//i', $value) || preg_match('/^\/\//', $value)) {
                    return $value;
                }

                return Storage::disk('public')->url($value);
            })
            ->values()
            ->all();

        if ($project->video_url && empty($videoUrls)) {
            $videoUrls = [$project->video_url];
        }

        $hero = Project::caseStudyCardImages($project);
        $imageUrl = $hero['url'];
        $imageWebpUrl = $hero['webp_url'];
        $ogImage = $imageWebpUrl ?? $imageUrl;

        $canonicalPath = 'portfolio/'.$project->slug;
        $canonicalUrl = rtrim((string) config('app.url'), '/').'/'.$canonicalPath;
        $base = rtrim((string) config('app.url'), '/');

        $schemas = [
            Seo::creativeWorkSchema($project, $canonicalUrl, $ogImage),
            Seo::breadcrumbListSchema([
                ['name' => 'Home', 'url' => $base.'/'],
                ['name' => 'Portfolio', 'url' => $base.'/portfolio'],
                ['name' => $project->title, 'url' => $canonicalUrl],
            ]),
        ];
        Seo::shareSchemas($schemas);

        return Inertia::render('Portfolio/Show', [
            'seo' => Seo::page(
                $project->title,
                str($project->description)->limit(160),
                $canonicalPath,
                $ogImage,
                'website',
                [
                    'og_image_alt' => $project->title,
                ],
            ),
            'schemas' => $schemas,
            'project' => [
                'title' => $project->title,
                'slug' => $project->slug,
                'category' => $project->category,
                'description' => $project->description,
                'client_name' => $project->client_name,
                'location' => $project->location,
                'industry' => $project->industry,
                'services' => $project->services ?? [],
                'problem_title' => $project->problem_title,
                'problem_text' => $project->problem_text,
                'how_it_started_title' => $project->how_it_started_title ?? $project->problem_title,
                'how_it_started_text' => $project->how_it_started_text ?? $project->problem_text,
                'how_it_started_image' => $sectionImage($project->how_it_started_image ?? null) ?? ($project->problem_title ? null : null),
                'challenge_title' => $project->challenge_title ?? $project->system_title ?? $project->problem_title,
                'challenge_text' => $project->challenge_text ?? $project->system_text ?? $project->problem_text,
                'challenge_image' => $sectionImage($project->challenge_image ?? null),
                'approach_title' => $project->approach_title,
                'approach_text' => $project->approach_text,
                'approach_image' => $sectionImage($project->approach_image ?? null),
                'system_title' => $project->system_title,
                'system_text' => $project->system_text,
                'repeat_title' => $project->repeat_title,
                'repeat_text' => $project->repeat_text,
                'results_title' => $project->results_title ?? $project->repeat_title ?? $project->next_title,
                'results_text' => $project->results_text ?? $project->repeat_text ?? $project->next_text,
                'results_image' => $sectionImage($project->results_image ?? null),
                'next_title' => $project->next_title,
                'next_text' => $project->next_text,
                'tech_stack' => $project->tech_stack ?? [],
                'gallery' => $galleryItems,
                'image_url' => $imageUrl,
                'image_webp_url' => $imageWebpUrl,
                'logo_url' => $project->logo_url ? Storage::disk('public')->url($project->logo_url) : null,
                'logo_is_light' => $project->logo_is_light,
                'video_url' => $project->video_url,
                'video_urls' => $videoUrls,
                'live_url' => $project->live_url,
                'github_url' => $project->github_url,
                'previous_project' => $previousProject ? [
                    'title' => $previousProject->title,
                    'slug' => $previousProject->slug,
                ] : null,
                'next_project' => $nextProject ? [
                    'title' => $nextProject->title,
                    'slug' => $nextProject->slug,
                ] : null,
            ],
        ]);
    }
}
