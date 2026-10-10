<?php

use App\Support\PublicApiCache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Screenshots carry text, so each project gets its own cover and gallery for the
 * English site. The existing columns stay the Persian set and keep their data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('image_path_en')->nullable()->after('image_path');
            $table->json('gallery_paths_en')->nullable()->after('gallery_paths');
        });

        PublicApiCache::flush();
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn(['image_path_en', 'gallery_paths_en']);
        });
    }
};
