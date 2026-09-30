<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class AiServiceCard extends Model
{
    protected $fillable = [
        'ai_service_section_id',
        'title',
        'description',
        'href',
        'icon',
        'color_theme',
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
        return $this->belongsTo(AiServiceSection::class, 'ai_service_section_id');
    }

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path) ?? PublicStorageUrl::FALLBACK_CARD;
    }
}
