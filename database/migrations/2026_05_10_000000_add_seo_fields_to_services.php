<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'meta_keywords')) {
                $table->string('meta_keywords', 255)->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('services', 'og_title')) {
                $table->string('og_title', 180)->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('services', 'og_description')) {
                $table->string('og_description', 500)->nullable()->after('og_title');
            }
            if (! Schema::hasColumn('services', 'og_image_path')) {
                $table->string('og_image_path', 255)->nullable()->after('og_description');
            }
            if (! Schema::hasColumn('services', 'noindex')) {
                $table->boolean('noindex')->default(false)->after('og_image_path');
            }
            if (! Schema::hasColumn('services', 'canonical_url')) {
                $table->string('canonical_url', 255)->nullable()->after('noindex');
            }
            if (! Schema::hasColumn('services', 'schema_type')) {
                $table->string('schema_type', 60)->nullable()->after('canonical_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            foreach (['meta_keywords', 'og_title', 'og_description', 'og_image_path', 'noindex', 'canonical_url', 'schema_type'] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
