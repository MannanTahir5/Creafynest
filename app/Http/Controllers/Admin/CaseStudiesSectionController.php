<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudiesSection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudiesSectionController extends Controller
{
    public function edit(): Response
    {
        $section = CaseStudiesSection::singleton();

        return Inertia::render('Admin/HomeSections/CaseStudies/Edit', [
            'section' => [
                'id' => $section->id,
                'heading_lead' => $section->heading_lead,
                'heading_accent' => $section->heading_accent,
                'view_all_label' => $section->view_all_label,
                'view_all_href' => $section->view_all_href,
                'is_active' => $section->is_active,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'heading_lead' => ['nullable', 'string', 'max:160'],
            'heading_accent' => ['nullable', 'string', 'max:160'],
            'view_all_label' => ['nullable', 'string', 'max:80'],
            'view_all_href' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        CaseStudiesSection::singleton()->update($data + ['is_active' => $data['is_active'] ?? true]);
        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'case_studies',
            'Case studies heading updated.',
            'admin.home-sections.case-studies.edit',
        );
    }
}
