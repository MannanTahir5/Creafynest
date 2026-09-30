<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applySiteSettingsOverrides();
        $this->applyTrustedProxies();
    }

    private function applySiteSettingsOverrides(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $row = SiteSetting::cached();
        if ($row === null) {
            return;
        }

        if ($row->app_name !== null && trim($row->app_name) !== '') {
            config(['app.name' => trim($row->app_name)]);
        }

        if ($row->app_url !== null && trim($row->app_url) !== '') {
            config(['app.url' => rtrim(trim($row->app_url), '/')]);
        }

        if ($row->timezone !== null && trim($row->timezone) !== '') {
            $tz = trim($row->timezone);
            config(['app.timezone' => $tz]);
            date_default_timezone_set($tz);
        }

        if ($row->locale !== null && trim($row->locale) !== '') {
            config(['app.locale' => trim($row->locale)]);
        }

        if ($row->ga4_measurement_id !== null && trim($row->ga4_measurement_id) !== '') {
            config(['analytics.ga4_measurement_id' => trim($row->ga4_measurement_id)]);
        }

        if ($row->gtm_container_id !== null && trim($row->gtm_container_id) !== '') {
            config(['analytics.gtm_container_id' => trim($row->gtm_container_id)]);
        }

        if ($row->meta_pixel_id !== null && trim($row->meta_pixel_id) !== '') {
            config(['analytics.meta_pixel_id' => trim($row->meta_pixel_id)]);
        }

        $this->applyMailOverrides($row);
    }

    private function applyMailOverrides(SiteSetting $row): void
    {
        if (! filled($row->smtp_host)) {
            return;
        }

        config(['mail.default' => 'smtp']);
        config(['mail.mailers.smtp.host' => $row->smtp_host]);

        if ($row->smtp_port) {
            config(['mail.mailers.smtp.port' => $row->smtp_port]);
        }

        if (filled($row->smtp_username)) {
            config(['mail.mailers.smtp.username' => $row->smtp_username]);
        }

        if (filled($row->smtp_password)) {
            try {
                config(['mail.mailers.smtp.password' => Crypt::decryptString($row->smtp_password)]);
            } catch (DecryptException) {
                // ignore invalid cipher text
            }
        }

        if (filled($row->smtp_encryption)) {
            config(['mail.mailers.smtp.encryption' => $row->smtp_encryption]);
        }

        if (filled($row->mail_from_address)) {
            config(['mail.from.address' => $row->mail_from_address]);
        }

        if (filled($row->mail_from_name)) {
            config(['mail.from.name' => $row->mail_from_name]);
        }
    }

    private function applyTrustedProxies(): void
    {
        $trusted = config('app.trusted_proxies');

        if ($trusted === '*') {
            TrustProxies::at('*');
        } elseif (is_string($trusted) && $trusted !== '') {
            $ips = array_values(array_filter(array_map('trim', explode(',', $trusted))));
            if ($ips !== []) {
                TrustProxies::at($ips);
            }
        }
    }
}
