<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorageUrl;

class Blog extends Model
{
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'content_format',
        'image',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_path',
        'noindex',
        'canonical_url',
        'schema_type',
    ];

    protected function casts(): array
    {
        return [
            'content_format' => 'string',
            'noindex' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(string $field): ?string
    {
        $path = $this->{$field} ?? null;

        return PublicStorageUrl::url($path);
    }
}
