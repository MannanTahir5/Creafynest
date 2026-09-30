<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class Industry extends Model
{
    protected $fillable = [
        'industries_section_id',
        'title',
        'icon',
        'image_path',
        'image_alt',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(IndustriesSection::class, 'industries_section_id');
    }

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path) ?? PublicStorageUrl::FALLBACK_CARD;
    }
}
