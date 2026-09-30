<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('services_eyebrow')->nullable();
            $table->string('services_heading_lead')->nullable();
            $table->string('services_heading_highlight')->nullable();
            $table->text('services_description')->nullable();
            $table->json('featured_project_slugs')->nullable();
            $table->json('featured_testimonial_ids')->nullable();
            $table->json('section_visibility')->nullable();
            $table->timestamps();
        });

        Schema::table('connect_sections', function (Blueprint $table) {
            $table->json('timeline_options')->nullable()->after('submit_label');
            $table->json('service_options')->nullable()->after('timeline_options');
            $table->string('how_found_label')->nullable()->after('service_options');
            $table->string('idea_label')->nullable()->after('how_found_label');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('role')->nullable()->after('name');
            $table->boolean('featured_on_home')->default(false)->after('feedback');
            $table->unsignedInteger('home_sort_order')->default(0)->after('featured_on_home');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['role', 'featured_on_home', 'home_sort_order']);
        });

        Schema::table('connect_sections', function (Blueprint $table) {
            $table->dropColumn(['timeline_options', 'service_options', 'how_found_label', 'idea_label']);
        });

        Schema::dropIfExists('home_page_settings');
    }
};
