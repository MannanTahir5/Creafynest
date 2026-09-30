<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            if (! Schema::hasColumn('heroes', 'heading_gradient')) {
                $table->string('heading_gradient', 32)->default('cyan-violet-fuchsia')->after('heading_line_two');
            }
            if (! Schema::hasColumn('heroes', 'feature_lines')) {
                $table->json('feature_lines')->nullable()->after('description');
            }
        });

        Schema::create('hero_avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_id')->constrained('heroes')->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hero_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_avatars');
        Schema::table('heroes', function (Blueprint $table) {
            if (Schema::hasColumn('heroes', 'feature_lines')) {
                $table->dropColumn('feature_lines');
            }
            if (Schema::hasColumn('heroes', 'heading_gradient')) {
                $table->dropColumn('heading_gradient');
            }
        });
    }
};
