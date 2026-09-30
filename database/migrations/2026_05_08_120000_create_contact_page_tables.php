<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Idempotent: clean up any partial state from a previously failed run.
        Schema::dropIfExists('contact_messaging_channels');
        Schema::dropIfExists('contact_social_links');
        Schema::dropIfExists('contact_page_sections');

        Schema::create('contact_page_sections', function (Blueprint $table) {
            $table->id();

            $table->string('hero_heading')->nullable();
            $table->text('hero_subtitle')->nullable();

            $table->string('methods_eyebrow')->nullable();
            $table->string('methods_heading')->nullable();
            $table->text('methods_description')->nullable();

            $table->string('chat_card_heading')->nullable();
            $table->text('chat_card_description')->nullable();
            $table->string('chat_card_button_label')->nullable();
            $table->string('chat_card_button_href')->nullable();
            $table->boolean('chat_card_is_active')->default(true);

            $table->string('cta_heading')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_button_label')->nullable();
            $table->string('cta_button_href')->nullable();
            $table->boolean('cta_is_active')->default(true);

            $table->string('social_heading')->nullable();
            $table->boolean('social_is_active')->default(true);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messaging_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_page_section_id')
                ->nullable()
                ->constrained('contact_page_sections')
                ->nullOnDelete();
            $table->string('name');
            $table->string('handle')->nullable();
            $table->string('icon_slug')->nullable();
            $table->string('icon_bg')->nullable();
            $table->string('qr_data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['contact_page_section_id', 'sort_order'], 'cmc_section_sort_idx');
        });

        Schema::create('contact_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_page_section_id')
                ->nullable()
                ->constrained('contact_page_sections')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('href')->nullable();
            $table->string('bg_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['contact_page_section_id', 'sort_order'], 'csl_section_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messaging_channels');
        Schema::dropIfExists('contact_social_links');
        Schema::dropIfExists('contact_page_sections');
    }
};
