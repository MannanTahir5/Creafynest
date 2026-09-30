<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialsSection extends Model
{
    protected $fillable = [
        'eyebrow',
        'heading',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'eyebrow' => 'Clients reviews',
            'heading' => 'Testimonials of our valued customers',
            'description' => 'Discover how our clients have achieved remarkable success through tailored solutions. Their words inspire us to keep delivering excellence and innovation.',
            'is_active' => true,
        ]);
    }
}
