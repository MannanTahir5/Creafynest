<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('problem_title')->nullable()->after('description');
            $table->longText('problem_text')->nullable()->after('problem_title');
            $table->string('approach_title')->nullable()->after('problem_text');
            $table->longText('approach_text')->nullable()->after('approach_title');
            $table->string('system_title')->nullable()->after('approach_text');
            $table->longText('system_text')->nullable()->after('system_title');
            $table->string('repeat_title')->nullable()->after('system_text');
            $table->longText('repeat_text')->nullable()->after('repeat_title');
            $table->string('next_title')->nullable()->after('repeat_text');
            $table->longText('next_text')->nullable()->after('next_title');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'problem_title',
                'problem_text',
                'approach_title',
                'approach_text',
                'system_title',
                'system_text',
                'repeat_title',
                'repeat_text',
                'next_title',
                'next_text',
            ]);
        });
    }
};
