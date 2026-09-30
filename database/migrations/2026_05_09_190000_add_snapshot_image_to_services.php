<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'snapshot_image_path')) {
                $table->string('snapshot_image_path', 255)->nullable()->after('value_image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'snapshot_image_path')) {
                $table->dropColumn('snapshot_image_path');
            }
        });
    }
};
