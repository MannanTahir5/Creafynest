<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Support\PublicStorageUrl;

class Hero extends Model
{
    protected $fillable = [
        'eyebrow',
        'trust_count',
        'trust_text',
        'heading_line_one',
        'heading_line_two',
        'heading_gradient',
        'description',
        'feature_lines',
        'image_path',
        'image_alt',
        'primary_cta_label',
        'primary_cta_href',
        'secondary_cta_label',
        'secondary_cta_href',
        'is_active',
    ];

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return PublicStorageUrl::url($this->image_path);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'feature_lines' => 'array',
        ];
    }

    public function avatars(): HasMany
    {
        return $this->hasMany(HeroAvatar::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public static function active(): ?self
    {
        return self::query()
            ->where('is_active', true)
            ->with(['avatars' => fn ($q) => $q->where('is_active', true)])
            ->latest('updated_at')
            ->first();
    }

    public function activate(): void
    {
        DB::transaction(function () {
            self::query()->where('id', '!=', $this->id)->update(['is_active' => false]);
            $this->forceFill(['is_active' => true])->save();
        });
    }
}
