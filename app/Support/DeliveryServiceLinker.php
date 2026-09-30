<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Collection;

/**
 * Maps delivery category line items (titles from DeliveryCategorySeeder / admin) to Service
 * models for `/services/{slug}` links. Service marketing titles often differ slightly
 * (e.g. "Website UI/UX" vs "Website UI/UX Design"), so we match by prefix and by slug map.
 */
final class DeliveryServiceLinker
{
    /**
     * @param  Collection<string, Service>  $allServicesBySlug  Active services keyed by slug (all categories).
     * @param  Collection<int, Service>  $servicesInCategory  Services with this delivery category id.
     */
    public static function resolveForItem(
        object $item,
        Collection $allServicesBySlug,
        Collection $servicesInCategory,
    ): ?Service {
        $itemTitle = mb_strtolower(trim($item->title));

        /** @var Collection<string, Service> $byTitle */
        $byTitle = $servicesInCategory->keyBy(fn (Service $s) => mb_strtolower(trim($s->title)));

        $service = $byTitle->get($itemTitle);

        if (! $service) {
            foreach ($servicesInCategory as $s) {
                $st = mb_strtolower(trim($s->title));
                if ($st === $itemTitle) {
                    $service = $s;
                    break;
                }
                if (str_starts_with($st, $itemTitle.' ') || str_starts_with($st, $itemTitle.'/')) {
                    $service = $s;
                    break;
                }
            }
        }

        if (! $service) {
            $mappedSlug = DeliveryItemToServiceSlug::slugForDeliveryItemTitle($item->title);
            if ($mappedSlug) {
                $service = $allServicesBySlug->get($mappedSlug);
            }
        }

        return $service;
    }
}
