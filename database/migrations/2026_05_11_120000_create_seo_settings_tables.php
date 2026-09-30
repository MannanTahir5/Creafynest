<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('default_og_image_path')->nullable();
            $table->string('default_og_image_alt', 255)->nullable();
            $table->string('twitter_site', 100)->nullable();
            $table->string('twitter_creator', 100)->nullable();
            $table->string('og_locale', 32)->default('en_US');
            $table->string('default_robots', 255)->nullable();
            $table->string('theme_color', 32)->nullable();
            $table->boolean('enable_blog_search_action')->default(true);
            $table->timestamps();
        });

        Schema::create('seo_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 64)->unique();
            $table->string('title_stem', 190)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('og_title', 190)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('og_image_alt', 255)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->boolean('noindex')->default(false);
            $table->string('robots', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_page_settings');
        Schema::dropIfExists('seo_site_settings');
    }
};
