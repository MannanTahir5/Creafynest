<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = Str::title(fake()->words(5, true));

        $html = '<h2>'.e($title).'</h2>'
            .'<p>'.e(fake()->paragraph()).'</p>'
            .'<h3>Highlights</h3>'
            .'<ul>'
            .'<li>'.e(fake()->sentence()).'</li>'
            .'<li>'.e(fake()->sentence()).'</li>'
            .'<li>'.e(fake()->sentence()).'</li>'
            .'</ul>'
            .'<p>'.e(fake()->paragraph()).'</p>';

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 999999),
            'content' => $html,
            'content_format' => 'html',
            'image' => null,
            'category_id' => Category::factory(),
            'meta_title' => $title.' | Azee',
            'meta_description' => Str::limit(strip_tags($html), 155),
        ];
    }
}
