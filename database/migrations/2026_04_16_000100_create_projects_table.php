<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->longText('description');
            $table->json('tech_stack');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('live_url')->nullable();
            $table->string('github_url')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
