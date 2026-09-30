<?php

namespace App\Support;

use App\Models\Service;

/**
 * Generates unique, content-aware SVG hero images per service (no PHP GD required).
 */
final class ServiceHeroSvgGenerator
{
    /** @var array<string, array{bg: string, frame: string, canvas: string, canvas2: string, accent: string, mark: string, eyebrow: string}> */
    private const CATEGORY_THEMES = [
        'logo-brand-identity' => ['bg' => '#fbf8f3', 'frame' => '#ffedd5', 'canvas' => '#6d28d9', 'canvas2' => '#8b5cf6', 'accent' => '#ea580c', 'mark' => '#ffffff', 'eyebrow' => '#ea580c'],
        'art-illustration' => ['bg' => '#fdf4ff', 'frame' => '#fae8ff', 'canvas' => '#a21caf', 'canvas2' => '#d946ef', 'accent' => '#f472b6', 'mark' => '#ffffff', 'eyebrow' => '#c026d3'],
        'print-design' => ['bg' => '#fff7ed', 'frame' => '#ffedd5', 'canvas' => '#c2410c', 'canvas2' => '#ea580c', 'accent' => '#fbbf24', 'mark' => '#ffffff', 'eyebrow' => '#ea580c'],
        'books-ebooks' => ['bg' => '#fffbeb', 'frame' => '#fef3c7', 'canvas' => '#b45309', 'canvas2' => '#d97706', 'accent' => '#f59e0b', 'mark' => '#ffffff', 'eyebrow' => '#d97706'],
        'visual-design' => ['bg' => '#f0f9ff', 'frame' => '#e0f2fe', 'canvas' => '#0369a1', 'canvas2' => '#0ea5e9', 'accent' => '#38bdf8', 'mark' => '#ffffff', 'eyebrow' => '#0284c7'],
        'marketing-design' => ['bg' => '#fdf2f8', 'frame' => '#fce7f3', 'canvas' => '#be185d', 'canvas2' => '#ec4899', 'accent' => '#f9a8d4', 'mark' => '#ffffff', 'eyebrow' => '#db2777'],
        'architecture-building-design' => ['bg' => '#f8fafc', 'frame' => '#e2e8f0', 'canvas' => '#334155', 'canvas2' => '#475569', 'accent' => '#94a3b8', 'mark' => '#ffffff', 'eyebrow' => '#64748b'],
        'fashion-merchandise' => ['bg' => '#fdf2f8', 'frame' => '#fce7f3', 'canvas' => '#9d174d', 'canvas2' => '#db2777', 'accent' => '#f472b6', 'mark' => '#ffffff', 'eyebrow' => '#be185d'],
        '3d-design' => ['bg' => '#ecfeff', 'frame' => '#cffafe', 'canvas' => '#0e7490', 'canvas2' => '#06b6d4', 'accent' => '#22d3ee', 'mark' => '#ffffff', 'eyebrow' => '#0891b2'],
        'design-miscellaneous' => ['bg' => '#f8fafc', 'frame' => '#e2e8f0', 'canvas' => '#475569', 'canvas2' => '#64748b', 'accent' => '#cbd5e1', 'mark' => '#ffffff', 'eyebrow' => '#64748b'],
        'ai-mobile-development' => ['bg' => '#f5f3ff', 'frame' => '#ede9fe', 'canvas' => '#5b21b6', 'canvas2' => '#7c3aed', 'accent' => '#a78bfa', 'mark' => '#ffffff', 'eyebrow' => '#7c3aed'],
        'ai-creative-artists' => ['bg' => '#fdf4ff', 'frame' => '#fae8ff', 'canvas' => '#7e22ce', 'canvas2' => '#a855f7', 'accent' => '#e879f9', 'mark' => '#ffffff', 'eyebrow' => '#9333ea'],
        'ai-video-production' => ['bg' => '#f5f3ff', 'frame' => '#ede9fe', 'canvas' => '#4c1d95', 'canvas2' => '#6d28d9', 'accent' => '#c4b5fd', 'mark' => '#ffffff', 'eyebrow' => '#6d28d9'],
        'ai-audio' => ['bg' => '#eef2ff', 'frame' => '#e0e7ff', 'canvas' => '#3730a3', 'canvas2' => '#4f46e5', 'accent' => '#818cf8', 'mark' => '#ffffff', 'eyebrow' => '#4f46e5'],
        'ai-content' => ['bg' => '#f0f9ff', 'frame' => '#e0f2fe', 'canvas' => '#075985', 'canvas2' => '#0284c7', 'accent' => '#7dd3fc', 'mark' => '#ffffff', 'eyebrow' => '#0369a1'],
        'video-editing-post-production' => ['bg' => '#fff1f2', 'frame' => '#ffe4e6', 'canvas' => '#9f1239', 'canvas2' => '#e11d48', 'accent' => '#fb7185', 'mark' => '#ffffff', 'eyebrow' => '#e11d48'],
        'social-marketing-videos' => ['bg' => '#fdf2f8', 'frame' => '#fce7f3', 'canvas' => '#9d174d', 'canvas2' => '#db2777', 'accent' => '#f9a8d4', 'mark' => '#ffffff', 'eyebrow' => '#db2777'],
        'animation-services' => ['bg' => '#fff7ed', 'frame' => '#ffedd5', 'canvas' => '#c2410c', 'canvas2' => '#f97316', 'accent' => '#fdba74', 'mark' => '#ffffff', 'eyebrow' => '#ea580c'],
        'product-videos' => ['bg' => '#fffbeb', 'frame' => '#fef3c7', 'canvas' => '#b45309', 'canvas2' => '#f59e0b', 'accent' => '#fcd34d', 'mark' => '#ffffff', 'eyebrow' => '#d97706'],
        'web' => ['bg' => '#eff6ff', 'frame' => '#dbeafe', 'canvas' => '#1d4ed8', 'canvas2' => '#2563eb', 'accent' => '#60a5fa', 'mark' => '#ffffff', 'eyebrow' => '#2563eb'],
        'mobile' => ['bg' => '#ecfdf5', 'frame' => '#d1fae5', 'canvas' => '#047857', 'canvas2' => '#059669', 'accent' => '#34d399', 'mark' => '#ffffff', 'eyebrow' => '#059669'],
        'ai' => ['bg' => '#f5f3ff', 'frame' => '#ede9fe', 'canvas' => '#5b21b6', 'canvas2' => '#7c3aed', 'accent' => '#c4b5fd', 'mark' => '#ffffff', 'eyebrow' => '#7c3aed'],
        'marketing' => ['bg' => '#fffbeb', 'frame' => '#fef3c7', 'canvas' => '#b45309', 'canvas2' => '#d97706', 'accent' => '#fbbf24', 'mark' => '#ffffff', 'eyebrow' => '#d97706'],
        'web-development' => ['bg' => '#eff6ff', 'frame' => '#dbeafe', 'canvas' => '#1d4ed8', 'canvas2' => '#2563eb', 'accent' => '#60a5fa', 'mark' => '#ffffff', 'eyebrow' => '#2563eb'],
        'mobile-development' => ['bg' => '#ecfdf5', 'frame' => '#d1fae5', 'canvas' => '#047857', 'canvas2' => '#059669', 'accent' => '#34d399', 'mark' => '#ffffff', 'eyebrow' => '#059669'],
        'ai-solutions' => ['bg' => '#f5f3ff', 'frame' => '#ede9fe', 'canvas' => '#5b21b6', 'canvas2' => '#7c3aed', 'accent' => '#c4b5fd', 'mark' => '#ffffff', 'eyebrow' => '#7c3aed'],
        'digital-marketing' => ['bg' => '#fffbeb', 'frame' => '#fef3c7', 'canvas' => '#b45309', 'canvas2' => '#d97706', 'accent' => '#fbbf24', 'mark' => '#ffffff', 'eyebrow' => '#d97706'],
    ];

