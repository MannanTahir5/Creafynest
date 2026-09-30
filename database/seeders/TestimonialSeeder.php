<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Support\ContentCache;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Curated testimonials for the home carousel (matched by name for idempotent seeding).
     */
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Deborah Jecobi',
                'feedback' => "An outstanding experience from start to finish\n\nFrom the initial consultation to the final delivery, the team was exceptional. They transformed our outdated site into a modern, easy-to-navigate platform. Communication was clear at every milestone and the quality of work exceeded our expectations.",
                'image' => null,
            ],
            [
                'name' => 'Marcus Chen',
                'feedback' => "Reliable delivery and sharp attention to detail\n\nWe needed a Laravel and Vue stack integrated with our existing APIs. Deadlines were tight, but the project shipped on time with solid tests and documentation. I would gladly recommend this work to other product leads.",
                'image' => null,
            ],
            [
                'name' => 'Priya Natarajan',
                'feedback' => "A partner who actually listens\n\nThey took time to understand our users before writing code. The Inertia + Tailwind UI feels cohesive and fast. Support after launch has been responsive whenever we’ve had questions or small tweaks.",
                'image' => null,
            ],
            [
                'name' => 'James O’Connell',
                'feedback' => "From prototype to production without drama\n\nWe started with a rough idea and ended with a maintainable codebase our internal team can extend. Performance and accessibility were handled thoughtfully, not as an afterthought.",
                'image' => null,
            ],
        ];

        foreach ($rows as $row) {
            Testimonial::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'feedback' => $row['feedback'],
                    'image' => $row['image'],
                ]
            );
        }

        ContentCache::forget();
    }
}
