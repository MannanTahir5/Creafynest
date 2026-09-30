<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudiesSection extends Model
{
    protected $fillable = [
        'heading_lead',
        'heading_accent',
        'view_all_label',
        'view_all_href',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'heading_lead' => 'Success stories &',
            'heading_accent' => 'case studies',
            'view_all_label' => 'View all case studies',
            'view_all_href' => '/portfolio',
            'is_active' => true,
        ]);
    }
}
