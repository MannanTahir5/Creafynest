<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('header_logo_path', 500)->nullable()->after('ga4_measurement_id');
            $table->string('header_logo_alt', 160)->nullable()->after('header_logo_path');
            $table->string('favicon_path', 500)->nullable()->after('header_logo_alt');
            $table->string('gtm_container_id', 32)->nullable()->after('favicon_path');
            $table->string('meta_pixel_id', 32)->nullable()->after('gtm_container_id');
            $table->string('smtp_host', 255)->nullable()->after('meta_pixel_id');
            $table->unsignedSmallInteger('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_username', 255)->nullable()->after('smtp_port');
            $table->text('smtp_password')->nullable()->after('smtp_username');
            $table->string('smtp_encryption', 16)->nullable()->after('smtp_password');
            $table->string('mail_from_address', 255)->nullable()->after('smtp_encryption');
            $table->string('mail_from_name', 120)->nullable()->after('mail_from_address');
            $table->boolean('sitemap_enabled')->default(true)->after('mail_from_name');
            $table->text('robots_extra_disallow')->nullable()->after('sitemap_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'header_logo_path',
                'header_logo_alt',
                'favicon_path',
                'gtm_container_id',
                'meta_pixel_id',
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'mail_from_address',
                'mail_from_name',
                'sitemap_enabled',
                'robots_extra_disallow',
            ]);
        });
    }
};
