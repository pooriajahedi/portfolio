<?php

use App\Models\HeroSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->string('current_status')
                ->default(HeroSection::STATUS_LOOKING_FOR_JOB)
                ->after('avatar_image');

            $table->dropColumn([
                'headline',
                'intro',
                'highlight_one',
                'highlight_two',
                'highlight_three',
            ]);
        });

        DB::table('hero_sections')
            ->whereNull('current_status')
            ->update(['current_status' => HeroSection::STATUS_LOOKING_FOR_JOB]);
    }

    public function down(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->string('headline')->default('')->after('current_status');
            $table->text('intro')->nullable()->after('headline');
            $table->string('highlight_one')->nullable()->after('intro');
            $table->string('highlight_two')->nullable()->after('highlight_one');
            $table->string('highlight_three')->nullable()->after('highlight_two');

            $table->dropColumn('current_status');
        });
    }
};
