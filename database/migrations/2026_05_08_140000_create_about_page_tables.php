<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('about_pillars');
        Schema::dropIfExists('about_value_items');
        Schema::dropIfExists('about_page_sections');

        Schema::create('about_page_sections', function (Blueprint $table) {
            $table->id();

            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_heading_lead')->nullable();
            $table->string('hero_heading_accent')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_label')->nullable();
            $table->string('hero_primary_href')->nullable();
            $table->string('hero_secondary_label')->nullable();
            $table->string('hero_secondary_href')->nullable();
            $table->string('hero_tertiary_label')->nullable();
            $table->string('hero_tertiary_href')->nullable();

            $table->string('panel_eyebrow')->nullable();
            $table->text('panel_description')->nullable();
            $table->string('panel_node_one_label')->nullable();
            $table->string('panel_node_two_label')->nullable();
            $table->string('panel_node_three_label')->nullable();
            $table->string('panel_center_label')->nullable();
            $table->string('panel_footer_label')->nullable();

            $table->string('who_heading')->nullable();
            $table->text('who_description')->nullable();

            $table->string('why_heading')->nullable();
            $table->text('why_description')->nullable();

            $table->string('cta_heading')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_button_label')->nullable();
            $table->string('cta_button_href')->nullable();
            $table->boolean('cta_is_active')->default(true);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('about_pillars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_section_id')
                ->nullable()
                ->constrained('about_page_sections')
                ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['about_page_section_id', 'sort_order'], 'about_pillars_section_sort_idx');
        });

        Schema::create('about_value_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_section_id')
                ->nullable()
                ->constrained('about_page_sections')
                ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['about_page_section_id', 'sort_order'], 'about_values_section_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_pillars');
        Schema::dropIfExists('about_value_items');
        Schema::dropIfExists('about_page_sections');
    }
};
