<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropColumn(['published_year', 'published_month', 'published_day']);
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->unsignedSmallInteger('published_year')->nullable()->after('image_path');
            $table->unsignedTinyInteger('published_month')->nullable()->after('published_year');
            $table->unsignedTinyInteger('published_day')->nullable()->after('published_month');
        });
    }
};
