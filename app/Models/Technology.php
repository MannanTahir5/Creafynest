<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class Technology extends Model
{
    protected $fillable = [
        'technology_section_id',
        'name',
        'icon_slug',
        'image_path',
        'image_alt',
        'row_index',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'row_index' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(TechnologySection::class, 'technology_section_id');
    }

    public function imageUrl(): ?string
    {
        return PublicStorageUrl::url($this->image_path);
    }

    public function iconSrc(): ?string
    {
        if ($this->image_path) {
            return $this->imageUrl();
        }

        if ($this->icon_slug) {
            return 'https://cdn.jsdelivr.net/npm/simple-icons/icons/' . $this->icon_slug . '.svg';
        }

        return null;
    }
}
