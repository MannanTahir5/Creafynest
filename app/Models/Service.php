<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;
use App\Support\SiteAssets;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'delivery_category_id',
        'title',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_path',
        'noindex',
        'canonical_url',
        'schema_type',
        'hero_image_path',
        'tech_image_path',
        'outcomes_image_path',
        'snapshot_image_path',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'noindex' => 'boolean',
            'sort_order' => 'integer',
            'content' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeliveryCategory::class, 'delivery_category_id');
    }

    public function imageUrl(string $field): ?string
    {
        $path = $this->{$field} ?? null;

        if ($path) {
            $url = PublicStorageUrl::url($path);
            if ($url !== null) {
                return $url;
            }
        }

        return match ($field) {
            'hero_image_path', 'og_image_path' => PublicStorageUrl::FALLBACK_SERVICE_HERO,
            'tech_image_path', 'outcomes_image_path', 'snapshot_image_path' => null,
            default => null,
        };
    }

    public function ensureStoredImages(): void
    {
        SiteAssets::ensureService($this);
        $this->refresh();
    }
}
