<?php

namespace App\Models;

use App\Support\WebpDerivative;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @phpstan-type CaseStudyPayload array{
 *   title: string,
 *   slug: string,
 *   category: string,
 *   excerpt: string,
 *   tech: array<int, string>,
 *   image_url: ?string,
 *   image_webp_url: ?string,
 *   image_alt: string
 * }
 */
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'client_name',
        'location',
        'industry',
        'services',
        'problem_title',
        'problem_text',
        'how_it_started_title',
        'how_it_started_text',
        'how_it_started_image',
        'challenge_title',
        'challenge_text',
        'challenge_image',
        'approach_title',
        'approach_text',
        'approach_image',
        'system_title',
        'system_text',
        'repeat_title',
        'repeat_text',
        'results_title',
        'results_text',
        'results_image',
        'next_title',
        'next_text',
        'tech_stack',
        'image',
        'logo_url',
        'logo_is_light',
        'gallery',
        'video_url',
        'video_urls',
        'live_url',
        'github_url',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'services' => 'array',
            'gallery' => 'array',
            'video_urls' => 'array',
            'logo_is_light' => 'boolean',
        ];
    }

    /**
     * Card / hero image URLs (matches portfolio grid resolution).
     *
     * @return array{url: ?string, webp_url: ?string}
     */
    public static function caseStudyCardImages(self $project): array
    {
        if ($project->image && Storage::disk('public')->exists($project->image)) {
            return [
                'url' => WebpDerivative::publicDiskRelativeUrl($project->image),
                'webp_url' => WebpDerivative::publicWebpRelativeUrlIfExists($project->image),
            ];
        }

        $slugJpg = public_path('images/case-studies/'.$project->slug.'.jpg');
        if (is_file($slugJpg)) {
            return [
                'url' => '/images/case-studies/'.$project->slug.'.jpg',
                'webp_url' => null,
            ];
        }

        return ['url' => null, 'webp_url' => null];
    }

    /**
     * @return CaseStudyPayload
     */
    public static function toCaseStudyPayload(self $project): array
    {
        $images = self::caseStudyCardImages($project);

        return [
            'title' => $project->title,
            'slug' => $project->slug,
            'category' => $project->category,
            'excerpt' => (string) str($project->description)->limit(160),
            'tech' => $project->tech_stack ?? [],
            'image_url' => $images['url'],
            'image_webp_url' => $images['webp_url'],
            'image_alt' => $project->title,
        ];
    }

    /**
     * Up to five items: curated case studies by slug order, then latest other projects.
     *
     * @return list<CaseStudyPayload>
     */
    public static function featuredCaseStudies(?array $slugOrder = null): array
    {
        $slugOrder = $slugOrder ?? \App\Models\HomePageSetting::singleton()->featuredProjectSlugList();

        if (!empty($slugOrder)) {
            $bySlug = self::query()->whereIn('slug', $slugOrder)->get()->keyBy('slug');

            $ordered = collect($slugOrder)
                ->map(fn (string $slug) => $bySlug->get($slug))
                ->filter();

            // Append any projects not in the curated list (latest first)
            $rest = self::query()
                ->whereNotIn('slug', $slugOrder)
                ->latest()
                ->get();

            return $ordered
                ->concat($rest)
                ->map(fn (self $project) => self::toCaseStudyPayload($project))
                ->values()
                ->all();
        }

        // No curated list — return all projects, latest first
        return self::query()
            ->latest()
            ->get()
            ->map(fn (self $project) => self::toCaseStudyPayload($project))
            ->values()
            ->all();
    }

    /**
     * Most recently added projects for listing surfaces (e.g. services page).
     *
     * @return list<CaseStudyPayload>
     */
    public static function recentCaseStudies(int $limit = 5): array
    {
        return self::query()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (self $project) => self::toCaseStudyPayload($project))
            ->values()
            ->all();
    }

}
