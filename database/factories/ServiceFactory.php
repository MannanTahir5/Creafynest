<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $icons = ['Code', 'Layers', 'Sparkles', 'Hammer', 'Briefcase'];

        return [
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'icon' => fake()->randomElement($icons),
        ];
    }
}
