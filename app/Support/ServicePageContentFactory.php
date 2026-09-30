<?php

namespace App\Support;

/**
 * Generates structured service page content for design (and similar) catalog entries.
 */
final class ServicePageContentFactory
{
    /**
     * @return array{hero: array<string, mixed>, tech: array<string, mixed>, outcomes: array<string, mixed>, snapshot: array<string, mixed>}
     */
    public static function forDesignService(
        string $serviceTitle,
        string $categoryTitle,
        string $icon,
        string $summary,
    ): array {
        $eyebrow = $serviceTitle;
        $highlight = self::headingHighlight($serviceTitle);

        return [
            'hero' => [
                'eyebrow' => $eyebrow,
                'heading_prefix' => 'Professional',
                'heading_highlight' => $highlight,
                'subhead' => 'tailored to your brand and audience',
                'description' => $summary,
                'chips' => [
                    ['icon' => $icon, 'label' => 'On-brand'],
                    ['icon' => 'Palette', 'label' => 'Creative direction'],
                    ['icon' => 'Sparkles', 'label' => 'Polished delivery'],
                ],
                'primary_cta_label' => 'Start a project',
                'primary_cta_href' => '/contact',
                'secondary_cta_label' => 'All services',
                'secondary_cta_href' => '/services',
                'image_alt' => "{$serviceTitle} preview — {$categoryTitle} by Creafynest.",
                'caption_eyebrow' => $categoryTitle,
                'caption_text' => 'Clear concepts, refined files, and formats ready to use.',
            ],
            'tech' => [
                'eyebrow' => 'Creative toolkit',
                'heading_prefix' => 'Tools',
                'heading_highlight' => '& workflow',
                'description' => 'Industry-standard design tools, organized handoff, and revision rounds built into a predictable delivery process.',
                'items' => [
                    ['name' => 'Figma', 'slug' => 'figma'],
                    ['name' => 'Adobe Illustrator', 'slug' => 'adobeillustrator'],
                    ['name' => 'Adobe Photoshop', 'slug' => 'adobephotoshop'],
                    ['name' => 'Adobe InDesign', 'slug' => 'adobeindesign'],
                    ['name' => 'Canva', 'slug' => 'canva'],
                    ['name' => 'Procreate', 'slug' => 'procreate'],
                ],
                'cta_label' => 'Discuss your brief',
                'cta_href' => '/contact',
                'image_alt' => 'Figma, Adobe Creative Cloud, and Canva logos.',
            ],
            'outcomes' => [
                'eyebrow' => 'Deliverables',
                'heading' => 'What you receive',
                'description' => 'Production-ready creative assets with source files, export specs, and guidance so your team can launch confidently.',
                'cards' => [
                    ['icon' => 'FileImage', 'title' => 'Master files', 'description' => 'Layered, editable source files plus print- or web-ready exports.'],
                    ['icon' => 'Layers', 'title' => 'Brand consistency', 'description' => 'Visual systems that stay coherent across touchpoints.'],
                    ['icon' => 'Rocket', 'title' => 'Fast iteration', 'description' => 'Structured feedback rounds so revisions stay focused and on schedule.'],
                ],
                'cta_label' => 'Request a quote',
                'cta_href' => '/contact',
                'image_alt' => '',
            ],
            'snapshot' => [
                'eyebrow' => 'Why Creafynest',
                'heading_prefix' => 'Design that',
                'heading_highlight' => 'ships',
                'heading_suffix' => 'not just impresses',
                'paragraphs' => [
                    "Our {$serviceTitle} work starts with your goals, audience, and brand constraints — then moves quickly to concepts you can react to.",
                    'You get a dedicated creative partner, transparent milestones, and files your marketing or product team can use immediately.',
                ],
                'caption_title' => $serviceTitle,
                'caption_subtitle' => $categoryTitle,
                'image_alt' => "{$serviceTitle} showcase",
            ],
        ];
    }

