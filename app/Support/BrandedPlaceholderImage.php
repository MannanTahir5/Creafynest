<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

/**
 * Generates branded JPEG placeholders when real assets are not checked in.
 */
final class BrandedPlaceholderImage
{
    public const STYLE_CASE_STUDY = 'case-study';

    public const STYLE_SERVICE_HERO = 'service-hero';

    public const STYLE_OG = 'og';

    public const STYLE_CARD = 'card';

    public const STYLE_AVATAR = 'avatar';

    public const STYLE_LOGO = 'logo';

    /** Logo & brand identity service hero — purple canvas, orange accents, mark preview. */
    public const STYLE_LOGO_DESIGN_HERO = 'logo-design-hero';

    /**
     * Write a JPEG if the target path does not exist. Returns whether the file is present afterward.
     */
    public static function ensureFile(
        string $absolutePath,
        string $seed,
        string $title,
        string $style = self::STYLE_CASE_STUDY,
        ?string $subtitle = null,
    ): bool {
        if (is_file($absolutePath)) {
            return true;
        }

        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        [$w, $h] = self::dimensionsFor($style);

        if (self::render($absolutePath, $seed, $title, $subtitle, $w, $h, $style)) {
            return is_file($absolutePath);
        }

        if ($style === self::STYLE_CASE_STUDY || $style === self::STYLE_CARD) {
            self::tryDownloadPicsum($seed, $absolutePath, $w, $h);
        }

        return is_file($absolutePath);
    }

    /**
     * @return array{0:int,1:int}
     */
    private static function dimensionsFor(string $style): array
    {
        return match ($style) {
            self::STYLE_SERVICE_HERO, self::STYLE_LOGO_DESIGN_HERO => [1200, 900],
            self::STYLE_OG => [1200, 630],
            self::STYLE_CARD => [960, 600],
            self::STYLE_AVATAR => [256, 256],
            self::STYLE_LOGO => [420, 120],
            default => [1600, 1000],
        };
    }

    private static function render(
        string $path,
        string $seed,
        string $title,
        ?string $subtitle,
        int $w,
        int $h,
        string $style,
    ): bool {
        if (! extension_loaded('gd') || ! function_exists('imagecreatetruecolor') || ! function_exists('imagejpeg')) {
            return false;
        }

        $im = imagecreatetruecolor($w, $h);
        if ($im === false) {
            return false;
        }

        $crc = (int) sprintf('%u', crc32($seed));
        $hue = $crc % 360;
        $hue2 = ($hue + 52) % 360;

        for ($y = 0; $y < $h; $y++) {
            $t = $y / max(1, $h - 1);
            $hNow = self::lerpHue($hue, $hue2, $t) / 360;
            $rgb = self::hslToRgb($hNow, 0.48, 0.32 + $t * 0.16);
            $col = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
            imageline($im, 0, $y, $w, $y, $col);
        }

        if ($style === self::STYLE_LOGO) {
            self::drawLogoMark($im, $w, $h, $title);
        } elseif ($style === self::STYLE_AVATAR) {
            self::drawAvatarFrame($im, $w, $h);
        } elseif ($style === self::STYLE_LOGO_DESIGN_HERO) {
            self::drawLogoDesignHero($im, $w, $h, $title, $subtitle);
        } else {
            self::drawFooterPanel($im, $w, $h, $title, $subtitle, $style);
        }

        $ok = $style === self::STYLE_LOGO && function_exists('imagepng')
            ? imagepng($im, $path, 8)
            : imagejpeg($im, $path, 88);
        imagedestroy($im);

        return $ok;
    }

