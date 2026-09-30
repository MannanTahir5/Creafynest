<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class RobotsController
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /login',
        ];

        if (Schema::hasTable('site_settings')) {
            $extra = SiteSetting::cached()?->robots_extra_disallow;
            if (filled($extra)) {
                foreach (preg_split('/\r\n|\r|\n/', (string) $extra) as $rule) {
                    $rule = trim($rule);
                    if ($rule !== '') {
                        $lines[] = str_starts_with(strtolower($rule), 'disallow:')
                            ? $rule
                            : 'Disallow: '.$rule;
                    }
                }
            }
        }

        $sitemapEnabled = false;
        if (Schema::hasTable('site_settings')) {
            $row = SiteSetting::cached();
            $sitemapEnabled = $row === null || $row->sitemap_enabled !== false;
        }

        if ($sitemapEnabled) {
            $lines[] = '';
            $lines[] = 'Sitemap: '.url('/sitemap.xml');
        }

        $lines[] = '';

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
