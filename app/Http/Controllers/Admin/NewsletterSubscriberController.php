<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): Response
    {
        $q = (string) $request->query('q', '');

        $subscribers = NewsletterSubscriber::query()
            ->when($q !== '', fn ($query) => $query->where('email', 'like', '%'.$q.'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Newsletter/Index', [
            'filters' => ['q' => $q],
            'subscribers' => $subscribers,
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()->route('admin.newsletter.index')->with('success', 'Subscriber removed.');
    }
}
