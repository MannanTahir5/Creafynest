<?php

namespace Database\Seeders;

use App\Models\AboutPageSection;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        $section = AboutPageSection::singleton();

        if ($section->pillars()->count() === 0) {
            $pillars = [
                [
                    'title' => 'Full-stack delivery',
                    'description' => 'From discovery and UI polish to APIs, data models, and deployment—we own the vertical slice so you are not juggling vendors.',
                    'icon_class' => 'from-blue-500 via-indigo-500 to-violet-600',
                ],
                [
                    'title' => 'Built to last',
                    'description' => 'Tests where they matter, observability hooks, and documentation that matches reality—so your codebase stays approachable as you grow.',
                    'icon_class' => 'from-emerald-500 via-teal-500 to-cyan-600',
                ],
                [
                    'title' => 'Transparent partnership',
                    'description' => 'Plain-language updates, visible trade-offs, and scope you can reason about. We would rather under-promise and over-deliver than hide risk.',
                    'icon_class' => 'from-amber-500 via-orange-500 to-rose-600',
                ],
                [
                    'title' => 'Experience across domains',
                    'description' => 'SaaS, marketplaces, content systems, and internal tools—patterns we have seen before, applied carefully to your context.',
                    'icon_class' => 'from-violet-500 via-fuchsia-600 to-pink-600',
                ],
            ];

            foreach ($pillars as $i => $pillar) {
                $section->pillars()->create($pillar + [
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        if ($section->valueItems()->count() === 0) {
            $values = [
                [
                    'title' => 'Thoughtful AI',
                    'description' => 'When AI fits your product, we integrate it with guardrails: retrieval, evaluation, and UX that sets expectations with users and stakeholders.',
                    'icon' => 'Brain',
                ],
                [
                    'title' => 'Direct communication',
                    'description' => 'No black-box phases. You get regular demos, written decisions, and a single thread of accountability from kickoff through handoff.',
                    'icon' => 'Heart',
                ],
                [
                    'title' => 'Measurable progress',
                    'description' => 'Milestones tied to user-visible value, not vanity metrics—so you can steer budget and timeline with confidence.',
                    'icon' => 'FileBarChart',
                ],
            ];

            foreach ($values as $i => $value) {
                $section->valueItems()->create($value + [
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }
    }
}