    /**
     * @return array{hero: array<string, mixed>, tech: array<string, mixed>, outcomes: array<string, mixed>, snapshot: array<string, mixed>}
     */
    public static function forAiService(
        string $serviceTitle,
        string $categoryTitle,
        string $icon,
        string $summary,
    ): array {
        $base = self::forDesignService($serviceTitle, $categoryTitle, $icon, $summary);

        $base['hero']['heading_prefix'] = 'Intelligent';
        $base['hero']['subhead'] = 'built with guardrails and observability';
        $base['hero']['chips'] = [
            ['icon' => $icon, 'label' => 'Production-ready'],
            ['icon' => 'Bot', 'label' => 'Model-aware'],
            ['icon' => 'Shield', 'label' => 'Policy-safe'],
        ];
        $base['tech']['eyebrow'] = 'AI stack';
        $base['tech']['description'] = 'Practical AI tooling — orchestration, eval, and deployment patterns your team can operate.';
        $base['tech']['items'] = [
            ['name' => 'OpenAI', 'slug' => 'openai'],
            ['name' => 'Anthropic', 'slug' => 'anthropic'],
            ['name' => 'LangChain', 'slug' => 'langchain'],
            ['name' => 'Hugging Face', 'slug' => 'huggingface'],
            ['name' => 'Python', 'slug' => 'python'],
            ['name' => 'AWS', 'slug' => 'amazonwebservices'],
        ];
        $base['outcomes']['description'] = 'Shippable AI features with eval suites, monitoring, and documentation your engineers can own.';
        $base['snapshot']['heading_prefix'] = 'AI that';
        $base['snapshot']['heading_highlight'] = 'delivers';

        return $base;
    }

    /**
     * @return array{hero: array<string, mixed>, tech: array<string, mixed>, outcomes: array<string, mixed>, snapshot: array<string, mixed>}
     */
    public static function forVideoService(
        string $serviceTitle,
        string $categoryTitle,
        string $icon,
        string $summary,
    ): array {
        $base = self::forDesignService($serviceTitle, $categoryTitle, $icon, $summary);

        $base['hero']['heading_prefix'] = 'Cinematic';
        $base['hero']['subhead'] = 'edited for every platform and audience';
        $base['hero']['chips'] = [
            ['icon' => $icon, 'label' => 'Story-first'],
            ['icon' => 'Film', 'label' => 'Broadcast-ready'],
            ['icon' => 'Clapperboard', 'label' => 'Fast turnaround'],
        ];
        $base['tech']['eyebrow'] = 'Post-production';
        $base['tech']['description'] = 'Professional NLE, motion, and finishing tools with delivery specs for social, broadcast, and web.';
        $base['tech']['items'] = [
            ['name' => 'Adobe Premiere Pro', 'slug' => 'adobepremierepro'],
            ['name' => 'After Effects', 'slug' => 'adobeaftereffects'],
            ['name' => 'DaVinci Resolve', 'slug' => 'davinciresolve'],
            ['name' => 'Final Cut Pro', 'slug' => 'finalcutpro'],
            ['name' => 'Blender', 'slug' => 'blender'],
            ['name' => 'CapCut', 'slug' => 'capcut'],
        ];
        $base['outcomes']['description'] = 'Mastered exports, revision rounds, and platform-specific cuts your marketing team can publish immediately.';
        $base['snapshot']['heading_prefix'] = 'Video that';
        $base['snapshot']['heading_highlight'] = 'converts';

        return $base;
    }

    public static function forCategorySlug(
        string $categorySlug,
        string $serviceTitle,
        string $categoryTitle,
        string $icon,
        string $summary,
    ): array {
        if (str_starts_with($categorySlug, 'ai-')) {
            return self::forAiService($serviceTitle, $categoryTitle, $icon, $summary);
        }

        if (
            str_starts_with($categorySlug, 'video-')
            || in_array($categorySlug, ['social-marketing-videos', 'animation-services', 'product-videos'], true)
        ) {
            return self::forVideoService($serviceTitle, $categoryTitle, $icon, $summary);
        }

        return self::forDesignService($serviceTitle, $categoryTitle, $icon, $summary);
    }

    private static function headingHighlight(string $title): string
    {
        $words = preg_split('/\s+/', trim($title)) ?: [];
        if (count($words) <= 2) {
            return mb_strtolower($title);
        }

        return mb_strtolower(implode(' ', array_slice($words, -2)));
    }
}
