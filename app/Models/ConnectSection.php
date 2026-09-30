<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectSection extends Model
{
    protected $fillable = [
        'eyebrow',
        'heading',
        'description',
        'submit_label',
        'timeline_options',
        'service_options',
        'how_found_label',
        'idea_label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'timeline_options' => 'array',
            'service_options' => 'array',
        ];
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    public function timelineOptions(): array
    {
        $options = $this->timeline_options;

        if (is_array($options) && $options !== []) {
            return array_values($options);
        }

        return [
            ['id' => 'short', 'label' => 'Short: up to 3 months'],
            ['id' => 'medium', 'label' => 'Medium: 3–9 months'],
            ['id' => 'long', 'label' => 'Long: 9+ months'],
            ['id' => 'unsure', 'label' => 'Not sure'],
        ];
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    public function serviceOptions(): array
    {
        $options = $this->service_options;

        if (is_array($options) && $options !== []) {
            return array_values($options);
        }

        return [
            ['id' => 'ai', 'label' => 'AI based'],
            ['id' => 'web', 'label' => 'Web development'],
            ['id' => 'mobile', 'label' => 'Mobile apps'],
            ['id' => 'design', 'label' => 'Web designs'],
            ['id' => 'branding', 'label' => 'Logos & branding'],
            ['id' => 'cms', 'label' => 'WordPress & Shopify'],
        ];
    }

    public static function singleton(): self
    {
        return self::query()->orderBy('id')->firstOrCreate([], [
            'eyebrow' => 'Get in touch',
            'heading' => "Let's connect",
            'description' => "We're just a message away. Contact us now and let's make something great together!",
            'submit_label' => "Let's work together",
            'is_active' => true,
        ]);
    }
}
