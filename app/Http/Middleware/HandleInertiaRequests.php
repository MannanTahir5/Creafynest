<?php

namespace App\Http\Middleware;

use App\Models\ContactPageSection;
use App\Models\FooterSetting;
use App\Models\SeoSiteSetting;
use App\Models\SiteSetting;
use App\Support\WebSettingsData;
use App\Support\Seo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function handle(Request $request, Closure $next): Response
    {
        $defaultBlogSearch = rtrim((string) config('app.url'), '/').'/blog?q={search_term_string}';

        $blogSearch = $defaultBlogSearch;
        if (Schema::hasTable('seo_site_settings')) {
            $site = SeoSiteSetting::cached();
            if ($site !== null && ! $site->enable_blog_search_action) {
                $blogSearch = null;
            }
        }

        $orgExtras = [
            'description' => null,
            'same_as' => [],
            'email' => null,
        ];
        if (Schema::hasTable('footer_settings')) {
            $orgExtras = FooterSetting::organizationExtrasForSchema();
        }

        View::share('globalSchemas', Seo::globalSchemas($orgExtras, $blogSearch));

        return parent::handle($request, $next);
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'site' => [
                'name' => config('app.name'),
                'url' => rtrim((string) config('app.url'), '/').'/',
            ],
            'footer' => fn () => FooterSetting::publicOrFallback(),
            'branding' => fn () => self::publicBranding(),
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'contactNewsletter' => fn () => self::contactNewsletter(),
        ];
    }

    /**
     * @return array{heading: string, subtitle: string, placeholder: string, buttonLabel: string}|null
     */
    private static function contactNewsletter(): ?array
    {
        if (! Schema::hasTable('contact_page_sections')) {
            return null;
        }

        $section = ContactPageSection::query()->orderBy('id')->first();

        if ($section === null || ! $section->is_active || ! $section->newsletter_is_active) {
            return null;
        }

        return [
            'heading' => filled($section->newsletter_heading) ? (string) $section->newsletter_heading : 'Newsletter',
            'subtitle' => filled($section->newsletter_subtitle) ? (string) $section->newsletter_subtitle : 'Get Global Updates. Subscribe Now.',
            'placeholder' => filled($section->newsletter_placeholder) ? (string) $section->newsletter_placeholder : 'Enter your email',
            'buttonLabel' => filled($section->newsletter_button_label) ? (string) $section->newsletter_button_label : 'Submit',
        ];
    }

    /**
     * @return array{header_logo_url: ?string, header_logo_alt: string, site_name: string}
     */
    private static function publicBranding(): array
    {
        $siteName = (string) config('app.name', 'Azee');
        $defaults = [
            'header_logo_url' => '/images/creafynest-logo.png',
            'header_logo_alt' => $siteName,
            'site_name' => $siteName,
        ];

        if (! Schema::hasTable('site_settings')) {
            return $defaults;
        }

        $row = SiteSetting::cached();
        $footer = FooterSetting::query()->first();

        return [
            'header_logo_url' => $row?->headerLogoUrl()
                ?? WebSettingsData::footerLogoUrl($footer)
                ?? $defaults['header_logo_url'],
            'header_logo_alt' => filled($row?->header_logo_alt)
                ? (string) $row->header_logo_alt
                : ($footer?->logo_alt ?: $siteName),
            'site_name' => $siteName,
        ];
    }
}
