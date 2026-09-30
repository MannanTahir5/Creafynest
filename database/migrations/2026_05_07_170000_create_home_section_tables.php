<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // AI services section (singleton meta) + cards (repeating)
        Schema::create('ai_service_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_service_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_service_section_id')
                ->nullable()
                ->constrained('ai_service_sections')
                ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('href')->nullable();
            $table->string('icon')->nullable();
            $table->string('color_theme', 24)->default('amber');
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['ai_service_section_id', 'sort_order']);
        });

        // Industries section (singleton meta) + industry items
        Schema::create('industries_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading_lead')->nullable();
            $table->string('heading_highlight')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industries_section_id')
                ->nullable()
                ->constrained('industries_sections')
                ->nullOnDelete();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['industries_section_id', 'sort_order']);
        });

        // Let's connect section (singleton, content + cosmetic)
        Schema::create('connect_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->string('submit_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Case studies section heading (singleton)
        Schema::create('case_studies_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading_lead')->nullable();
            $table->string('heading_accent')->nullable();
            $table->string('view_all_label')->nullable();
            $table->string('view_all_href')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Testimonials section heading (singleton)
        Schema::create('testimonials_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industries');
        Schema::dropIfExists('industries_sections');
        Schema::dropIfExists('ai_service_cards');
        Schema::dropIfExists('ai_service_sections');
        Schema::dropIfExists('connect_sections');
        Schema::dropIfExists('case_studies_sections');
        Schema::dropIfExists('testimonials_sections');
    }
};
