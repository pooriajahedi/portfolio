<?php

use App\Support\PublicApiCache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Content fields behind the redesigned single-page site: the about and contact
 * headlines, English copy for the sections that were Persian-only, and a brand
 * colour plus cover style for every project. The starfield background is gone,
 * so its settings are dropped as well.
 *
 * English text is only filled in where it is still empty, so admin edits survive.
 */
return new class extends Migration
{
    private const ABOUT_HEADLINE = [
        'fa' => 'یک دهه تجربه؛ از نگهداری کدهای قدیمی تا مهاجرت کامل به معماری مدرن.',
        'en' => 'A decade across the whole product lifecycle — from legacy upkeep to full migrations onto modern architecture.',
    ];

    private const CONTACT_HEADLINE = [
        'fa' => 'بیایید چیزی بسازیم که زیر بار هم سرپا بماند.',
        'en' => 'Let’s build something that holds up under load.',
    ];

    private const CONTACT_DESCRIPTION_EN = 'If you are looking for help optimising a running product, rewriting sensitive parts or building a new feature, I would be glad to talk.';

    /** Persian service-card title => English copy. */
    private const SERVICE_CARDS = [
        'بهینه‌سازی سیستم‌های در حال اجرا' => ['Optimising running systems', 'Bottleneck analysis, fewer timeouts and more stable services.'],
        'بازنویسی بخش‌های Legacy' => ['Rewriting legacy modules', 'Turning old, hard-coded logic into an extensible, testable architecture.'],
        'طراحی API و خروجی استاندارد' => ['API design & standard output', 'Reshaping web-service responses for easier consumption and simpler upkeep.'],
        'بهینه‌سازی دیتابیس و کوئری' => ['Database & query tuning', 'Indexing, rewriting hot queries and using caching with intent.'],
    ];

    /** Persian resume-item title => English copy. */
    private const RESUME_ITEMS = [
        'تکامل پروژه در یک شرکت' => ['Evolving one product inside a company', 'Over the years I worked across several generations of code, focusing on rewriting high-risk parts and reducing complexity.'],
        'گزارش گیری چانک/استریم' => ['Chunked / streamed reporting', 'Redesigned the Excel export so long waits and 504 errors disappear and report history is kept.'],
        'بازنویسی منطق قیمت گذاری' => ['Pricing-logic rewrite', 'Turned hard-coded legacy logic into an extensible structure so new rules are easy to add.'],
        'بهینه سازی زیرساخت داده' => ['Data infrastructure tuning', 'On a 180-table database, I analysed logs and hot queries, then re-indexed and fixed the structure.'],
    ];

    private const OBSOLETE_SETTING_KEYS = [
        'site_starfield_enabled',
        'site_starfield_opacity',
    ];

    public function up(): void
    {
        Schema::table('about_sections', function (Blueprint $table): void {
            $table->string('headline')->nullable()->after('title');
            $table->string('headline_en')->nullable()->after('headline');
            $table->text('paragraph_one_en')->nullable()->after('paragraph_one');
            $table->text('paragraph_two_en')->nullable()->after('paragraph_two');
        });

        Schema::table('about_service_cards', function (Blueprint $table): void {
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('resume_items', function (Blueprint $table): void {
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('contact_sections', function (Blueprint $table): void {
            $table->string('headline')->nullable()->after('title');
            $table->string('headline_en')->nullable()->after('headline');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->string('brand_color', 7)->nullable()->after('tags');
            $table->string('cover_type', 8)->default('web')->after('brand_color');
        });

        $this->backfillEnglishContent();

        DB::table('site_settings')->whereIn('key', self::OBSOLETE_SETTING_KEYS)->delete();

        // The public payloads gained new keys; drop what is cached so they show up at once.
        PublicApiCache::flush();
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn(['brand_color', 'cover_type']);
        });

        Schema::table('contact_sections', function (Blueprint $table): void {
            $table->dropColumn(['headline', 'headline_en', 'description_en']);
        });

        Schema::table('resume_items', function (Blueprint $table): void {
            $table->dropColumn(['title_en', 'description_en']);
        });

        Schema::table('about_service_cards', function (Blueprint $table): void {
            $table->dropColumn(['title_en', 'description_en']);
        });

        Schema::table('about_sections', function (Blueprint $table): void {
            $table->dropColumn(['headline', 'headline_en', 'paragraph_one_en', 'paragraph_two_en']);
        });
    }

    private function backfillEnglishContent(): void
    {
        DB::table('about_sections')->whereNull('headline')->update([
            'headline' => self::ABOUT_HEADLINE['fa'],
            'headline_en' => self::ABOUT_HEADLINE['en'],
        ]);

        DB::table('contact_sections')->whereNull('headline')->update([
            'headline' => self::CONTACT_HEADLINE['fa'],
            'headline_en' => self::CONTACT_HEADLINE['en'],
            'description_en' => self::CONTACT_DESCRIPTION_EN,
        ]);

        foreach (self::SERVICE_CARDS as $title => [$titleEn, $descriptionEn]) {
            DB::table('about_service_cards')
                ->where('title', $title)
                ->update(['title_en' => $titleEn, 'description_en' => $descriptionEn]);
        }

        foreach (self::RESUME_ITEMS as $title => [$titleEn, $descriptionEn]) {
            DB::table('resume_items')
                ->where('title', $title)
                ->update(['title_en' => $titleEn, 'description_en' => $descriptionEn]);
        }
    }
};
