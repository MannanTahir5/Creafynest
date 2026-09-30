<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
            $table->string('image_alt')->nullable()->after('image_path');
        });

        Schema::table('delivery_categories', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('feature_icon');
            $table->string('image_alt')->nullable()->after('image_path');
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('icon');
            $table->string('image_alt')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_alt']);
        });

        Schema::table('delivery_categories', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_alt']);
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_alt']);
        });
    }
};
