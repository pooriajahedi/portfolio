<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add React and Next.js to the frontend skill group, slotted in right after Vue.js.
 */
return new class extends Migration
{
    private const INSERT_AFTER = 3;

    private const SKILLS = [
        [
            'title' => 'React',
            'title_en' => 'React',
            'description' => 'ساخت رابط کاربری کامپوننت‌محور و قابل نگهداری.',
            'description_en' => 'Building component-driven, maintainable interfaces.',
            'icon' => 'logos:react',
        ],
        [
            'title' => 'Next.js',
            'title_en' => 'Next.js',
            'description' => 'توسعه اپلیکیشن‌های رندر سمت سرور و بهینه برای SEO.',
            'description_en' => 'Server-rendered, SEO-friendly application development.',
            'icon' => 'logos:nextjs-icon',
        ],
    ];

    public function up(): void
    {
        $existing = DB::table('skills')->whereIn('title', array_column(self::SKILLS, 'title'))->pluck('title')->all();
        $missing = array_values(array_filter(self::SKILLS, fn (array $skill): bool => ! in_array($skill['title'], $existing, true)));

        if ($missing === []) {
            return;
        }

        // Make room so the new skills land next to the other frontend entries.
        DB::table('skills')
            ->where('sort_order', '>', self::INSERT_AFTER)
            ->increment('sort_order', count($missing));

        $now = now();

        DB::table('skills')->insert(array_map(fn (array $skill, int $index): array => [
            ...$skill,
            'category' => 'frontend',
            'sort_order' => self::INSERT_AFTER + $index + 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $missing, array_keys($missing)));
    }

    public function down(): void
    {
        DB::table('skills')->whereIn('title', array_column(self::SKILLS, 'title'))->delete();
    }
};
