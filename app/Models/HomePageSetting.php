<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    public const SECTION_KEYS = [
        'hero',
        'services',
        'technologies',
        'ai',
        'case_studies',
        'industries',
        'testimonials',
        'connect',
    ];

    protected $fillable = [
        'services_eyebrow',
        'services_heading_lead',
        'services_heading_highlight',
        'services_description',
        'featured_project_slugs',
        'featured_testimonial_ids',
        'section_visibility',
    ];

    protected function casts(): array
    {
        return [
            'featured_project_slugs' => 'array',
            'featured_testimonial_ids' => 'array',
            'section_visibility' => 'array',
        ];
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'services_eyebrow' => 'Services Category',
            'services_heading_lead' => 'Our premium',
            'services_heading_highlight' => 'expertise',
            'services_description' => 'Comprehensive digital solutions engineered for growth and scalability—browse by practice area.',
            'featured_project_slugs' => [
                'swiftcare-patient-portal',
                'northwind-operations-hub',
                'aurora-booking-co',
                'fieldlink-mobile',
                'northern-stack-erp',
            ],
            'featured_testimonial_ids' => [],
            'section_visibility' => array_fill_keys(self::SECTION_KEYS, true),
        ]);
    }

    /**
     * @return array<string, bool>
     */
    public function visibilityMap(): array
    {
        $stored = $this->section_visibility ?? [];
        $map = [];
        foreach (self::SECTION_KEYS as $key) {
            $map[$key] = array_key_exists($key, $stored)
                ? (bool) $stored[$key]
                : true;
        }

        return $map;
    }

    public function isSectionVisible(string $key): bool
    {
        return $this->visibilityMap()[$key] ?? true;
    }

    /**
     * @return list<string>
     */
    public function featuredProjectSlugList(): array
    {
        $slugs = $this->featured_project_slugs ?? [];

        return array_values(array_filter(
            array_map('strval', is_array($slugs) ? $slugs : []),
            fn (string $s) => $s !== '',
        ));
    }

    /**
     * @return list<int>
     */
    public function featuredTestimonialIdList(): array
    {
        $ids = $this->featured_testimonial_ids ?? [];

        return array_values(array_filter(
            array_map('intval', is_array($ids) ? $ids : []),
            fn (int $id) => $id > 0,
        ));
    }
}
