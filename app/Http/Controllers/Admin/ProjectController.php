<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\ContentCache;
use App\Support\UniqueSlug;
use App\Support\WebpDerivative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $q = (string) $request->query('q', '');

        $projects = Project::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('title', 'like', '%'.$q.'%')
                        ->orWhere('slug', 'like', '%'.$q.'%')
                        ->orWhere('category', 'like', '%'.$q.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Projects/Index', [
            'filters' => ['q' => $q],
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Projects/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProject($request);

        $slug = $data['slug'] !== ''
            ? UniqueSlug::for('projects', $data['title'], $data['slug'])
            : UniqueSlug::for('projects', $data['title']);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('projects', 'public');
            WebpDerivative::encodeFromStoredPublicPath($imagePath);
        }

        $logoPath = null;
        if ($request->hasFile('logo_image') && $request->file('logo_image')->isValid()) {
            $logoPath = $request->file('logo_image')->store('projects/logo', 'public');
            WebpDerivative::encodeFromStoredPublicPath($logoPath);
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ((array) $request->file('gallery') as $file) {
                if ($file && $file->isValid()) {
                    $stored = $file->store('projects/gallery', 'public');
                    WebpDerivative::encodeFromStoredPublicPath($stored);
                    $galleryPaths[] = $stored;
                }
            }
        }

        $howItStartedImagePath = null;
        if ($request->hasFile('how_it_started_image') && $request->file('how_it_started_image')->isValid()) {
            $howItStartedImagePath = $request->file('how_it_started_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($howItStartedImagePath);
        }

        $challengeImagePath = null;
        if ($request->hasFile('challenge_image') && $request->file('challenge_image')->isValid()) {
            $challengeImagePath = $request->file('challenge_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($challengeImagePath);
        }

        $approachImagePath = null;
        if ($request->hasFile('approach_image') && $request->file('approach_image')->isValid()) {
            $approachImagePath = $request->file('approach_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($approachImagePath);
        }

        $resultsImagePath = null;
        if ($request->hasFile('results_image') && $request->file('results_image')->isValid()) {
            $resultsImagePath = $request->file('results_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($resultsImagePath);
        }

        $videoPaths = [];
        if ($request->hasFile('video_files')) {
            foreach ((array) $request->file('video_files') as $file) {
                if ($file && $file->isValid()) {
                    $videoPaths[] = $file->store('projects/videos', 'public');
                }
            }
        }

        $videoUrls = collect(preg_split('/\r\n|\n|\r/', (string) ($data['video_urls'] ?? '')))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->all();

        $videoList = array_merge($videoUrls, $videoPaths);

        Project::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'category' => $data['category'],
            'description' => $data['description'],
            'client_name' => $data['client_name'],
            'location' => $data['location'],
            'industry' => $data['industry'],
            'services' => $data['services'],
            'problem_title' => $data['problem_title'],
            'problem_text' => $data['problem_text'],
            'how_it_started_title' => $data['how_it_started_title'] ?? $data['problem_title'],
            'how_it_started_text' => $data['how_it_started_text'] ?? $data['problem_text'],
            'how_it_started_image' => $howItStartedImagePath,
            'challenge_title' => $data['challenge_title'] ?? $data['system_title'] ?? $data['problem_title'],
            'challenge_text' => $data['challenge_text'] ?? $data['system_text'] ?? $data['problem_text'],
            'challenge_image' => $challengeImagePath,
            'approach_title' => $data['approach_title'],
            'approach_text' => $data['approach_text'],
            'approach_image' => $approachImagePath,
            'system_title' => $data['system_title'],
            'system_text' => $data['system_text'],
            'repeat_title' => $data['repeat_title'],
            'repeat_text' => $data['repeat_text'],
            'results_title' => $data['results_title'] ?? $data['repeat_title'] ?? $data['next_title'],
            'results_text' => $data['results_text'] ?? $data['repeat_text'] ?? $data['next_text'],
            'results_image' => $resultsImagePath,
            'next_title' => $data['next_title'],
            'next_text' => $data['next_text'],
            'tech_stack' => $data['tech_stack'],
            'image' => $imagePath,
            'gallery' => $galleryPaths ?: null,
            'logo_url' => $logoPath ?: ($data['logo_url'] ?? null),
            'logo_is_light' => $data['logo_is_light'] ?? false,
            'video_url' => $data['video_url'] ?? null,
            'video_urls' => $videoList ?: null,
            'live_url' => $data['live_url'],
        ]);

        ContentCache::forget();

        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project): Response
    {
        // Helper: convert a storage-disk relative path to a public URL for preview.
        $storageUrl = fn (?string $path): ?string => $path
            ? Storage::disk('public')->url($path)
            : null;

        // Gallery: each item may be a storage path or an already-absolute URL.
        $galleryPreviews = collect($project->gallery ?? [])
            ->map(fn (string $path) => [
                'path' => $path,
                'url'  => str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path),
            ])
            ->values()
            ->all();

        $videoPreviews = collect($project->video_urls ?? [])
            ->map(fn (string $path) => [
                'path' => $path,
                'url'  => preg_match('/^https?:\/\//i', $path) || str_starts_with($path, '//')
                    ? $path
                    : Storage::disk('public')->url($path),
            ])
            ->values()
            ->all();

        return Inertia::render('Admin/Projects/Edit', [
            'project' => [
                'id'                      => $project->id,
                'title'                   => $project->title,
                'slug'                    => $project->slug,
                'category'                => $project->category,
                'description'             => $project->description,
                'client_name'             => $project->client_name,
                'location'                => $project->location,
                'industry'                => $project->industry,
                'services'                => implode(', ', $project->services ?? []),
                'problem_title'           => $project->problem_title,
                'problem_text'            => $project->problem_text,
                'how_it_started_title'    => $project->how_it_started_title ?? $project->problem_title,
                'how_it_started_text'     => $project->how_it_started_text ?? $project->problem_text,
                'how_it_started_image'    => $project->how_it_started_image,
                'how_it_started_image_url'=> $storageUrl($project->how_it_started_image),
                'challenge_title'         => $project->challenge_title ?? $project->system_title ?? $project->problem_title,
                'challenge_text'          => $project->challenge_text ?? $project->system_text ?? $project->problem_text,
                'challenge_image'         => $project->challenge_image,
                'challenge_image_url'     => $storageUrl($project->challenge_image),
                'approach_title'          => $project->approach_title,
                'approach_text'           => $project->approach_text,
                'approach_image'          => $project->approach_image,
                'approach_image_url'      => $storageUrl($project->approach_image),
                'system_title'            => $project->system_title,
                'system_text'             => $project->system_text,
                'repeat_title'            => $project->repeat_title,
                'repeat_text'             => $project->repeat_text,
                'results_title'           => $project->results_title ?? $project->repeat_title ?? $project->next_title,
                'results_text'            => $project->results_text ?? $project->repeat_text ?? $project->next_text,
                'results_image'           => $project->results_image,
                'results_image_url'       => $storageUrl($project->results_image),
                'next_title'              => $project->next_title,
                'next_text'               => $project->next_text,
                'tech_csv'                => implode(', ', $project->tech_stack ?? []),
                'logo_url'                => $project->logo_url,
                'logo_preview_url'        => $storageUrl($project->logo_url) ?? $project->logo_url,
                'logo_is_light'           => $project->logo_is_light,
                'video_url'               => $project->video_url,
                'video_urls'              => $project->video_urls ?? [],
                'video_previews'          => $videoPreviews,
                'live_url'                => $project->live_url,
                'github_url'              => $project->github_url,
                'image'                   => $project->image,
                'image_url'               => $storageUrl($project->image),
                'gallery'                 => array_column($galleryPreviews, 'path'),
                'gallery_previews'        => $galleryPreviews,
            ],
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validatedProject($request);

        $slug = $data['slug'] !== ''
            ? UniqueSlug::for('projects', $data['title'], $data['slug'], $project->id)
            : UniqueSlug::for('projects', $data['title'], $project->slug, $project->id);

        $imagePath = $project->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($project->image) {
                WebpDerivative::deleteForOriginal($project->image);
                Storage::disk('public')->delete($project->image);
            }
            $imagePath = $request->file('image')->store('projects', 'public');
            WebpDerivative::encodeFromStoredPublicPath($imagePath);
        }

        $logoPath = $project->logo_url;
        if ($request->hasFile('logo_image') && $request->file('logo_image')->isValid()) {
            if ($project->logo_url) {
                WebpDerivative::deleteForOriginal($project->logo_url);
                Storage::disk('public')->delete($project->logo_url);
            }
            $logoPath = $request->file('logo_image')->store('projects/logo', 'public');
            WebpDerivative::encodeFromStoredPublicPath($logoPath);
        }

        $keepGallery = $request->input('keep_gallery', []);
        if (! is_array($keepGallery)) {
            $keepGallery = [];
        }

        $existingGallery = $project->gallery ?? [];
        $newGallery = array_values(array_intersect($existingGallery, $keepGallery));

        if ($request->hasFile('gallery')) {
            foreach ((array) $request->file('gallery') as $file) {
                if ($file && $file->isValid()) {
                    $stored = $file->store('projects/gallery', 'public');
                    WebpDerivative::encodeFromStoredPublicPath($stored);
                    $newGallery[] = $stored;
                }
            }
        }

        $removed = array_diff($existingGallery, $newGallery);
        foreach ($removed as $path) {
            WebpDerivative::deleteForOriginal($path);
            Storage::disk('public')->delete($path);
        }

        $howItStartedImagePath = $project->how_it_started_image;
        if ($request->hasFile('how_it_started_image') && $request->file('how_it_started_image')->isValid()) {
            if ($project->how_it_started_image) {
                WebpDerivative::deleteForOriginal($project->how_it_started_image);
                Storage::disk('public')->delete($project->how_it_started_image);
            }
            $howItStartedImagePath = $request->file('how_it_started_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($howItStartedImagePath);
        }

        $challengeImagePath = $project->challenge_image;
        if ($request->hasFile('challenge_image') && $request->file('challenge_image')->isValid()) {
            if ($project->challenge_image) {
                WebpDerivative::deleteForOriginal($project->challenge_image);
                Storage::disk('public')->delete($project->challenge_image);
            }
            $challengeImagePath = $request->file('challenge_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($challengeImagePath);
        }

        $approachImagePath = $project->approach_image;
        if ($request->hasFile('approach_image') && $request->file('approach_image')->isValid()) {
            if ($project->approach_image) {
                WebpDerivative::deleteForOriginal($project->approach_image);
                Storage::disk('public')->delete($project->approach_image);
            }
            $approachImagePath = $request->file('approach_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($approachImagePath);
        }

        $resultsImagePath = $project->results_image;
        if ($request->hasFile('results_image') && $request->file('results_image')->isValid()) {
            if ($project->results_image) {
                WebpDerivative::deleteForOriginal($project->results_image);
                Storage::disk('public')->delete($project->results_image);
            }
            $resultsImagePath = $request->file('results_image')->store('projects/story', 'public');
            WebpDerivative::encodeFromStoredPublicPath($resultsImagePath);
        }

        $videoPaths = [];
        if ($request->hasFile('video_files')) {
            foreach ((array) $request->file('video_files') as $file) {
                if ($file && $file->isValid()) {
                    $videoPaths[] = $file->store('projects/videos', 'public');
                }
            }
        }

        $videoUrls = collect(preg_split('/\r\n|\n|\r/', (string) ($data['video_urls'] ?? '')))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->all();

        $keepVideos = $request->input('keep_videos');
        if (is_array($keepVideos)) {
            $keptExistingVideos = array_values(array_intersect($project->video_urls ?? [], $keepVideos));
            $removedVideos = array_diff($project->video_urls ?? [], $keepVideos);
            foreach ($removedVideos as $oldVideo) {
                if (str_starts_with($oldVideo, 'projects/videos/')) {
                    Storage::disk('public')->delete($oldVideo);
                }
            }
        } else {
            $keptExistingVideos = $request->has('keep_videos') ? [] : ($project->video_urls ?? []);
        }

        $hasVideoChanges = $request->hasFile('video_files') || $request->filled('video_urls') || $request->has('keep_videos');
        $videoList = $hasVideoChanges
            ? array_values(array_unique(array_merge($keptExistingVideos, $videoUrls, $videoPaths)))
            : ($project->video_urls ?? []);

        $project->update([
            'title' => $data['title'],
            'slug' => $slug,
            'category' => $data['category'],
            'description' => $data['description'],
            'client_name' => $data['client_name'],
            'location' => $data['location'],
            'industry' => $data['industry'],
            'services' => $data['services'],
            'problem_title' => $data['problem_title'],
            'problem_text' => $data['problem_text'],
            'how_it_started_title' => $data['how_it_started_title'] ?? $data['problem_title'],
            'how_it_started_text' => $data['how_it_started_text'] ?? $data['problem_text'],
            'how_it_started_image' => $howItStartedImagePath,
            'challenge_title' => $data['challenge_title'] ?? $data['system_title'] ?? $data['problem_title'],
            'challenge_text' => $data['challenge_text'] ?? $data['system_text'] ?? $data['problem_text'],
            'challenge_image' => $challengeImagePath,
            'approach_title' => $data['approach_title'],
            'approach_text' => $data['approach_text'],
            'approach_image' => $approachImagePath,
            'system_title' => $data['system_title'],
            'system_text' => $data['system_text'],
            'repeat_title' => $data['repeat_title'],
            'repeat_text' => $data['repeat_text'],
            'results_title' => $data['results_title'] ?? $data['repeat_title'] ?? $data['next_title'],
            'results_text' => $data['results_text'] ?? $data['repeat_text'] ?? $data['next_text'],
            'results_image' => $resultsImagePath,
            'next_title' => $data['next_title'],
            'next_text' => $data['next_text'],
            'tech_stack' => $data['tech_stack'],
            'image' => $imagePath,
            'gallery' => $newGallery ?: null,
            'logo_url' => $logoPath ?: ($data['logo_url'] ?? $project->logo_url),
            'logo_is_light' => $data['logo_is_light'] ?? false,
            'video_url' => $data['video_url'] ?? $project->video_url,
            'video_urls' => $videoList ?: null,
            'live_url' => $data['live_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
        ]);

        ContentCache::forget();

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->image) {
            WebpDerivative::deleteForOriginal($project->image);
            Storage::disk('public')->delete($project->image);
        }
        foreach ($project->gallery ?? [] as $path) {
            WebpDerivative::deleteForOriginal($path);
            Storage::disk('public')->delete($path);
        }

        $project->delete();

        ContentCache::forget();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    /**
     * @return array{
     *   title: string,
     *   slug: string,
     *   category: string,
     *   description: string,
     *   client_name: ?string,
     *   location: ?string,
     *   industry: ?string,
     *   services: array<int, string>,
     *   problem_title: ?string,
     *   problem_text: ?string,
     *   approach_title: ?string,
     *   approach_text: ?string,
     *   system_title: ?string,
     *   system_text: ?string,
     *   repeat_title: ?string,
     *   repeat_text: ?string,
     *   next_title: ?string,
     *   next_text: ?string,
     *   tech_stack: array<int, string>,
     *   logo_url: ?string,
     *   live_url: ?string,
     *   github_url: ?string
     * }
     */
    private function validatedProject(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190'],
            'category' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:190'],
            'location' => ['nullable', 'string', 'max:190'],
            'industry' => ['nullable', 'string', 'max:190'],
            'services' => ['nullable', 'string', 'max:2000'],
            'problem_title' => ['nullable', 'string', 'max:190'],
            'problem_text' => ['nullable', 'string'],
            'how_it_started_title' => ['nullable', 'string', 'max:190'],
            'how_it_started_text' => ['nullable', 'string'],
            'how_it_started_image' => ['nullable', 'image', 'max:5120'],
            'challenge_title' => ['nullable', 'string', 'max:190'],
            'challenge_text' => ['nullable', 'string'],
            'challenge_image' => ['nullable', 'image', 'max:5120'],
            'approach_title' => ['nullable', 'string', 'max:190'],
            'approach_text' => ['nullable', 'string'],
            'approach_image' => ['nullable', 'image', 'max:5120'],
            'system_title' => ['nullable', 'string', 'max:190'],
            'system_text' => ['nullable', 'string'],
            'repeat_title' => ['nullable', 'string', 'max:190'],
            'repeat_text' => ['nullable', 'string'],
            'results_title' => ['nullable', 'string', 'max:190'],
            'results_text' => ['nullable', 'string'],
            'results_image' => ['nullable', 'image', 'max:5120'],
            'next_title' => ['nullable', 'string', 'max:190'],
            'next_text' => ['nullable', 'string'],
            'tech_csv' => ['nullable', 'string', 'max:2000'],
            'logo_image' => ['nullable', 'image', 'max:5120'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_is_light' => ['boolean'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'video_urls' => ['nullable', 'string'],
            'video_files' => ['nullable', 'array'],
            'video_files.*' => ['file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm,video/avi,video/x-matroska', 'max:102400'],
            'live_url' => ['nullable', 'string', 'max:500'],
            'github_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:5120'],
        ]);

        $tech = [];
        if (! empty($validated['tech_csv'])) {
            $tech = array_values(array_filter(array_map('trim', explode(',', $validated['tech_csv']))));
        }

        $services = [];
        if (! empty($validated['services'])) {
            $services = array_values(array_filter(array_map('trim', explode(',', $validated['services']))));
        }

        return [
            'title' => $validated['title'],
            'slug' => trim((string) ($validated['slug'] ?? '')),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'client_name' => $validated['client_name'] ?? null,
            'location' => $validated['location'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'services' => $services,
            'problem_title' => $validated['problem_title'] ?? null,
            'problem_text' => $validated['problem_text'] ?? null,
            'how_it_started_title' => $validated['how_it_started_title'] ?? null,
            'how_it_started_text' => $validated['how_it_started_text'] ?? null,
            'challenge_title' => $validated['challenge_title'] ?? null,
            'challenge_text' => $validated['challenge_text'] ?? null,
            'approach_title' => $validated['approach_title'] ?? null,
            'approach_text' => $validated['approach_text'] ?? null,
            'system_title' => $validated['system_title'] ?? null,
            'system_text' => $validated['system_text'] ?? null,
            'repeat_title' => $validated['repeat_title'] ?? null,
            'repeat_text' => $validated['repeat_text'] ?? null,
            'results_title' => $validated['results_title'] ?? null,
            'results_text' => $validated['results_text'] ?? null,
            'next_title' => $validated['next_title'] ?? null,
            'next_text' => $validated['next_text'] ?? null,
            'tech_stack' => $tech,
            'logo_url' => $validated['logo_url'] ?? null,
            'logo_is_light' => $validated['logo_is_light'] ?? false,
            'video_url' => $validated['video_url'] ?? null,
            'video_urls' => $validated['video_urls'] ?? null,
            'live_url' => $validated['live_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
        ];
    }
}
