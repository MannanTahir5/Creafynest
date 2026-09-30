<?php

namespace App\Console\Commands;

use App\Support\ContentCache;
use App\Support\SiteAssets;
use Illuminate\Console\Command;

class EnsureSiteImagesCommand extends Command
{
    protected $signature = 'site:ensure-images {--force-services : Regenerate every service hero SVG}';

    protected $description = 'Generate or publish missing site images (services, portfolio, home, blogs, logo)';

    public function handle(): int
    {
        $this->info('Ensuring site images…');
        if ($this->option('force-services')) {
            $this->warn('Regenerating all service hero images…');
        }
        SiteAssets::ensureAll($this->option('force-services'));
        ContentCache::forget();
        $this->info('Done. Upload branded assets via Admin anytime to replace placeholders.');

        return self::SUCCESS;
    }
}
