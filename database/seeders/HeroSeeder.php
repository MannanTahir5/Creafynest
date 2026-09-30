<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;

class HeroSeeder extends Seeder
{
    public function run(): void
    {
        $hero = Hero::query()->orderBy('id')->first();

        if (! $hero) {
            $hero = Hero::query()->create([
                'eyebrow' => 'Premium digital solutions',
                'trust_count' => '200+',
                'trust_text' => 'Trusted by teams shipping real products',
                'heading_line_one' => 'Elevate your corporate',
                'heading_line_two' => 'digital presence',
                'heading_gradient' => 'cyan-violet-fuchsia',
                'description' => 'We deliver premium web, mobile, and AI solutions tailored for the modern enterprise. Innovation meets elegance.',
                'feature_lines' => [
                    'Production-grade engineering',
                    'Modern UX & accessibility',
                    'Pragmatic delivery from discovery to launch',
                ],
                'primary_cta_label' => 'Our services',
                'primary_cta_href' => '/services',
                'secondary_cta_label' => 'Contact us',
                'secondary_cta_href' => '/contact',
                'is_active' => true,
            ]);
            $hero->activate();

            return;
        }

        // Backfill new fields on the existing hero only when they're missing,
        // so manually edited content is preserved.
        $patch = [];
        if (! $hero->heading_gradient) {
            $patch['heading_gradient'] = 'cyan-violet-fuchsia';
        }
        if (! is_array($hero->feature_lines) || count($hero->feature_lines) === 0) {
            $patch['feature_lines'] = [
                'Production-grade engineering',
                'Modern UX & accessibility',
                'Pragmatic delivery from discovery to launch',
            ];
        }
        if (! empty($patch)) {
            $hero->update($patch);
        }
    }
}
