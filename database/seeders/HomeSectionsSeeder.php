<?php

namespace Database\Seeders;

use App\Models\AiServiceCard;
use App\Models\AiServiceSection;
use App\Models\CaseStudiesSection;
use App\Models\ConnectSection;
use App\Models\IndustriesSection;
use App\Models\Industry;
use App\Models\TestimonialsSection;
use Illuminate\Database\Seeder;

class HomeSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAiServices();
        $this->seedIndustries();
        ConnectSection::singleton();
        CaseStudiesSection::singleton();
        TestimonialsSection::singleton();
    }

    private function seedAiServices(): void
    {
        $section = AiServiceSection::singleton();

        $cards = [
            [
                'title' => 'Generative AI development',
                'description' => 'We specialize in Generative AI development, creating intelligent systems capable of generating human-like content, including text, images, audio, and even code.',
                'href' => '/services/generative-ai',
                'icon' => 'Sparkles',
                'color_theme' => 'violet',
            ],
            [
                'title' => 'Conversational AI development',
                'description' => 'We develop advanced Conversational AI solutions that enable seamless, human-like interactions between businesses and their customers.',
                'href' => '/services/rag-retrieval-systems',
                'icon' => 'MessagesSquare',
                'color_theme' => 'violet',
            ],
            [
                'title' => 'Voice AI development',
                'description' => 'We specialize in Voice AI development, creating intelligent voice-enabled solutions that enhance user interactions through natural and seamless communication.',
                'href' => '/services/voice-ai',
                'icon' => 'Mic',
                'color_theme' => 'violet',
            ],
        ];

        if (! $section->cards()->exists()) {
            foreach ($cards as $i => $card) {
                AiServiceCard::query()->create($card + [
                    'ai_service_section_id' => $section->id,
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }

            return;
        }

        foreach ($cards as $card) {
            AiServiceCard::query()
                ->where('ai_service_section_id', $section->id)
                ->where('title', $card['title'])
                ->update([
                    'href' => $card['href'],
                    'color_theme' => $card['color_theme'],
                ]);
        }
    }

    private function seedIndustries(): void
    {
        $section = IndustriesSection::singleton();

        if ($section->industries()->exists()) {
            return;
        }

        $items = [
            ['Healthcare', 'HeartPulse'],
            ['Finance & Banking', 'Landmark'],
            ['Retail & E-commerce', 'ShoppingBag'],
            ['Manufacturing', 'Factory'],
            ['Education', 'GraduationCap'],
            ['Real Estate', 'Building2'],
            ['Logistics', 'Truck'],
            ['Technology & SaaS', 'Cpu'],
            ['Food & Beverage', 'UtensilsCrossed'],
            ['Media & Entertainment', 'Tv'],
            ['Travel & Hospitality', 'Plane'],
            ['Insurance', 'Shield'],
            ['Legal', 'Scale'],
            ['Nonprofit', 'HeartHandshake'],
        ];

        foreach ($items as $i => [$title, $icon]) {
            Industry::query()->create([
                'industries_section_id' => $section->id,
                'title' => $title,
                'icon' => $icon,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
