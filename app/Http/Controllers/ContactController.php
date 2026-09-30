<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactPageSection;
use App\Support\ContactPagePayload;
use App\Support\PublicPageSeo;
use App\Support\SeoPageKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController
{
    public function index(): Response
    {
        $section = ContactPageSection::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->first() ?? ContactPageSection::singleton();

        if (! $section->is_active) {
            $section = null;
        }

        return Inertia::render('Contact', [
            'seo' => PublicPageSeo::page(
                SeoPageKey::CONTACT,
                'contact',
                'Contact',
                'Reach out for collaborations, freelance work, or questions about Laravel, Vue, and Inertia projects.',
            ),
            'contactPage' => ContactPagePayload::forPublic($section),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $section = ContactPageSection::singleton();
        $maxMessage = max(500, min(10000, (int) ($section->form_message_max_length ?: 5000)));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:'.$maxMessage],
        ]);

        Contact::query()->create($validated);

        $success = filled($section->form_success_message)
            ? $section->form_success_message
            : 'Thanks! Your message has been sent.';

        return back()->with('success', $success);
    }
}
