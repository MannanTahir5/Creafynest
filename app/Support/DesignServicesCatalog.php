<?php

namespace App\Support;

/**
 * @deprecated Use {@see ServicesCatalog}
 */
final class DesignServicesCatalog
{
    public static function categories(): array
    {
        return ServicesCatalog::catalogCategories();
    }

    public static function developmentMegaMenuColumns(): array
    {
        return ServicesCatalog::developmentMegaMenuColumns();
    }

    public static function deliveryTitleToSlugMap(): array
    {
        return ServicesCatalog::deliveryTitleToSlugMap();
    }
}
