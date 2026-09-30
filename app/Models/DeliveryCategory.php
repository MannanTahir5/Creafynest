<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\PublicStorageUrl;

class DeliveryCategory extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'feature_icon',
        'image_path',
        'image_alt',
        'sort_order',
        'is_active',
    ];

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path) ?? PublicStorageUrl::FALLBACK_CARD;
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class)->orderBy('sort_order');
    }
}
