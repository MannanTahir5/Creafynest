<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                'projects' => Project::query()->count(),
                'blogs' => Blog::query()->count(),
                'categories' => Category::query()->count(),
                'services' => Service::query()->count(),
                'testimonials' => Testimonial::query()->count(),
            ],
        ]);
    }
}
