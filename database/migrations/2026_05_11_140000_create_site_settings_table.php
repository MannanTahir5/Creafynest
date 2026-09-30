<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name', 120)->nullable();
            $table->string('app_url', 500)->nullable();
            $table->string('timezone', 64)->nullable();
            $table->string('locale', 16)->nullable();
            $table->string('ga4_measurement_id', 32)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
