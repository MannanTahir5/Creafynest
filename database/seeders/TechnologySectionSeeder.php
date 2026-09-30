<?php

namespace Database\Seeders;

use App\Models\Technology;
use App\Models\TechnologySection;
use Illuminate\Database\Seeder;

class TechnologySectionSeeder extends Seeder
{
    public function run(): void
    {
        $section = TechnologySection::query()->orderBy('id')->first();

        if (! $section) {
            $section = TechnologySection::query()->create([
                'eyebrow' => 'Latest technologies',
                'heading_lead' => 'Our core',
                'heading_highlight' => 'technologies',
                'description' => 'We work across a modern stack to deliver effective, scalable, and future-proof custom web and mobile experiences.',
                'is_active' => true,
            ]);
        }

        if ($section->technologies()->exists()) {
            return;
        }

        $row1 = [
            ['React', 'react'],
            ['Vue.js', 'vuedotjs'],
            ['Angular', 'angular'],
            ['HTML5', 'html5'],
            ['CSS3', 'css3'],
            ['Sass', 'sass'],
            ['Node.js', 'nodedotjs'],
            ['Java', 'java'],
            ['JavaScript', 'javascript'],
            ['Vite', 'vite'],
            ['Redis', 'redis'],
            ['PostgreSQL', 'postgresql'],
            ['MongoDB', 'mongodb'],
            ['GraphQL', 'graphql'],
            ['Alpine.js', 'alpinejs'],
            ['Jest', 'jest'],
        ];

        $row2 = [
            ['Laravel', 'laravel'],
            ['PHP', 'php'],
            ['TypeScript', 'typescript'],
            ['.NET', 'dotnet'],
            ['Python', 'python'],
            ['Tailwind CSS', 'tailwindcss'],
            ['MySQL', 'mysql'],
            ['Docker', 'docker'],
            ['Git', 'git'],
            ['GitHub Actions', 'githubactions'],
            ['Nginx', 'nginx'],
            ['Kubernetes', 'kubernetes'],
            ['AWS', 'amazonaws'],
            ['PHPUnit', 'phpunit'],
            ['Composer', 'composer'],
            ['npm', 'npm'],
            ['Webpack', 'webpack'],
            ['ESLint', 'eslint'],
            ['Prettier', 'prettier'],
        ];

        $insert = function (array $rows, int $rowIndex) use ($section) {
            foreach ($rows as $i => [$name, $slug]) {
                Technology::query()->create([
                    'technology_section_id' => $section->id,
                    'name' => $name,
                    'icon_slug' => $slug,
                    'row_index' => $rowIndex,
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        };

        $insert($row1, 1);
        $insert($row2, 2);
    }
}
