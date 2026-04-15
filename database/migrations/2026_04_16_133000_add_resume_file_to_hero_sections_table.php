<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('hero_sections', 'resume_file')) {
            return;
        }

        Schema::table('hero_sections', function (Blueprint $table): void {
            $table->string('resume_file')->nullable()->after('avatar_image');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('hero_sections', 'resume_file')) {
            return;
        }

        Schema::table('hero_sections', function (Blueprint $table): void {
            $table->dropColumn('resume_file');
        });
    }
};
