<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The matrix rain and its alternative backgrounds are replaced by a single
 * starfield, so carry the old on/off preference over and drop the dead keys.
 */
return new class extends Migration
{
    private const OBSOLETE_KEYS = [
        'site_theme_style',
        'site_matrix_enabled',
        'site_matrix_opacity',
        'site_background_mode',
        'site_background_image',
        'site_background_image_opacity',
        'site_background_solid_color',
        'site_background_gradient_from',
        'site_background_gradient_to',
    ];

    public function up(): void
    {
        $matrixEnabled = DB::table('site_settings')->where('key', 'site_matrix_enabled')->value('value');

        if ($matrixEnabled !== null) {
            SiteSetting::setMany([
                SiteSetting::KEY_STARFIELD_ENABLED => filter_var((string) $matrixEnabled, FILTER_VALIDATE_BOOL) ? '1' : '0',
            ]);
        }

        DB::table('site_settings')->whereIn('key', self::OBSOLETE_KEYS)->delete();
    }

    public function down(): void
    {
        // The removed keys fall back to their old defaults if the feature ever returns.
    }
};
