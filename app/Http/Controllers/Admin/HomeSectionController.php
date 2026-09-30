<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceSection;
use App\Models\CaseStudiesSection;
use App\Models\ConnectSection;
use App\Models\IndustriesSection;
use App\Models\TestimonialsSection;
use Inertia\Inertia;
use Inertia\Response;

class HomeSectionController extends Controller
{
    public function index(): Response
    {
        $aiSection = AiServiceSection::singleton()->loadCount('cards');
        $industriesSection = IndustriesSection::singleton()->loadCount('industries');
        $connect = ConnectSection::singleton();
        $caseStudies = CaseStudiesSection::singleton();
        $testimonials = TestimonialsSection::singleton();

        return Inertia::render('Admin/HomeSections/Index', [
            'sections' => [
                [
                    'key' => 'ai-services',
                    'name' => 'AI services',
                    'description' => 'The “What We Do With AI” cards section.',
                    'edit_url' => route('admin.home-sections.ai-services.edit'),
                    'is_active' => $aiSection->is_active,
                    'meta' => $aiSection->cards_count . ' cards',
                ],
                [
                    'key' => 'industries',
                    'name' => 'Industries we serve',
                    'description' => 'The “Industries We Transform” grid.',
                    'edit_url' => route('admin.home-sections.industries.edit'),
                    'is_active' => $industriesSection->is_active,
                    'meta' => $industriesSection->industries_count . ' industries',
                ],
                [
                    'key' => 'case-studies',
                    'name' => 'Case studies heading',
                    'description' => 'Heading copy for the case-studies/portfolio carousel.',
                    'edit_url' => route('admin.home-sections.case-studies.edit'),
                    'is_active' => $caseStudies->is_active,
                    'meta' => null,
                ],
                [
                    'key' => 'testimonials',
                    'name' => 'Testimonials heading',
                    'description' => 'Heading copy for the testimonials section.',
                    'edit_url' => route('admin.home-sections.testimonials.edit'),
                    'is_active' => $testimonials->is_active,
                    'meta' => null,
                ],
                [
                    'key' => 'connect',
                    'name' => 'Let’s connect',
                    'description' => 'Heading and CTA label for the contact-form section.',
                    'edit_url' => route('admin.home-sections.connect.edit'),
                    'is_active' => $connect->is_active,
                    'meta' => null,
                ],
            ],
        ]);
    }
}
