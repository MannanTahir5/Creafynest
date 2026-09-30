<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechnologySection extends Model
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
        return [
            'is_active' => 'boolean',
        ];
    }

    public function technologies(): HasMany
    {
        return $this->hasMany(Technology::class)
            ->orderBy('row_index')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'eyebrow' => 'Latest technologies',
            'heading_lead' => 'Our core',
            'heading_highlight' => 'technologies',
            'description' => 'We work across a modern stack to deliver effective, scalable, and future-proof custom web and mobile experiences.',
            'is_active' => true,
        ]);
    }
}
