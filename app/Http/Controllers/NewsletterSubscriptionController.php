<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $validated['email']],
            ['confirmed_at' => now()],
        );

        return back()->with('success', 'You are subscribed to the newsletter.');
    }
}
