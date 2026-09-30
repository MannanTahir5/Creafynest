<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactSocialLink extends Model
{
    protected $fillable = [
        'contact_page_section_id',
        'name',
        'slug',
        'href',
        'bg_class',
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
        return $this->belongsTo(ContactPageSection::class, 'contact_page_section_id');
    }
}
