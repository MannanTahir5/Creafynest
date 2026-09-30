<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = Str::title(fake()->words(3, true));

        $techPool = ['Laravel', 'Vue 3', 'Inertia', 'Tailwind CSS', 'MySQL', 'Vite', 'REST APIs', 'SEO'];

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 999999),
            'category' => fake()->randomElement(['Web', 'Landing Page', 'UI', 'E-commerce']),
            'description' => collect(fake()->paragraphs(6))->implode("\n\n"),
            'tech_stack' => fake()->randomElements($techPool, fake()->numberBetween(3, 5)),
            'image' => null,
            'gallery' => null,
            'live_url' => fake()->boolean(60) ? fake()->url() : null,
            'github_url' => fake()->boolean(60) ? 'https://github.com/'.fake()->userName().'/'.Str::slug($title) : null,
        ];
    }
}
