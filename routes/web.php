<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogDetailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ServiceDetailController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeliveryCategoryController as AdminDeliveryCategoryController;
use App\Http\Controllers\Admin\HeroController as AdminHeroController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\AboutPageController as AdminAboutPageController;
use App\Http\Controllers\Admin\AiServiceSectionController as AdminAiServiceSectionController;
use App\Http\Controllers\Admin\CaseStudiesSectionController as AdminCaseStudiesSectionController;
use App\Http\Controllers\Admin\ConnectSectionController as AdminConnectSectionController;
use App\Http\Controllers\Admin\FooterSettingController as AdminFooterSettingController;
use App\Http\Controllers\Admin\SeoSettingsController as AdminSeoSettingsController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\Admin\ContactPageController as AdminContactPageController;
use App\Http\Controllers\Admin\HomePageEditorController as AdminHomePageEditorController;
use App\Http\Controllers\Admin\HomeSectionController as AdminHomeSectionController;
use App\Http\Controllers\Admin\IndustriesSectionController as AdminIndustriesSectionController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TechnologySectionController as AdminTechnologySectionController;
use App\Http\Controllers\Admin\TestimonialsSectionController as AdminTestimonialsSectionController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\WebSettingsEditorController as AdminWebSettingsEditorController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Support\Seo;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class);
Route::get('/about', AboutController::class);

Route::get('/portfolio', PortfolioController::class);
Route::get('/portfolio/{project:slug}', ProjectController::class);

Route::get('/services', ServicesController::class);
Route::get('/services/{service:slug}', ServiceDetailController::class);

Route::get('/blog', BlogController::class);
Route::get('/blog/{blog:slug}', BlogDetailController::class);

