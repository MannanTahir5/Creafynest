<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndustriesSection extends Model
{
    protected $fillable = [
        'eyebrow',
        'heading_lead',
        'heading_highlight',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function industries(): HasMany
    {
        return $this->hasMany(Industry::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'eyebrow' => 'Who we serve',
            'heading_lead' => 'Industries',
            'heading_highlight' => 'We Transform',
            'description' => 'From healthcare to logistics, we deliver tailored digital solutions that address industry-specific challenges and unlock growth.',
            'is_active' => true,
        ]);
    }
}
