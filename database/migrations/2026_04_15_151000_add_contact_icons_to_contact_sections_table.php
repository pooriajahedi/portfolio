<?php

use App\Models\ContactSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->string('email_icon')->nullable()->after('email');
            $table->string('github_icon')->nullable()->after('github');
            $table->string('linkedin_icon')->nullable()->after('linkedin');
            $table->string('telegram_icon')->nullable()->after('telegram');
        });

        DB::table('contact_sections')->whereNull('email_icon')->update(['email_icon' => ContactSection::ICON_EMAIL]);
        DB::table('contact_sections')->whereNull('github_icon')->update(['github_icon' => ContactSection::ICON_GITHUB]);
        DB::table('contact_sections')->whereNull('linkedin_icon')->update(['linkedin_icon' => ContactSection::ICON_LINKEDIN]);
        DB::table('contact_sections')->whereNull('telegram_icon')->update(['telegram_icon' => ContactSection::ICON_TELEGRAM]);
    }

    public function down(): void
    {
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->dropColumn(['email_icon', 'github_icon', 'linkedin_icon', 'telegram_icon']);
        });
    }
};