    /**
     * @param \GdImage $im
     */
    private static function drawFooterPanel($im, int $w, int $h, string $title, ?string $subtitle, string $style): void
    {
        $footerRatio = match ($style) {
            self::STYLE_OG => 0.42,
            self::STYLE_SERVICE_HERO => 0.36,
            self::STYLE_CARD => 0.38,
            default => 0.34,
        };

        $footerH = (int) round($h * $footerRatio);
        $footerTop = $h - $footerH;
        $panel = imagecolorallocate($im, 15, 23, 42);
        imagefilledrectangle($im, 0, $footerTop, $w, $h, $panel);

        $accent = imagecolorallocate($im, 167, 139, 250);
        imagefilledrectangle($im, 0, $footerTop, 6, $h, $accent);

        $muted = imagecolorallocate($im, 148, 163, 184);
        $white = imagecolorallocate($im, 248, 250, 252);
        $font = 5;
        $lineH = imagefontheight($font);
        $charW = imagefontwidth($font);
        $x0 = 36;

        $y = $footerTop + 20;
        if ($subtitle) {
            $subLines = self::wrapTitle($subtitle, max(12, (int) floor(($w - 72) / max(1, $charW))));
            foreach ($subLines as $i => $line) {
                if ($i > 1) {
                    break;
                }
                imagestring($im, $font, $x0, $y + $i * $lineH, strtoupper($line), $muted);
            }
            $y += count($subLines) * $lineH + 8;
        }

        $maxChars = max(14, (int) floor(($w - 72) / max(1, $charW)));
        $lines = self::wrapTitle($title, $maxChars);
        $lines = array_slice($lines, 0, $style === self::STYLE_OG ? 2 : 3);
        foreach ($lines as $i => $line) {
            imagestring($im, $font, $x0, $y + $i * $lineH, $line, $white);
        }
    }

    /**
     * @param \GdImage $im
     */
    private static function drawAvatarFrame($im, int $w, int $h): void
    {
        $cx = (int) ($w / 2);
        $cy = (int) ($h / 2);
        $r = (int) (min($w, $h) * 0.38);
        $ring = imagecolorallocate($im, 248, 250, 252);
        imageellipse($im, $cx, $cy, $r * 2, $r * 2, $ring);
        $inner = imagecolorallocate($im, 30, 41, 59);
        imagefilledellipse($im, $cx, $cy, (int) ($r * 1.6), (int) ($r * 1.6), $inner);
    }

    /**
     * Logo design service hero — cream frame, violet canvas, orange mark (matches public service page).
     *
     * @param \GdImage $im
     */
    private static function drawLogoDesignHero($im, int $w, int $h, string $title, ?string $subtitle): void
    {
        $cream = imagecolorallocate($im, 251, 248, 243);
        $peach = imagecolorallocate($im, 255, 228, 204);
        $violet = imagecolorallocate($im, 109, 40, 217);
        $violetLight = imagecolorallocate($im, 139, 92, 246);
        $violetSoft = imagecolorallocate($im, 124, 58, 237);
        $orange = imagecolorallocate($im, 234, 88, 12);
        $orangeBright = imagecolorallocate($im, 249, 115, 22);
        $white = imagecolorallocate($im, 255, 255, 255);
        $muted = imagecolorallocate($im, 233, 213, 255);
        $slate = imagecolorallocate($im, 30, 41, 59);

        imagefilledrectangle($im, 0, 0, $w, $h, $cream);

        $padX = (int) round($w * 0.06);
        $padY = (int) round($h * 0.08);
        imagefilledrectangle($im, $padX, $padY, $w - $padX, $h - $padY, $peach);

        $inX = $padX + (int) round($w * 0.035);
        $inY = $padY + (int) round($h * 0.04);
        $inW = $w - $inX * 2;
        $inH = $h - $inY * 2;
        imagefilledrectangle($im, $inX, $inY, $inX + $inW, $inY + $inH, $violet);

        imagefilledellipse($im, (int) ($w * 0.72), (int) ($h * 0.28), (int) ($w * 0.42), (int) ($w * 0.42), $violetLight);
        imagefilledellipse($im, (int) ($w * 0.28), (int) ($h * 0.62), (int) ($w * 0.48), (int) ($w * 0.48), $violetSoft);

        $cx = (int) ($w / 2);
        $cy = (int) ($h * 0.44);
        $r = (int) min($w, $h) * 0.14;
        imagefilledellipse($im, $cx, $cy, $r * 2, $r * 2, $orangeBright);
        imagefilledellipse($im, $cx, $cy, (int) ($r * 1.55), (int) ($r * 1.55), $white);
        imagefilledellipse($im, $cx, $cy, (int) ($r * 0.95), (int) ($r * 0.95), $orange);

        $font = 5;
        $mark = 'Aa';
        $mw = imagefontwidth($font) * strlen($mark);
        imagestring($im, $font, $cx - (int) ($mw / 2), $cy - (int) (imagefontheight($font) / 2), $mark, $white);

        $label = self::shortLabel($title, 18);
        $lw = imagefontwidth($font) * strlen($label);
        imagestring($im, $font, $cx - (int) ($lw / 2), $cy + $r + 28, $label, $white);

        $sub = $subtitle ? strtoupper(self::shortLabel($subtitle, 28)) : 'LOGO & BRAND IDENTITY';
        $sw = imagefontwidth($font) * strlen($sub);
        imagestring($im, $font, $cx - (int) ($sw / 2), $cy + $r + 48, $sub, $muted);

        $cardW = (int) round($w * 0.38);
        $cardH = (int) round($h * 0.17);
        $cardX = $w - $padX - $cardW - (int) round($w * 0.02);
        $cardY = $h - $padY - $cardH - (int) round($h * 0.03);
        imagefilledrectangle($im, $cardX, $cardY, $cardX + $cardW, $cardY + $cardH, $white);
        imagefilledrectangle($im, $cardX, $cardY, $cardX + 5, $cardY + $cardH, $orange);

        $fontSm = 3;
        $eyebrow = 'LOGO & BRAND IDENTITY';
        imagestring($im, $fontSm, $cardX + 14, $cardY + 12, $eyebrow, $orange);
        $cap = 'Clear concepts, refined files.';
        imagestring($im, $fontSm, $cardX + 14, $cardY + 28, $cap, $slate);
        imagestring($im, $fontSm, $cardX + 14, $cardY + 38, 'Formats ready to use.', $slate);
    }

