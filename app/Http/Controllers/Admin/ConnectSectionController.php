<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConnectSection;
use App\Support\AdminHomeEditorRedirect;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConnectSectionController extends Controller
{
    public function edit(): Response
    {
        $section = ConnectSection::singleton();

        return Inertia::render('Admin/HomeSections/Connect/Edit', [
            'section' => [
                'id' => $section->id,
                'eyebrow' => $section->eyebrow,
                'heading' => $section->heading,
                'description' => $section->description,
                'submit_label' => $section->submit_label,
                'how_found_label' => $section->how_found_label,
                'idea_label' => $section->idea_label,
                'timeline_options' => $section->timelineOptions(),
                'service_options' => $section->serviceOptions(),
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
            'submit_label' => ['nullable', 'string', 'max:80'],
            'how_found_label' => ['nullable', 'string', 'max:160'],
            'idea_label' => ['nullable', 'string', 'max:160'],
            'timeline_options' => ['array', 'max:12'],
            'timeline_options.*.id' => ['required', 'string', 'max:40'],
            'timeline_options.*.label' => ['required', 'string', 'max:120'],
            'service_options' => ['array', 'max:16'],
            'service_options.*.id' => ['required', 'string', 'max:40'],
            'service_options.*.label' => ['required', 'string', 'max:120'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        ConnectSection::singleton()->update([
            'eyebrow' => $data['eyebrow'] ?? null,
            'heading' => $data['heading'] ?? null,
            'description' => $data['description'] ?? null,
            'submit_label' => $data['submit_label'] ?? null,
            'how_found_label' => $data['how_found_label'] ?? null,
            'idea_label' => $data['idea_label'] ?? null,
            'timeline_options' => $data['timeline_options'] ?? null,
            'service_options' => $data['service_options'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
        ContentCache::forget();

        return AdminHomeEditorRedirect::afterSave(
            $request,
            'connect',
            '“Let’s connect” section updated.',
            'admin.home-sections.connect.edit',
        );
    }
}