    private const DEFAULT_THEME = ['bg' => '#fbf8f3', 'frame' => '#ffedd5', 'canvas' => '#6d28d9', 'canvas2' => '#8b5cf6', 'accent' => '#ea580c', 'mark' => '#ffffff', 'eyebrow' => '#ea580c'];

    public static function ensure(string $slug, string $title, ?string $categoryTitle = null, ?string $categorySlug = null, ?string $description = null, bool $force = false): ?string
    {
        $relative = 'services/'.$slug.'/hero.svg';
        $absolute = storage_path('app/public/'.$relative);

        if ($force) {
            self::deleteHeroAssets($slug);
        }

        if (is_file($absolute)) {
            return $relative;
        }

        $dir = dirname($absolute);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $svg = self::generate($slug, $title, $categoryTitle ?? 'Services', $categorySlug, $description);
        if (file_put_contents($absolute, $svg) === false) {
            return null;
        }

        return $relative;
    }

    public static function ensureForService(Service $service, bool $force = false): ?string
    {
        $service->loadMissing('category:id,title,slug');

        $description = is_array($service->content) ? ($service->content['hero']['description'] ?? null) : null;
        if (! is_string($description) || trim($description) === '') {
            $description = (string) $service->description;
        }

        return self::ensure(
            $service->slug,
            $service->title,
            $service->category?->title,
            $service->category?->slug,
            $description,
            $force,
        );
    }

