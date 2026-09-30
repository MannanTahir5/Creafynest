<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The historical `services` table drifted from its original migration
        // (manual edits left an unused 30-column shape with no rows). Drop it
        // and recreate cleanly here, then mark the legacy create-migration as
        // already applied so future `php artisan migrate` runs don't re-create.
        Schema::dropIfExists('services');

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_category_id')
                ->nullable()
                ->constrained('delivery_categories')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->string('hero_image_path')->nullable();
            $table->string('tech_image_path')->nullable();
            $table->string('outcomes_image_path')->nullable();

            $table->json('content')->nullable();

            $table->timestamps();

            $table->index('delivery_category_id');
            $table->index('sort_order');
        });

        $legacy = '2026_04_16_000130_create_services_table';
        $exists = DB::table('migrations')->where('migration', $legacy)->exists();
        if (!$exists) {
            $batch = (int) (DB::table('migrations')->max('batch') ?? 0);
            DB::table('migrations')->insert([
                'migration' => $legacy,
                'batch' => max($batch, 1),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');

        DB::table('migrations')
            ->where('migration', '2026_04_16_000130_create_services_table')
            ->delete();
    }
};
