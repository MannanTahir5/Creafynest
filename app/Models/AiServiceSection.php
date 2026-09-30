<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiServiceSection extends Model
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

    public function cards(): HasMany
    {
        return $this->hasMany(AiServiceCard::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'eyebrow' => 'Our top services',
            'heading' => 'What We Do With AI',
            'description' => 'We specialize in Generative AI development, creating intelligent systems capable of generating human-like content, including text, images, audio, and even code.',
            'is_active' => true,
        ]);
    }
}
