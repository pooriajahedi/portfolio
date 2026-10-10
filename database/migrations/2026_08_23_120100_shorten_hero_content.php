<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The redesigned hero replaces the long "about" copy with one short line,
 * so seed a condensed tagline for every existing hero record.
 */
return new class extends Migration
{
    private const TAGLINE_FA = 'بک‌اند و فول‌استک می‌نویسم؛ تمرکزم روی کد تمیز، API استاندارد و سیستم‌هایی است که زیر بار هم سرپا می‌مانند.';

    private const TAGLINE_EN = 'I build fast, dependable web products — clean backends, standard APIs and systems that hold up under load.';

    public function up(): void
    {
        DB::table('hero_sections')
            ->where(fn ($query) => $query->whereNull('tagline')->orWhere('tagline', ''))
            ->update(['tagline' => self::TAGLINE_FA]);

        DB::table('hero_sections')
            ->where(fn ($query) => $query->whereNull('tagline_en')->orWhere('tagline_en', ''))
            ->update(['tagline_en' => self::TAGLINE_EN]);
    }

    public function down(): void
    {
        // Nothing to restore: the tagline columns are dropped by the previous migration.
    }
};
