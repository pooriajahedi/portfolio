<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resume_items', function (Blueprint $table): void {
            $table->unsignedSmallInteger('start_year')->nullable()->after('description');
            $table->unsignedTinyInteger('start_month')->nullable()->after('start_year');
            $table->unsignedTinyInteger('start_day')->nullable()->after('start_month');
            $table->unsignedSmallInteger('end_year')->nullable()->after('start_day');
            $table->unsignedTinyInteger('end_month')->nullable()->after('end_year');
            $table->unsignedTinyInteger('end_day')->nullable()->after('end_month');
            $table->boolean('is_current')->default(false)->after('end_day');
        });
    }

    public function down(): void
    {
        Schema::table('resume_items', function (Blueprint $table): void {
            $table->dropColumn([
                'start_year',
                'start_month',
                'start_day',
                'end_year',
                'end_month',
                'end_day',
                'is_current',
            ]);
        });
    }
};
