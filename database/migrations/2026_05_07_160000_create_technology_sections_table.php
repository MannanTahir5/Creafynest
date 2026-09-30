<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop any pre-existing legacy / partial-state tables. Verified empty before this migration.
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('technology_sections');

        Schema::create('technology_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading_lead')->nullable();
            $table->string('heading_highlight')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technology_section_id')
                ->nullable()
                ->constrained('technology_sections')
                ->nullOnDelete();
            $table->string('name');
            $table->string('icon_slug')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedTinyInteger('row_index')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['technology_section_id', 'row_index', 'sort_order']);
        });

        // Mark the orphaned legacy migration as already-run so future `migrate` calls
        // don't try to recreate the now-replaced `technologies` table.
        $batch = (int) DB::table('migrations')->max('batch');
        $legacy = '2025_05_09_221150_create_technologies_table';
        if (! DB::table('migrations')->where('migration', $legacy)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $legacy,
                'batch' => $batch ?: 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('technology_sections');
    }
};