Route::get('/contact', [ContactController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

Route::post('/newsletter', [NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:10,1');

Route::get('/robots.txt', RobotsController::class);

require __DIR__.'/auth.php';

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', AdminProjectController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('blogs', AdminBlogController::class)->except(['show']);
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);

    Route::resource('heroes', AdminHeroController::class)->except(['show']);
    Route::post('heroes/{hero}/activate', [AdminHeroController::class, 'activate'])->name('heroes.activate');

    Route::resource('delivery-categories', AdminDeliveryCategoryController::class)
        ->parameters(['delivery-categories' => 'deliveryCategory'])
        ->except(['show']);

    Route::get('technology-section', [AdminTechnologySectionController::class, 'edit'])
        ->name('technology-section.edit');
    Route::match(['put', 'patch'], 'technology-section', [AdminTechnologySectionController::class, 'update'])
        ->name('technology-section.update');

    Route::get('home-page', [AdminHomePageEditorController::class, 'edit'])
        ->name('home-page.edit');
    Route::match(['put', 'patch'], 'home-page/settings', [AdminHomePageEditorController::class, 'updateSettings'])
        ->name('home-page.settings.update');

    Route::redirect('home-sections', '/admin/home-page')->name('home-sections.index');

    Route::prefix('home-sections')->name('home-sections.')->group(function () {
        Route::get('ai-services', [AdminAiServiceSectionController::class, 'edit'])->name('ai-services.edit');
        Route::match(['put', 'patch'], 'ai-services', [AdminAiServiceSectionController::class, 'update'])->name('ai-services.update');

        Route::get('industries', [AdminIndustriesSectionController::class, 'edit'])->name('industries.edit');
        Route::match(['put', 'patch'], 'industries', [AdminIndustriesSectionController::class, 'update'])->name('industries.update');

        Route::get('connect', [AdminConnectSectionController::class, 'edit'])->name('connect.edit');
        Route::match(['put', 'patch'], 'connect', [AdminConnectSectionController::class, 'update'])->name('connect.update');

        Route::get('case-studies', [AdminCaseStudiesSectionController::class, 'edit'])->name('case-studies.edit');
        Route::match(['put', 'patch'], 'case-studies', [AdminCaseStudiesSectionController::class, 'update'])->name('case-studies.update');

        Route::get('testimonials', [AdminTestimonialsSectionController::class, 'edit'])->name('testimonials.edit');
        Route::match(['put', 'patch'], 'testimonials', [AdminTestimonialsSectionController::class, 'update'])->name('testimonials.update');
    });

    Route::get('about-page', [AdminAboutPageController::class, 'edit'])->name('about-page.edit');
    Route::match(['put', 'patch'], 'about-page', [AdminAboutPageController::class, 'update'])->name('about-page.update');

    Route::get('contact-page', [AdminContactPageController::class, 'edit'])->name('contact-page.edit');
    Route::match(['put', 'patch'], 'contact-page', [AdminContactPageController::class, 'update'])->name('contact-page.update');

    Route::get('footer', [AdminFooterSettingController::class, 'edit'])->name('footer.edit');
    Route::match(['put', 'patch'], 'footer', [AdminFooterSettingController::class, 'update'])->name('footer.update');

    Route::redirect('seo-settings', '/admin/web-settings?tab=seo');
    Route::get('seo-settings', [AdminSeoSettingsController::class, 'edit'])->name('seo-settings.edit');
    Route::match(['put', 'patch'], 'seo-settings', [AdminSeoSettingsController::class, 'update'])->name('seo-settings.update');

    Route::get('web-settings', [AdminWebSettingsEditorController::class, 'edit'])->name('web-settings.edit');
    Route::post('web-settings/branding', [AdminWebSettingsEditorController::class, 'updateBranding'])->name('web-settings.branding.update');
    Route::match(['put', 'patch'], 'web-settings/tracking', [AdminWebSettingsEditorController::class, 'updateTracking'])->name('web-settings.tracking.update');
    Route::match(['put', 'patch'], 'web-settings/smtp', [AdminWebSettingsEditorController::class, 'updateSmtp'])->name('web-settings.smtp.update');
    Route::match(['put', 'patch'], 'web-settings/sitemap', [AdminWebSettingsEditorController::class, 'updateSitemap'])->name('web-settings.sitemap.update');
    Route::match(['put', 'patch'], 'web-settings/social', [AdminWebSettingsEditorController::class, 'updateSocial'])->name('web-settings.social.update');
    Route::match(['put', 'patch'], 'web-settings/other', [AdminWebSettingsEditorController::class, 'updateOther'])->name('web-settings.other.update');
    Route::match(['put', 'patch'], 'web-settings/contact-seo', [AdminWebSettingsEditorController::class, 'updateContactSeo'])->name('web-settings.contact-seo.update');
    Route::match(['put', 'patch'], 'web-settings/seo', [AdminWebSettingsEditorController::class, 'updateSeo'])->name('web-settings.seo.update');

    Route::redirect('site-settings', '/admin/web-settings?tab=other');
    Route::redirect('site_settings', '/admin/web-settings?tab=other');
    Route::get('site-settings', [AdminSiteSettingsController::class, 'edit'])->name('site-settings.edit');
    Route::match(['put', 'patch'], 'site-settings', [AdminSiteSettingsController::class, 'update'])->name('site-settings.update');

    Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['put', 'patch'], 'profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::match(['put', 'patch'], 'profile/password', [AdminProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::get('newsletter', [AdminNewsletterSubscriberController::class, 'index'])->name('newsletter.index');
    Route::delete('newsletter/{subscriber}', [AdminNewsletterSubscriberController::class, 'destroy'])->name('newsletter.destroy');
});

Route::fallback(function () {
    $path = request()->path();

    return Inertia::render('Errors/NotFound', [
        'seo' => Seo::page(
            'Page not found',
            'The page you are looking for does not exist or may have moved.',
            $path === '' ? '404' : $path,
            null,
            'website',
            ['noindex' => true],
        ),
    ])
        ->toResponse(request())
        ->setStatusCode(404);
});
