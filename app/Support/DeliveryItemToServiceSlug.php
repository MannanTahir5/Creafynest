<?php

namespace App\Support;

/**
 * Maps delivery category line-item titles (see DeliveryCategorySeeder) to Service slugs
 * (see AllServicesSeeder) when the canonical service title differs from the menu card title.
 */
final class DeliveryItemToServiceSlug
{
    /** @var array<string, string> normalized item title => service slug */
    private const MAP = [
        'website ui/ux' => 'website-ui-ux',
        'cms' => 'cms-solutions',
        'custom web application' => 'custom-web-applications',
        'e-commerce' => 'ecommerce-development',
        'mobile app ui/ux' => 'mobile-app-ui-ux',
        'android app development' => 'android-app-development',
        'ios app development' => 'ios-app-development',
        'cross-platform development' => 'cross-platform-app-development',
        'generative ai' => 'generative-ai',
        'voice ai' => 'voice-ai',
        'custom model integration' => 'custom-ai-model-integration',
        'rag & retrieval' => 'rag-retrieval-systems',
        'seo' => 'seo-services',
        'social media marketing' => 'social-media-marketing',
        'ppc' => 'ppc-advertising',
        'content writing' => 'content-writing',
    ];

    public static function slugForDeliveryItemTitle(string $title): ?string
    {
        $key = mb_strtolower(trim($title));

        return self::MAP[$key]
            ?? ServicesCatalog::deliveryTitleToSlugMap()[$key]
            ?? null;
    }
}
