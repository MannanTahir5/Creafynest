<?php

namespace App\Http\Controllers;

use App\Models\AiServiceCard;
use App\Models\AiServiceSection;
use App\Models\CaseStudiesSection;
use App\Models\ConnectSection;
use App\Models\DeliveryCategory;
use App\Models\Hero;
use App\Models\HomePageSetting;
use App\Models\IndustriesSection;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use App\Models\TechnologySection;
use App\Models\Testimonial;
use App\Models\TestimonialsSection;
use App\Support\ContentCache;
use App\Support\DeliveryServiceLinker;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use Illuminate\Support\Facades\Cache;
use App\Support\PublicStorageUrl;
use App\Support\SiteAssets;
use Inertia\Inertia;
use Inertia\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $blocks = Cache::remember(ContentCache::HOME, ContentCache::TTL, function () {
            SiteAssets::publishPublicStatics();
            SiteAssets::ensureHomeHero();
            SiteAssets::ensureHeroAvatars();
            SiteAssets::ensureAiServiceCardImages();
            SiteAssets::ensureIndustryImages();
            SiteAssets::ensureDeliveryCategoryImages();

            $pageSettings = HomePageSetting::singleton();
            $visibility = $pageSettings->visibilityMap();

            $hero = $visibility['hero']
                ? (Hero::active() ?? Hero::query()->with(['avatars' => fn ($q) => $q->where('is_active', true)])->latest('updated_at')->first())
                : null;

            $servicesByCategory = Service::query()
                ->where('is_active', true)
                ->whereNotNull('delivery_category_id')
                ->get(['id', 'delivery_category_id', 'title', 'slug'])
                ->groupBy('delivery_category_id');

            $allServicesBySlug = Service::query()
                ->where('is_active', true)
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->get(['id', 'delivery_category_id', 'title', 'slug'])
                ->keyBy('slug');

            $deliveryCategories = $visibility['services']
                ? DeliveryCategory::query()
                ->where('is_active', true)
                ->with('items')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(function (DeliveryCategory $c) use ($servicesByCategory, $allServicesBySlug) {
                    $inCategory = $servicesByCategory->get($c->id) ?? collect();

                    return [
                        'slug' => $c->slug,
                        'title' => $c->title,
                        'subtitle' => $c->subtitle,
                        'featureIcon' => $c->feature_icon,
                        'image' => $c->imageUrl(),
                        'imageAlt' => $c->image_alt,
                        'items' => $c->items->map(function ($i) use ($inCategory, $allServicesBySlug) {
                            $service = DeliveryServiceLinker::resolveForItem($i, $allServicesBySlug, $inCategory);

                            return [
                                'id' => $i->id,
                                'title' => $i->title,
                                'description' => $i->description,
                                'icon' => $i->icon,
                                'image' => $i->imageUrl(),
                                'imageAlt' => $i->image_alt,
                                'service_slug' => $service?->slug,
                            ];
                        })->all(),
                    ];
                })
                ->take(6)
                ->all()
                : [];

            $technologySection = $visibility['technologies']
                ? TechnologySection::query()->where('is_active', true)->orderBy('id')->first()
                : null;
            $technologies = Technology::query()
                ->where('is_active', true)
                ->when($technologySection, fn ($q) => $q->where('technology_section_id', $technologySection->id))
                ->orderBy('row_index')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $serializeTech = fn (Technology $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'iconSrc' => $t->iconSrc(),
                'imageAlt' => $t->image_alt,
            ];

            $aiSection = $visibility['ai']
                ? AiServiceSection::query()->where('is_active', true)->orderBy('id')->first()
                : null;
            $aiCards = $aiSection
                ? $aiSection->cards()->where('is_active', true)->get()->map(fn (AiServiceCard $c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'description' => $c->description,
                    'href' => $c->href,
                    'icon' => $c->icon,
                    'color_theme' => $c->color_theme,
                    'image_url' => $c->imageUrl(),
                    'image_alt' => $c->image_alt,
                ])->all()
                : [];

            $industriesSection = $visibility['industries']
                ? IndustriesSection::query()->where('is_active', true)->orderBy('id')->first()
                : null;
            $industriesItems = $industriesSection
                ? $industriesSection->industries()->where('is_active', true)->get()->map(fn (Industry $i) => [
                    'id' => $i->id,
                    'title' => $i->title,
                    'icon' => $i->icon,
                    'image_url' => $i->imageUrl(),
                    'image_alt' => $i->image_alt,
                ])->all()
                : [];

            $connectSection = $visibility['connect']
                ? ConnectSection::query()->where('is_active', true)->orderBy('id')->first()
                : null;
            $caseStudiesSection = $visibility['case_studies']
                ? CaseStudiesSection::query()->where('is_active', true)->orderBy('id')->first()
                : null;
            $testimonialsSection = $visibility['testimonials']
                ? TestimonialsSection::query()->where('is_active', true)->orderBy('id')->first()
                : null;

            $featuredIds = $pageSettings->featuredTestimonialIdList();
            $testimonialsQuery = Testimonial::query();
            if ($featuredIds !== []) {
                $testimonials = $testimonialsQuery
                    ->whereIn('id', $featuredIds)
                    ->get()
                    ->sortBy(fn (Testimonial $t) => array_search($t->id, $featuredIds, true))
                    ->values();
            } else {
                $testimonials = $testimonialsQuery->latest()->limit(3)->get();
            }

            return [
                'servicesIntro' => $visibility['services'] ? [
                    'eyebrow' => $pageSettings->services_eyebrow,
                    'heading_lead' => $pageSettings->services_heading_lead,
                    'heading_highlight' => $pageSettings->services_heading_highlight,
                    'description' => $pageSettings->services_description,
                ] : null,
                'hero' => $hero ? [
                    'eyebrow' => $hero->eyebrow,
                    'trust_count' => $hero->trust_count,
                    'trust_text' => $hero->trust_text,
                    'heading_line_one' => $hero->heading_line_one,
                    'heading_line_two' => $hero->heading_line_two,
                    'heading_gradient' => $hero->heading_gradient,
                    'description' => $hero->description,
                    'image_url' => $hero->imageUrl(),
                    'image_alt' => $hero->image_alt,
                    'feature_lines' => $hero->feature_lines ?? [],
                    'primary_cta_label' => $hero->primary_cta_label,
                    'primary_cta_href' => $hero->primary_cta_href,
                    'secondary_cta_label' => $hero->secondary_cta_label,
                    'secondary_cta_href' => $hero->secondary_cta_href,
                    'avatars' => $hero->avatars->map(fn ($a) => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'image_url' => $a->imageUrl(),
                        'image_alt' => $a->image_alt,
                    ])->all(),
                ] : null,
                'deliveryCategories' => $deliveryCategories,
                'technologySection' => $technologySection ? [
                    'eyebrow' => $technologySection->eyebrow,
                    'heading_lead' => $technologySection->heading_lead,
                    'heading_highlight' => $technologySection->heading_highlight,
                    'description' => $technologySection->description,
                    'rowOne' => $technologies->where('row_index', 1)->values()->map($serializeTech)->all(),
                    'rowTwo' => $technologies->where('row_index', 2)->values()->map($serializeTech)->all(),
                ] : null,
                'aiServiceSection' => $aiSection ? [
                    'eyebrow' => $aiSection->eyebrow,
                    'heading' => $aiSection->heading,
                    'description' => $aiSection->description,
                    'cards' => $aiCards,
                ] : null,
                'industriesSection' => $industriesSection ? [
                    'eyebrow' => $industriesSection->eyebrow,
                    'heading_lead' => $industriesSection->heading_lead,
                    'heading_highlight' => $industriesSection->heading_highlight,
                    'description' => $industriesSection->description,
                    'industries' => $industriesItems,
                ] : null,
                'connectSection' => $connectSection ? [
                    'eyebrow' => $connectSection->eyebrow,
                    'heading' => $connectSection->heading,
                    'description' => $connectSection->description,
                    'submit_label' => $connectSection->submit_label,
                    'how_found_label' => $connectSection->how_found_label,
                    'idea_label' => $connectSection->idea_label,
                    'timeline_options' => $connectSection->timelineOptions(),
                    'service_options' => $connectSection->serviceOptions(),
                ] : null,
                'caseStudiesSection' => $caseStudiesSection ? [
                    'heading_lead' => $caseStudiesSection->heading_lead,
                    'heading_accent' => $caseStudiesSection->heading_accent,
                    'view_all_label' => $caseStudiesSection->view_all_label,
                    'view_all_href' => $caseStudiesSection->view_all_href,
                ] : null,
                'testimonialsSection' => $testimonialsSection ? [
                    'eyebrow' => $testimonialsSection->eyebrow,
                    'heading' => $testimonialsSection->heading,
                    'description' => $testimonialsSection->description,
                ] : null,
                'featuredProjects' => $visibility['case_studies']
                    ? Project::featuredCaseStudies($pageSettings->featuredProjectSlugList())
                    : [],
                'testimonials' => $visibility['testimonials']
                    ? $testimonials->map(fn (Testimonial $testimonial) => [
                        'name' => $testimonial->name,
                        'role' => $testimonial->role ?? '',
                        'feedback' => $testimonial->feedback,
                        'image' => PublicStorageUrl::url($testimonial->image)
                            ?? PublicStorageUrl::FALLBACK_AVATAR,
                    ])->all()
                    : [],
            ];
        });

        return Inertia::render('Home', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::HOME,
                '/',
                'Home',
                'Explore featured projects, services, and writing from a Laravel and Vue-focused developer portfolio.',
            ),
            ...$blocks,
        ]);
    }
}
