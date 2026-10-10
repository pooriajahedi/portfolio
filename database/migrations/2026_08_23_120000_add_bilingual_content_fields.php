<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('role_en')->nullable()->after('role');
            $table->text('tagline')->nullable()->after('role_en');
            $table->text('tagline_en')->nullable()->after('tagline');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('project_categories', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
        });

        Schema::table('portfolio_sections', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'role_en', 'tagline', 'tagline_en']);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'description_en']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'description_en']);
        });

        Schema::table('project_categories', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });

        Schema::table('portfolio_sections', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });
    }
};
