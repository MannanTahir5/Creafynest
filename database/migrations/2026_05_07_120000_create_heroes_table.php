<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('trust_count')->nullable();
            $table->string('trust_text')->nullable();
            $table->string('heading_line_one');
            $table->string('heading_line_two');
            $table->text('description');
            $table->string('primary_cta_label');
            $table->string('primary_cta_href');
            $table->string('secondary_cta_label')->nullable();
            $table->string('secondary_cta_href')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