    private static function shortLabel(string $text, int $max): string
    {
        $text = trim($text);
        if (strlen($text) <= $max) {
            return $text;
        }

        return substr($text, 0, max(0, $max - 1)).'…';
    }

    /**
     * @param \GdImage $im
     */
    private static function drawLogoMark($im, int $w, int $h, string $title): void
    {
        $white = imagecolorallocate($im, 248, 250, 252);
        $font = 5;
        $text = 'Creafynest';
        $tw = imagefontwidth($font) * strlen($text);
        imagestring($im, $font, (int) (($w - $tw) / 2), (int) (($h - imagefontheight($font)) / 2), $text, $white);
    }

    private static function tryDownloadPicsum(string $seed, string $path, int $w, int $h): void
    {
        $url = 'https://picsum.photos/seed/'.rawurlencode($seed)."/{$w}/{$h}.jpg";

        try {
            $response = Http::timeout(25)->connectTimeout(8)->retry(1, 400)->get($url);
        } catch (\Throwable) {
            return;
        }

        if (! $response->successful()) {
            return;
        }

        $body = $response->body();
        if (strlen($body) < 2048) {
            return;
        }

        file_put_contents($path, $body);
    }

    /**
     * @return list<string>
     */
    private static function wrapTitle(string $title, int $maxLen): array
    {
        $title = trim($title);
        if ($title === '') {
            return ['Untitled'];
        }

        $words = preg_split('/\s+/', $title) ?: [];
        $lines = [];
        $cur = '';

        foreach ($words as $word) {
            $try = $cur === '' ? $word : $cur.' '.$word;
            if (strlen($try) <= $maxLen) {
                $cur = $try;
            } else {
                if ($cur !== '') {
                    $lines[] = $cur;
                }
                $cur = strlen($word) > $maxLen ? substr($word, 0, $maxLen) : $word;
            }
        }

        if ($cur !== '') {
            $lines[] = $cur;
        }

        return $lines !== [] ? $lines : [$title];
    }

    private static function lerpHue(float $a, float $b, float $t): float
    {
        $a = fmod($a, 360.0);
        $b = fmod($b, 360.0);
        $d = $b - $a;
        if ($d > 180) {
            $d -= 360;
        }
        if ($d < -180) {
            $d += 360;
        }

        return fmod(fmod($a + $d * $t, 360.0) + 360.0, 360.0);
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private static function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s <= 0) {
            $v = (int) round($l * 255);

            return [$v, $v, $v];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        return [
            (int) round(self::hueToRgb($p, $q, $h + 1 / 3) * 255),
            (int) round(self::hueToRgb($p, $q, $h) * 255),
            (int) round(self::hueToRgb($p, $q, $h - 1 / 3) * 255),
        ];
    }

    private static function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1) {
            $t -= 1;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }
}
