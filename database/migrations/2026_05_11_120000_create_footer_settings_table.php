<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('logo_alt')->default('');
            $table->string('home_aria_label')->default('');
            $table->string('resources_heading')->default('Resources');
            $table->string('contact_heading')->default('Contact us');
            $table->string('studio_label')->default('Studio');
            $table->text('studio_text')->nullable();
            $table->string('contact_email')->default('');
            $table->string('connect_heading')->default('Connect with us');
            $table->string('copyright_entity')->default('');
            $table->text('organization_description')->nullable();
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
