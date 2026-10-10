<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Skill categories were stored with uppercase Latin labels. Now that the site is
 * bilingual, the Persian label belongs in `label` and the Latin one in `label_en`.
 */
return new class extends Migration
{
    private const TRANSLATIONS = [
        'frontend' => ['fa' => 'فرانت‌اند', 'en' => 'Frontend'],
        'backend' => ['fa' => 'بک‌اند', 'en' => 'Backend'],
        'database' => ['fa' => 'دیتابیس', 'en' => 'Database'],
        'tools' => ['fa' => 'ابزارها', 'en' => 'Tools'],
        'devops' => ['fa' => 'دواپس', 'en' => 'DevOps'],
        'mobile' => ['fa' => 'موبایل', 'en' => 'Mobile'],
    ];

    public function up(): void
    {
        foreach (DB::table('about_sections')->get(['id', 'skill_categories']) as $section) {
            $categories = json_decode((string) $section->skill_categories, true);

            if (! is_array($categories)) {
                continue;
            }

            $updated = array_map(function ($item): array {
                if (! is_array($item)) {
                    return $item;
                }

                $key = strtolower(trim((string) ($item['key'] ?? '')));
                $label = trim((string) ($item['label'] ?? ''));
                $translation = self::TRANSLATIONS[$key] ?? null;

                $item['label'] = $translation ? $translation['fa'] : $label;
                $item['label_en'] = trim((string) ($item['label_en'] ?? '')) ?: ($translation['en'] ?? $label);

                return $item;
            }, $categories);

            DB::table('about_sections')
                ->where('id', $section->id)
                ->update(['skill_categories' => json_encode($updated, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        // The original uppercase labels survive as `label_en`.
    }
};
