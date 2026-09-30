<?php

namespace App\Support;

use App\Models\AiServiceCard;
use App\Models\Blog;
use App\Models\DeliveryCategory;
use App\Models\DeliveryItem;
use App\Models\Hero;
use App\Models\HeroAvatar;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\SeoSiteSetting;
use App\Models\Technology;
use App\Support\WebpDerivative;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Ensures public and storage images exist across the marketing site.
 */
final class SiteAssets
{
    public static function ensureAll(bool $forceServiceHeroes = false): void
    {
        self::publishPublicStatics();
        self::ensureServiceHeroes($forceServiceHeroes);
        self::ensureProjectImages();
        self::ensureHomeHero();
        self::ensureHeroAvatars();
        self::ensureAiServiceCardImages();
        self::ensureIndustryImages();
        self::ensureTechnologyImages();
        self::ensureDeliveryCategoryImages();
        self::ensureBlogImages();
        self::ensureDefaultOgImage();
    }

    public static function publishPublicStatics(): void
    {
        $publicImages = public_path('images');
        if (! is_dir($publicImages)) {
            mkdir($publicImages, 0755, true);
        }

        $logoPath = $publicImages.DIRECTORY_SEPARATOR.'creafynest-logo.png';
        if (! is_file($logoPath)) {
            BrandedPlaceholderImage::ensureFile(
                $logoPath,
                'creafynest-logo',
                'Creafynest',
                BrandedPlaceholderImage::STYLE_LOGO,
            );
        }

        $heroSrc = resource_path('images/hero-devices.png');
        if (is_file($heroSrc)) {
            $dest = $publicImages.DIRECTORY_SEPARATOR.'hero-devices.png';
            if (! is_file($dest)) {
                File::copy($heroSrc, $dest);
            }
        }

        $caseHeroSrc = resource_path('images/case-studies-hero-mockups.png');
        if (is_file($caseHeroSrc)) {
            $dest = $publicImages.DIRECTORY_SEPARATOR.'case-studies-hero-mockups.png';
            if (! is_file($dest)) {
                File::copy($caseHeroSrc, $dest);
            }
        }
    }

    public static function ensureServiceHeroes(bool $force = false): void
    {
        Service::query()
            ->where('is_active', true)
            ->with('category:id,title,slug')
            ->orderBy('id')
            ->each(fn (Service $service) => self::ensureService($service, $force));
    }

    public static function ensureService(Service $service, bool $force = false): void
    {
        if (! $force && $service->hero_image_path && Storage::disk('public')->exists($service->hero_image_path)) {
            return;
        }

        $service->loadMissing('category:id,title,slug');

        $generated = ServicePlaceholderImage::ensureForService($service, $force);

        if ($generated && (! $service->hero_image_path || ! Storage::disk('public')->exists($service->hero_image_path))) {
            $service->update(['hero_image_path' => $generated]);
        }
    }

    public static function ensureProjectImages(): void
    {
        Project::query()->orderBy('id')->each(function (Project $project) {
            CaseStudyPlaceholderImage::ensure($project->slug, $project->title);

            if ($project->image && ! Storage::disk('public')->exists($project->image)) {
                $relative = 'projects/'.$project->slug.'/hero.jpg';
                if (BrandedPlaceholderImage::ensureFile(
                    storage_path('app/public/'.$relative),
                    'project-'.$project->slug,
                    $project->title,
                    BrandedPlaceholderImage::STYLE_CASE_STUDY,
                    $project->category,
                )) {
                    $project->update(['image' => $relative]);
                    WebpDerivative::encodeFromStoredPublicPath($relative);
                }
            }
        });
    }

    /** Home hero is text-only; no image asset is required. */
    public static function ensureHomeHero(): void
    {
    }

    public static function ensureHeroAvatars(): void
    {
        $hero = Hero::query()->orderBy('id')->first();
        if (! $hero) {
            return;
        }

        $names = ['Alex', 'Jordan', 'Sam', 'Taylor', 'Riley'];

        if ($hero->avatars()->where('is_active', true)->count() === 0) {
            foreach ($names as $i => $name) {
                HeroAvatar::query()->create([
                    'hero_id' => $hero->id,
                    'name' => $name,
                    'sort_order' => $i,
                    'is_active' => true,
                    'image_alt' => $name,
                ]);
            }
        }

        $hero->avatars()->where('is_active', true)->orderBy('sort_order')->each(function (HeroAvatar $avatar) {
            if ($avatar->image_path && Storage::disk('public')->exists($avatar->image_path)) {
                return;
            }

            $slug = 'avatar-'.($avatar->id ?: $avatar->name);
            $relative = 'home/avatars/'.$slug.'.jpg';
            $absolute = storage_path('app/public/'.$relative);

            if (BrandedPlaceholderImage::ensureFile(
                $absolute,
                $slug,
                $avatar->name ?: 'Team',
                BrandedPlaceholderImage::STYLE_AVATAR,
            )) {
                $avatar->update(['image_path' => $relative]);
                WebpDerivative::encodeFromStoredPublicPath($relative);
            }
        });
    }

