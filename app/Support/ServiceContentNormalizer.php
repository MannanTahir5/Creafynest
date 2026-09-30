<?php

namespace App\Support;

/**
 * Normalizes and merges JSON content payloads for {@see \App\Models\Service}.
 * Used by admin validation and serialization so create/update/list behave consistently.
 */
final class ServiceContentNormalizer
{
    /**
     * Default structured content for new services (hero / tech / outcomes / snapshot).
     *
     * @return array<string, mixed>
     */
    public static function defaultStructure(): array
    {
        return [
            'hero' => [
                'eyebrow' => '',
                'heading_prefix' => '',
                'heading_highlight' => '',
                'subhead' => '',
                'description' => '',
                'chips' => [],
                'primary_cta_label' => 'Start a project',
                'primary_cta_href' => '/contact',
                'secondary_cta_label' => 'All services',
                'secondary_cta_href' => '/services',
                'image_alt' => '',
                'caption_eyebrow' => '',
                'caption_text' => '',
            ],
            'tech' => [
                'eyebrow' => '',
                'heading_prefix' => '',
                'heading_highlight' => '',
                'description' => '',
                'items' => [],
                'cta_label' => '',
                'cta_href' => '',
                'image_alt' => '',
            ],
            'outcomes' => [
                'eyebrow' => '',
                'heading' => '',
                'description' => '',
                'cards' => [],
                'cta_label' => '',
                'cta_href' => '',
                'image_alt' => '',
            ],
            'snapshot' => [
                'eyebrow' => '',
                'heading_prefix' => '',
                'heading_highlight' => '',
                'heading_suffix' => '',
                'paragraphs' => [],
                'caption_title' => '',
                'caption_subtitle' => '',
                'image_alt' => '',
            ],
        ];
    }

    /**
     * Merge stored JSON with defaults so every key exists in admin/public UIs.
     *
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    public static function mergeWithDefaults(array $existing): array
    {
        $defaults = self::defaultStructure();

        return [
            'hero' => array_replace($defaults['hero'], $existing['hero'] ?? []),
            'tech' => array_replace($defaults['tech'], $existing['tech'] ?? []),
            'outcomes' => array_replace($defaults['outcomes'], $existing['outcomes'] ?? []),
            'snapshot' => array_replace($defaults['snapshot'], $existing['snapshot'] ?? []),
        ];
    }

    /**
     * Strip incomplete repeater rows and normalize snapshot paragraphs before validation.
     *
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    public static function normalizeForPersistence(array $content): array
    {
        $hero = is_array($content['hero'] ?? null) ? $content['hero'] : [];
        if (isset($hero['chips']) && is_array($hero['chips'])) {
            $hero['chips'] = array_values(array_filter($hero['chips'], function ($row) {
                if (! is_array($row)) {
                    return false;
                }
                $icon = trim((string) ($row['icon'] ?? ''));
                $label = trim((string) ($row['label'] ?? ''));

                return $icon !== '' && $label !== '';
            }));
        }
        $content['hero'] = $hero;

        $tech = is_array($content['tech'] ?? null) ? $content['tech'] : [];
        if (isset($tech['items']) && is_array($tech['items'])) {
            $tech['items'] = array_values(array_filter($tech['items'], function ($row) {
                if (! is_array($row)) {
                    return false;
                }
                $name = trim((string) ($row['name'] ?? ''));
                $slug = trim((string) ($row['slug'] ?? ''));

                return $name !== '' && $slug !== '';
            }));
        }
        $content['tech'] = $tech;

        $outcomes = is_array($content['outcomes'] ?? null) ? $content['outcomes'] : [];
        if (isset($outcomes['cards']) && is_array($outcomes['cards'])) {
            $outcomes['cards'] = array_values(array_filter($outcomes['cards'], function ($row) {
                if (! is_array($row)) {
                    return false;
                }
                $icon = trim((string) ($row['icon'] ?? ''));
                $title = trim((string) ($row['title'] ?? ''));
                $description = trim((string) ($row['description'] ?? ''));

                return $icon !== '' && $title !== '' && $description !== '';
            }));
        }
        $content['outcomes'] = $outcomes;

        unset($content['value']);

        $snapshot = is_array($content['snapshot'] ?? null) ? $content['snapshot'] : [];
        if (isset($snapshot['paragraphs']) && is_array($snapshot['paragraphs'])) {
            $snapshot['paragraphs'] = array_values(array_filter(array_map(
                fn ($row) => is_string($row) ? trim($row) : '',
                $snapshot['paragraphs'],
            ), fn ($row) => $row !== ''));
        }
        $content['snapshot'] = $snapshot;

        return $content;
    }
}
