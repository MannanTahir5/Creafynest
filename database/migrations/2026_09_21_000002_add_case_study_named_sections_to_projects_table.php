<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('how_it_started_title')->nullable()->after('problem_text');
            $table->longText('how_it_started_text')->nullable()->after('how_it_started_title');
            $table->string('how_it_started_image')->nullable()->after('how_it_started_text');

            $table->string('challenge_title')->nullable()->after('how_it_started_image');
            $table->longText('challenge_text')->nullable()->after('challenge_title');
            $table->string('challenge_image')->nullable()->after('challenge_text');

            $table->string('approach_image')->nullable()->after('approach_text');

            $table->string('results_title')->nullable()->after('repeat_text');
            $table->longText('results_text')->nullable()->after('results_title');
            $table->string('results_image')->nullable()->after('results_text');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'how_it_started_title',
                'how_it_started_text',
                'how_it_started_image',
                'challenge_title',
                'challenge_text',
                'challenge_image',
                'approach_image',
                'results_title',
                'results_text',
                'results_image',
            ]);
        });
    }
};
