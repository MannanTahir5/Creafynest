<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class DeliveryItem extends Model
{
    protected $fillable = [
        'delivery_category_id',
        'title',
        'description',
        'icon',
        'image_path',
        'image_alt',
        'sort_order',
    ];

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path) ?? PublicStorageUrl::FALLBACK_CARD;
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeliveryCategory::class, 'delivery_category_id');
    }
}
