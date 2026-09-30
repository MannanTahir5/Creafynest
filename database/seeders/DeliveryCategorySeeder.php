<?php

namespace Database\Seeders;

use App\Models\DeliveryCategory;
use Illuminate\Database\Seeder;

class DeliveryCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (DeliveryCategory::query()->exists()) {
            return;
        }

        $categories = [
            [
                'slug' => 'web-development',
                'title' => 'Web Development',
                'subtitle' => 'Custom websites, web applications, and digital platforms.',
                'feature_icon' => 'LayoutGrid',
                'items' => [
                    ['title' => 'Website UI/UX', 'icon' => 'LayoutTemplate', 'description' => 'Responsive, user-centered interfaces with accessible patterns and design systems.'],
                    ['title' => 'CMS', 'icon' => 'FileStack', 'description' => 'Structured content, editorial workflows, and fast delivery for marketing teams.'],
                    ['title' => 'Custom Web Application', 'icon' => 'Box', 'description' => 'Dashboards, internal tools, and multi-tenant products built to scale with your business.'],
                    ['title' => 'E-commerce', 'icon' => 'ShoppingCart', 'description' => 'Catalogs, checkout, payments, and fulfillment-aware storefront experiences.'],
                ],
            ],
            [
                'slug' => 'mobile-development',
                'title' => 'Mobile App Development',
                'subtitle' => 'Native-quality experiences across iOS, Android, and cross-platform stacks.',
                'feature_icon' => 'Smartphone',
                'items' => [
                    ['title' => 'Mobile App UI/UX', 'icon' => 'Sparkles', 'description' => 'Motion, gestures, and polish tuned for small screens and real-world performance.'],
                    ['title' => 'Android App Development', 'icon' => 'Smartphone', 'description' => 'Material-aware builds, Play compliance, and solid offline-first patterns.'],
                    ['title' => 'iOS App Development', 'icon' => 'TabletSmartphone', 'description' => 'Human Interface Guidelines–friendly flows with App Store–ready delivery.'],
                    ['title' => 'Cross-Platform Development', 'icon' => 'Share2', 'description' => 'One codebase where it makes sense; native modules where it matters.'],
                ],
            ],
            [
                'slug' => 'ai-solutions',
                'title' => 'AI Solutions',
                'subtitle' => 'Practical AI that augments your product without compromising trust or clarity.',
                'feature_icon' => 'Bot',
                'items' => [
                    ['title' => 'Generative AI', 'icon' => 'Sparkles', 'description' => 'Assistants, summarization, and content workflows grounded in your data policies.'],
                    ['title' => 'Voice AI', 'icon' => 'Mic', 'description' => 'Speech interfaces, transcription, and conversational UX with clear fallbacks.'],
                    ['title' => 'Custom Model Integration', 'icon' => 'Cpu', 'description' => 'Inference pipelines, evaluation harnesses, and cost-aware deployment.'],
                    ['title' => 'RAG & retrieval', 'icon' => 'Database', 'description' => 'Retrieval-augmented answers with citations, guardrails, and observability.'],
                ],
            ],
            [
                'slug' => 'digital-marketing',
                'title' => 'Digital Marketing',
                'subtitle' => 'Technical foundations that make acquisition and attribution actually measurable.',
                'feature_icon' => 'LineChart',
                'items' => [
                    ['title' => 'SEO', 'icon' => 'Search', 'description' => 'Technical SEO, structured data, and performance budgets that search engines reward.'],
                    ['title' => 'Social Media Marketing', 'icon' => 'Share2', 'description' => 'Campaign-ready landing pages, tracking, and creative iteration loops.'],
                    ['title' => 'PPC', 'icon' => 'Target', 'description' => 'Conversion tracking, experiments, and landing experiences aligned to bids.'],
                    ['title' => 'Content Writing', 'icon' => 'PenLine', 'description' => 'Clear product narrative, docs, and on-site copy that supports discovery.'],
                ],
            ],
        ];

        foreach (array_values($categories) as $position => $payload) {
            $category = DeliveryCategory::query()->create([
                'slug' => $payload['slug'],
                'title' => $payload['title'],
                'subtitle' => $payload['subtitle'],
                'feature_icon' => $payload['feature_icon'],
                'sort_order' => $position,
                'is_active' => true,
            ]);

            foreach (array_values($payload['items']) as $itemPosition => $item) {
                $category->items()->create([
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'icon' => $item['icon'],
                    'sort_order' => $itemPosition,
                ]);
            }
        }
    }
}
