<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Controller;
use App\Models\AiServiceCard;
use App\Models\AiServiceSection;
use App\Models\CaseStudiesSection;
use App\Models\ConnectSection;
use App\Models\DeliveryCategory;
use App\Models\Hero;
use App\Models\HeroAvatar;
use App\Models\HomePageSetting;
use App\Models\IndustriesSection;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Technology;
use App\Models\TechnologySection;
use App\Models\Testimonial;
use App\Models\TestimonialsSection;
use App\Support\ContentCache;
use App\Models\SeoPageSetting;
use App\Support\SeoPageKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class HomePageEditorController extends Controller
{
    public function edit(Request $request): Response
    {
        $settings = HomePageSetting::singleton();
        $activeHero = Hero::query()->where('is_active', true)->with('avatars')->first()
            ?? Hero::query()->with('avatars')->latest('updated_at')->first();

        $aiSection = AiServiceSection::singleton()->load('cards');
        $industriesSection = IndustriesSection::singleton()->load('industries');
        $techSection = TechnologySection::singleton()->load('technologies');
        $connect = ConnectSection::singleton();
        $caseStudies = CaseStudiesSection::singleton();
        $testimonialsSection = TestimonialsSection::singleton();

        $visibility = $settings->visibilityMap();

        return Inertia::render('Admin/HomePage/Edit', [
            'activeSection' => $request->query('section', 'overview'),
            'previewUrl' => url('/'),
            'settings' => [
                'services_eyebrow' => $settings->services_eyebrow,
                'services_heading_lead' => $settings->services_heading_lead,
                'services_heading_highlight' => $settings->services_heading_highlight,
                'services_description' => $settings->services_description,
                'featured_project_slugs' => $settings->featuredProjectSlugList(),
                'featured_testimonial_ids' => $settings->featuredTestimonialIdList(),
                'section_visibility' => $visibility,
            ],
            'seo' => [
                'edit_url' => route('admin.seo-settings.edit'),
                'meta_title' => SeoPageSetting::cachedFor(SeoPageKey::HOME)?->title_stem ?? 'Home',
            ],
            'sections' => [
                ['key' => 'hero', 'label' => 'Hero', 'visible' => $visibility['hero'], 'icon' => 'Sparkles'],
                ['key' => 'services', 'label' => 'Services carousel', 'visible' => $visibility['services'], 'icon' => 'Package'],
                ['key' => 'technologies', 'label' => 'Technologies', 'visible' => $visibility['technologies'], 'icon' => 'Cpu'],
                ['key' => 'ai', 'label' => 'AI services', 'visible' => $visibility['ai'], 'icon' => 'Bot'],
                ['key' => 'case_studies', 'label' => 'Case studies', 'visible' => $visibility['case_studies'], 'icon' => 'FolderKanban'],
                ['key' => 'industries', 'label' => 'Industries', 'visible' => $visibility['industries'], 'icon' => 'Building2'],
                ['key' => 'testimonials', 'label' => 'Testimonials', 'visible' => $visibility['testimonials'], 'icon' => 'MessageSquareQuote'],
                ['key' => 'connect', 'label' => "Let's connect", 'visible' => $visibility['connect'], 'icon' => 'Mail'],
            ],
            'hero' => $activeHero ? $this->serializeHero($activeHero) : null,
            'heroGradients' => HeroController::GRADIENTS,
            'deliveryCategories' => DeliveryCategory::query()
                ->withCount('items')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (DeliveryCategory $c) => [
                    'id' => $c->id,
                    'slug' => $c->slug,
                    'title' => $c->title,
                    'is_active' => $c->is_active,
                    'items_count' => $c->items_count,
                    'edit_url' => route('admin.delivery-categories.edit', $c),
                ])
                ->all(),
            'technologySection' => [
                'id' => $techSection->id,
                'eyebrow' => $techSection->eyebrow,
                'heading_lead' => $techSection->heading_lead,
                'heading_highlight' => $techSection->heading_highlight,
                'description' => $techSection->description,
                'is_active' => $techSection->is_active,
                'technologies' => $techSection->technologies->map(fn (Technology $t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'icon_slug' => $t->icon_slug,
                    'image_alt' => $t->image_alt,
                    'image_url' => $t->imageUrl(),
                    'row_index' => $t->row_index,
                    'sort_order' => $t->sort_order,
                    'is_active' => $t->is_active,
                ])->all(),
            ],
            'aiServiceSection' => [
                'eyebrow' => $aiSection->eyebrow,
                'heading' => $aiSection->heading,
                'description' => $aiSection->description,
                'is_active' => $aiSection->is_active,
                'cards' => $aiSection->cards->map(fn (AiServiceCard $c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'description' => $c->description,
                    'href' => $c->href,
                    'icon' => $c->icon,
                    'color_theme' => $c->color_theme,
                    'image_alt' => $c->image_alt,
                    'image_url' => $c->imageUrl(),
                    'is_active' => $c->is_active,
                ])->all(),
            ],
            'industriesSection' => [
                'eyebrow' => $industriesSection->eyebrow,
                'heading_lead' => $industriesSection->heading_lead,
                'heading_highlight' => $industriesSection->heading_highlight,
                'description' => $industriesSection->description,
                'is_active' => $industriesSection->is_active,
                'industries' => $industriesSection->industries->map(fn (Industry $i) => [
                    'id' => $i->id,
                    'title' => $i->title,
                    'icon' => $i->icon,
                    'image_alt' => $i->image_alt,
                    'image_url' => $i->imageUrl(),
                    'is_active' => $i->is_active,
                ])->all(),
            ],
            'caseStudiesSection' => [
                'heading_lead' => $caseStudies->heading_lead,
                'heading_accent' => $caseStudies->heading_accent,
                'view_all_label' => $caseStudies->view_all_label,
                'view_all_href' => $caseStudies->view_all_href,
                'is_active' => $caseStudies->is_active,
            ],
            'testimonialsSection' => [
                'eyebrow' => $testimonialsSection->eyebrow,
                'heading' => $testimonialsSection->heading,
                'description' => $testimonialsSection->description,
                'is_active' => $testimonialsSection->is_active,
            ],
            'connectSection' => [
                'eyebrow' => $connect->eyebrow,
                'heading' => $connect->heading,
                'description' => $connect->description,
                'submit_label' => $connect->submit_label,
                'how_found_label' => $connect->how_found_label,
                'idea_label' => $connect->idea_label,
                'timeline_options' => $connect->timelineOptions(),
                'service_options' => $connect->serviceOptions(),
                'is_active' => $connect->is_active,
            ],
            'projectPicker' => Project::query()
                ->orderBy('title')
                ->get(['id', 'title', 'slug', 'category'])
                ->map(fn (Project $p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'category' => $p->category,
                ])
                ->all(),
            'testimonialPicker' => Testimonial::query()
                ->orderByDesc('featured_on_home')
                ->orderBy('home_sort_order')
                ->orderByDesc('created_at')
                ->get(['id', 'name', 'role', 'feedback', 'featured_on_home'])
                ->map(fn (Testimonial $t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'role' => $t->role,
                    'excerpt' => (string) str($t->feedback)->limit(80),
                    'featured_on_home' => (bool) $t->featured_on_home,
                ])
                ->all(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'services_eyebrow' => ['nullable', 'string', 'max:120'],
            'services_heading_lead' => ['nullable', 'string', 'max:120'],
            'services_heading_highlight' => ['nullable', 'string', 'max:120'],
            'services_description' => ['nullable', 'string', 'max:600'],
            'featured_project_slugs' => ['array', 'max:12'],
            'featured_project_slugs.*' => ['string', 'max:190'],
            'featured_testimonial_ids' => ['array', 'max:12'],
            'featured_testimonial_ids.*' => ['integer', 'exists:testimonials,id'],
            'section_visibility' => ['array'],
            'section_visibility.*' => ['boolean'],
        ]);

        $settings = HomePageSetting::singleton();

        $visibility = [];
        foreach (HomePageSetting::SECTION_KEYS as $key) {
            $visibility[$key] = (bool) ($data['section_visibility'][$key] ?? true);
        }

        $settings->update([
            'services_eyebrow' => $data['services_eyebrow'] ?? null,
            'services_heading_lead' => $data['services_heading_lead'] ?? null,
            'services_heading_highlight' => $data['services_heading_highlight'] ?? null,
            'services_description' => $data['services_description'] ?? null,
            'featured_project_slugs' => array_values($data['featured_project_slugs'] ?? []),
            'featured_testimonial_ids' => array_values($data['featured_testimonial_ids'] ?? []),
            'section_visibility' => $visibility,
        ]);

        $this->syncSectionActiveFlags($visibility);
        $this->syncFeaturedTestimonials($settings->featuredTestimonialIdList());

        ContentCache::forget();

        return redirect()
            ->route('admin.home-page.edit', ['section' => $request->input('active_section', 'overview')])
            ->with('success', 'Home page settings saved.');
    }

    /**
     * @param  array<string, bool>  $visibility
     */
    private function syncSectionActiveFlags(array $visibility): void
    {
        DB::transaction(function () use ($visibility) {
            if (array_key_exists('hero', $visibility) && ! $visibility['hero']) {
                Hero::query()->update(['is_active' => false]);
            } elseif (array_key_exists('hero', $visibility) && $visibility['hero']) {
                $latest = Hero::query()->latest('updated_at')->first();
                if ($latest && ! Hero::query()->where('is_active', true)->exists()) {
                    $latest->activate();
                }
            }

            if (array_key_exists('services', $visibility)) {
                DeliveryCategory::query()->update(['is_active' => $visibility['services']]);
            }

            $singletonMap = [
                'technologies' => TechnologySection::class,
                'ai' => AiServiceSection::class,
                'case_studies' => CaseStudiesSection::class,
                'industries' => IndustriesSection::class,
                'testimonials' => TestimonialsSection::class,
                'connect' => ConnectSection::class,
            ];

            foreach ($singletonMap as $key => $model) {
                if (array_key_exists($key, $visibility)) {
                    $model::singleton()->update(['is_active' => $visibility[$key]]);
                }
            }
        });
    }

    /**
     * @param  list<int>  $featuredIds
     */
    private function syncFeaturedTestimonials(array $featuredIds): void
    {
        Testimonial::query()->update(['featured_on_home' => false, 'home_sort_order' => 0]);

        foreach ($featuredIds as $order => $id) {
            Testimonial::query()->whereKey($id)->update([
                'featured_on_home' => true,
                'home_sort_order' => $order,
            ]);
        }
    }

    private function serializeHero(Hero $hero): array
    {
        return array_merge($hero->toArray(), [
            'image_url' => $hero->imageUrl(),
            'avatars' => $hero->avatars->map(fn (HeroAvatar $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'image_alt' => $a->image_alt,
                'image_url' => $a->imageUrl(),
                'is_active' => $a->is_active,
            ])->all(),
        ]);
    }
}
