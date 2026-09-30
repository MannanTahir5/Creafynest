<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class HeroAvatar extends Model
{
    protected $fillable = [
        'hero_id',
        'image_path',
        'image_alt',
        'name',
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

    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path) ?? PublicStorageUrl::FALLBACK_AVATAR;
    }
}
