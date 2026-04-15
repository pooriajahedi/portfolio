<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_sections', function (Blueprint $table) {
            $table->json('skill_categories')->nullable()->after('paragraph_two');
        });
    }

    public function down(): void
    {
        Schema::table('about_sections', function (Blueprint $table) {
            $table->dropColumn('skill_categories');
        });
    }
};
