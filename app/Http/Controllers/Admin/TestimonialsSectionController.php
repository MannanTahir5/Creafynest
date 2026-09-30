<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimonialsSection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialsSectionController extends Controller
{
    public function edit(): Response
    {
        $section = TestimonialsSection::singleton();

        return Inertia::render('Admin/HomeSections/Testimonials/Edit', [
            'section' => [
                'id' => $section->id,
                'eyebrow' => $section->eyebrow,
                'heading' => $section->heading,
                'description' => $section->description,
                'is_active' => $section->is_active,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        TestimonialsSection::singleton()->update($data + ['is_active' => $data['is_active'] ?? true]);
        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'testimonials',
            'Testimonials heading updated.',
            'admin.home-sections.testimonials.edit',
        );
    }
}
