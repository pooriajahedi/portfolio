<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The site became bilingual with empty English columns, so the English view fell
 * back to Persian. Seed the translations for the content that already exists.
 * Rows that already carry an English value are left alone.
 */
return new class extends Migration
{
    private const HERO = [
        'name_en' => 'Pooria Jahedi',
        'role_en' => 'Senior Backend & Full-Stack Developer',
    ];

    private const SKILLS = [
        'آشنایی با Node.js' => 'Node.js (familiar)',
    ];

    private const CATEGORIES = [
        'web-application' => 'Web Applications',
    ];

    private const PROJECTS = [
        'اپلیکیشن آموزشی پرورش افکار' => [
            'title_en' => 'Parvaresh-e Afkar Learning Platform',
            'description_en' => 'Parvaresh-e Afkar is a learning platform for free and paid courses, exercises and student support. I worked on migrating a legacy framework-less system to Laravel: rewriting the custom modules, reconciling the old and new database schemas, and moving users, payments and invoices across to the new version.',
        ],
        'ربات تلگرامی دیده‌بان ثروت' => [
            'title_en' => 'Didebaan-e Servat Telegram Bot',
            'description_en' => 'I led development of a subscription Telegram bot where users could buy a plan, get access to the channel and group, and open support requests. I also built the admin panel that manages users, subscriptions, payments, tickets and FAQ content.',
        ],
    ];

    public function up(): void
    {
        foreach (self::HERO as $column => $value) {
            DB::table('hero_sections')
                ->where(fn ($query) => $query->whereNull($column)->orWhere($column, ''))
                ->update([$column => $value]);
        }

        foreach (self::SKILLS as $title => $titleEn) {
            DB::table('skills')
                ->where('title', $title)
                ->where(fn ($query) => $query->whereNull('title_en')->orWhere('title_en', ''))
                ->update(['title_en' => $titleEn]);
        }

        foreach (self::CATEGORIES as $slug => $titleEn) {
            DB::table('project_categories')
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('title_en')->orWhere('title_en', ''))
                ->update(['title_en' => $titleEn]);
        }

        foreach (self::PROJECTS as $title => $translation) {
            DB::table('projects')
                ->where('title', $title)
                ->where(fn ($query) => $query->whereNull('title_en')->orWhere('title_en', ''))
                ->update($translation);
        }
    }

    public function down(): void
    {
        // Translations are ordinary content now; the admin owns them from here on.
    }
};