    public static function ensureAiServiceCardImages(): void
    {
        AiServiceCard::query()->where('is_active', true)->orderBy('id')->each(function (AiServiceCard $card) {
            if ($card->image_path && Storage::disk('public')->exists($card->image_path)) {
                return;
            }

            $slug = 'ai-card-'.$card->id;
            $relative = 'home/ai-services/'.$slug.'.jpg';
            if (ServicePlaceholderImage::ensureStoredPath($relative, $slug, $card->title, 'AI Services')) {
                $card->update(['image_path' => $relative]);
            }
        });
    }

    public static function ensureIndustryImages(): void
    {
        Industry::query()->where('is_active', true)->orderBy('id')->each(function (Industry $row) {
            if ($row->image_path && Storage::disk('public')->exists($row->image_path)) {
                return;
            }

            $slug = 'industry-'.$row->id;
            $relative = 'home/industries/'.$slug.'.jpg';
            if (ServicePlaceholderImage::ensureStoredPath($relative, $slug, $row->title, 'Industries')) {
                $row->update(['image_path' => $relative]);
            }
        });
    }

    public static function ensureTechnologyImages(): void
    {
        Technology::query()->where('is_active', true)->orderBy('id')->each(function (Technology $row) {
            if ($row->image_path && Storage::disk('public')->exists($row->image_path)) {
                return;
            }

            $slug = 'tech-'.$row->id;
            $relative = 'home/technologies/'.$slug.'.jpg';
            if (ServicePlaceholderImage::ensureStoredPath($relative, $slug, $row->name, 'Technologies')) {
                $row->update(['image_path' => $relative]);
            }
        });
    }

    public static function ensureDeliveryCategoryImages(): void
    {
        DeliveryCategory::query()->where('is_active', true)->orderBy('id')->each(function (DeliveryCategory $cat) {
            if ($cat->image_path && Storage::disk('public')->exists($cat->image_path)) {
                return;
            }

            $relative = 'delivery/categories/'.$cat->slug.'.jpg';
            if (ServicePlaceholderImage::ensureStoredPath($relative, 'delivery-'.$cat->slug, $cat->title, 'Services')) {
                $cat->update(['image_path' => $relative]);
            }

            $cat->items()->orderBy('sort_order')->each(function (DeliveryItem $item) use ($cat) {
                if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                    return;
                }

                $relative = 'delivery/items/'.$cat->slug.'-'.$item->id.'.jpg';
                if (ServicePlaceholderImage::ensureStoredPath($relative, 'ditem-'.$item->id, $item->title, $cat->title)) {
                    $item->update(['image_path' => $relative]);
                }
            });
        });
    }

    public static function ensureBlogImages(): void
    {
        Blog::query()->orderBy('id')->each(function (Blog $blog) {
            $path = $blog->image;
            if ($path && Storage::disk('public')->exists($path)) {
                WebpDerivative::encodeFromStoredPublicPath($path);

                return;
            }

            $relative = 'blogs/'.$blog->slug.'/cover.jpg';
            $absolute = storage_path('app/public/'.$relative);

            if (BrandedPlaceholderImage::ensureFile(
                $absolute,
                'blog-'.$blog->slug,
                $blog->title,
                BrandedPlaceholderImage::STYLE_CARD,
                'Blog',
            )) {
                $blog->update(['image' => $relative]);
                WebpDerivative::encodeFromStoredPublicPath($relative);
            }
        });
    }

    public static function ensureDefaultOgImage(): void
    {
        $site = SeoSiteSetting::query()->orderBy('id')->first();
        if (! $site) {
            return;
        }

        $relative = $site->default_og_image_path;
        if ($relative && Storage::disk('public')->exists($relative)) {
            return;
        }

        $relative = 'seo/default-og.jpg';
        $absolute = storage_path('app/public/'.$relative);

        if (BrandedPlaceholderImage::ensureFile(
            $absolute,
            'default-og',
            config('app.name', 'Creafynest'),
            BrandedPlaceholderImage::STYLE_OG,
            'Digital agency',
        )) {
            WebpDerivative::encodeFromStoredPublicPath($relative);
            $site->update([
                'default_og_image_path' => $relative,
                'default_og_image_alt' => $site->default_og_image_alt ?: config('app.name', 'Creafynest'),
            ]);
            SeoSiteSetting::forgetCache();
        }
    }
}