    public static function deleteHeroAssets(string $slug): void
    {
        foreach (['hero.svg', 'hero.jpg', 'hero.webp'] as $name) {
            $relative = 'services/'.$slug.'/'.$name;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relative)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relative);
            }
        }
        WebpDerivative::deleteForOriginal('services/'.$slug.'/hero.jpg');
    }

    public static function generate(
        string $slug,
        string $title,
        string $categoryTitle,
        ?string $categorySlug,
        ?string $description,
    ): string {
        $theme = self::themeForCategorySlug($categorySlug);
        $seed = (int) sprintf('%u', crc32($slug));
        $motif = self::motifForCategory($categorySlug);

        $cx1 = 680 + ($seed % 180);
        $cy1 = 220 + (($seed >> 4) % 120);
        $cx2 = 280 + (($seed >> 8) % 160);
        $cy2 = 520 + (($seed >> 12) % 100);
        $r1 = 160 + ($seed % 60);
        $r2 = 180 + (($seed >> 6) % 80);

        $titleEsc = self::escape(self::truncate($title, 48));
        $catEsc = self::escape(strtoupper($categoryTitle));
        $caption = self::escape(self::excerpt($description ?? '', 72));
        $caption2 = self::escape(self::excerpt($description ?? '', 72, 72));

        $initial = self::escape(self::initial($title));
        $markY = 400;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900" role="img" aria-label="{$titleEsc}">
  <rect width="1200" height="900" fill="{$theme['bg']}"/>
  <rect x="52" y="56" width="1096" height="788" rx="48" fill="{$theme['frame']}"/>
  <rect x="88" y="92" width="1024" height="716" rx="40" fill="{$theme['canvas']}"/>
  <circle cx="{$cx1}" cy="{$cy1}" r="{$r1}" fill="{$theme['canvas2']}" opacity="0.55"/>
  <circle cx="{$cx2}" cy="{$cy2}" r="{$r2}" fill="{$theme['canvas2']}" opacity="0.38"/>
  {$motif}
  <text x="600" y="{$markY}" text-anchor="middle" fill="{$theme['mark']}" font-family="system-ui,Segoe UI,sans-serif" font-size="42" font-weight="800">{$initial}</text>
  <text x="600" y="500" text-anchor="middle" fill="{$theme['mark']}" font-family="system-ui,Segoe UI,sans-serif" font-size="26" font-weight="700">{$titleEsc}</text>
  <text x="600" y="536" text-anchor="middle" fill="{$theme['accent']}" font-family="system-ui,Segoe UI,sans-serif" font-size="13" font-weight="600" letter-spacing="0.16em">{$catEsc}</text>
  <rect x="720" y="680" width="420" height="130" rx="20" fill="#ffffff"/>
  <rect x="720" y="680" width="6" height="130" rx="3" fill="{$theme['eyebrow']}"/>
  <text x="748" y="718" fill="{$theme['eyebrow']}" font-family="system-ui,Segoe UI,sans-serif" font-size="11" font-weight="700" letter-spacing="0.12em">{$catEsc}</text>
  <text x="748" y="748" fill="#1e293b" font-family="system-ui,Segoe UI,sans-serif" font-size="14" font-weight="600">{$caption}</text>
  <text x="748" y="772" fill="#475569" font-family="system-ui,Segoe UI,sans-serif" font-size="13">{$caption2}</text>
</svg>
SVG;
    }

    /**
     * @return array{bg: string, frame: string, canvas: string, canvas2: string, accent: string, mark: string, eyebrow: string}
     */
    public static function themeForCategorySlug(?string $categorySlug): array
    {
        if ($categorySlug === null || $categorySlug === '') {
            return self::DEFAULT_THEME;
        }

        return self::CATEGORY_THEMES[$categorySlug] ?? self::DEFAULT_THEME;
    }

    private static function motifForCategory(?string $categorySlug): string
    {
        return match ($categorySlug) {
            'logo-brand-identity' => '<circle cx="600" cy="360" r="88" fill="#f97316"/><circle cx="600" cy="360" r="62" fill="#ffffff"/><circle cx="600" cy="360" r="38" fill="#ea580c"/><text x="600" y="372" text-anchor="middle" fill="#ffffff" font-family="system-ui,sans-serif" font-size="40" font-weight="700">Aa</text>',
            'ai-mobile-development', 'ai-creative-artists', 'ai-video-production', 'ai-audio', 'ai-content' => '<polygon points="600,300 640,360 600,420 560,360" fill="#ffffff" opacity="0.9"/><circle cx="600" cy="360" r="100" fill="none" stroke="#ffffff" stroke-width="3" opacity="0.35"/>',
            'video-editing-post-production', 'social-marketing-videos', 'animation-services', 'product-videos' => '<polygon points="580,330 660,360 580,390" fill="#ffffff" opacity="0.95"/><rect x="520" y="310" width="200" height="100" rx="12" fill="none" stroke="#ffffff" stroke-width="4" opacity="0.4"/>',
            'web', 'web-development' => '<rect x="500" y="300" width="200" height="130" rx="10" fill="#ffffff" opacity="0.15"/><rect x="520" y="320" width="160" height="12" rx="4" fill="#ffffff" opacity="0.5"/><rect x="520" y="345" width="120" height="8" rx="3" fill="#ffffff" opacity="0.35"/>',
            'mobile', 'mobile-development' => '<rect x="540" y="290" width="120" height="200" rx="18" fill="none" stroke="#ffffff" stroke-width="4" opacity="0.5"/><circle cx="600" cy="460" r="10" fill="#ffffff" opacity="0.4"/>',
            'ai-solutions', 'ai' => '<polygon points="600,300 640,360 600,420 560,360" fill="#ffffff" opacity="0.9"/><circle cx="600" cy="360" r="100" fill="none" stroke="#ffffff" stroke-width="3" opacity="0.35"/>',
            'digital-marketing', 'marketing' => '<rect x="520" y="320" width="160" height="100" rx="8" fill="#ffffff" opacity="0.2"/><path d="M540 380 L580 340 L620 380 L660 350" fill="none" stroke="#ffffff" stroke-width="4" opacity="0.45"/>',
            '3d-design' => '<polygon points="560,380 640,340 720,380 640,420" fill="#ffffff" opacity="0.25"/><polygon points="600,320 680,360 600,400 520,360" fill="#ffffff" opacity="0.4"/>',
            'print-design', 'books-ebooks' => '<rect x="520" y="310" width="70" height="90" rx="6" fill="#ffffff" opacity="0.2"/><rect x="560" y="295" width="70" height="90" rx="6" fill="#ffffff" opacity="0.35"/><rect x="600" y="280" width="70" height="90" rx="6" fill="#ffffff" opacity="0.5"/>',
            'art-illustration' => '<path d="M520 420 Q600 280 680 420" fill="none" stroke="#ffffff" stroke-width="6" opacity="0.45" stroke-linecap="round"/>',
            default => '<circle cx="600" cy="360" r="72" fill="#ffffff" opacity="0.12"/><circle cx="600" cy="360" r="48" fill="'.self::DEFAULT_THEME['accent'].'" opacity="0.85"/>',
        };
    }

    private static function initial(string $title): string
    {
        $t = trim($title);
        if ($t === '') {
            return 'C';
        }
        if (preg_match('/[A-Za-z0-9]/', $t, $m)) {
            return strtoupper($m[0]);
        }

        return 'C';
    }

    private static function excerpt(string $text, int $start = 0, int $max = 64): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?: '');
        if ($plain === '') {
            return 'Professional delivery tailored to your goals.';
        }
        $slice = mb_substr($plain, $start, $max);

        return mb_strlen($plain) > $start + $max ? rtrim($slice).'…' : $slice;
    }

    private static function truncate(string $value, int $max): string
    {
        $value = trim($value);
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, max(1, $max - 1))).'…';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
