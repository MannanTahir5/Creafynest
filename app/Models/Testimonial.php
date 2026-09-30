<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'feedback',
        'image',
        'featured_on_home',
        'home_sort_order',
    ];

    protected function casts(): array
    {
        return [
            'featured_on_home' => 'boolean',
            'home_sort_order' => 'integer',
        ];
    }
}
