<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\ContentCache;
use App\Support\ImageRules;
use App\Support\WebpDerivative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        $testimonials = Testimonial::query()->latest()->paginate(20);

        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Testimonials/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'feedback' => ['required', 'string'],
            'image' => ImageRules::lenient(),
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('testimonials', 'public');
            WebpDerivative::encodeFromStoredPublicPath($imagePath);
        }

        Testimonial::query()->create([
            'name' => $data['name'],
            'feedback' => $data['feedback'],
            'image' => $imagePath,
        ]);

        ContentCache::forget();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('Admin/Testimonials/Edit', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'feedback' => ['required', 'string'],
            'image' => ImageRules::lenient(),
        ]);

        $imagePath = $testimonial->image;
        if ($request->hasFile('image')) {
            if ($testimonial->image) {
                WebpDerivative::deleteForOriginal($testimonial->image);
                Storage::disk('public')->delete($testimonial->image);
            }
            $imagePath = $request->file('image')->store('testimonials', 'public');
            WebpDerivative::encodeFromStoredPublicPath($imagePath);
        }

        $testimonial->update([
            'name' => $data['name'],
            'feedback' => $data['feedback'],
            'image' => $imagePath,
        ]);

        ContentCache::forget();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->image) {
            WebpDerivative::deleteForOriginal($testimonial->image);
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        ContentCache::forget();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted.');
    }
}
