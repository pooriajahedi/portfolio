<?php

use App\Support\PublicApiCache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The about, services and experience sections had no English copy yet, so the
 * English site fell back to Persian. This translates the content that is live
 * today. Rows are matched on their exact Persian text and an English value that
 * already exists is never overwritten.
 */
return new class extends Migration
{
    private const ABOUT_TITLE = 'بیش از ۱۰ سال تجربه در توسعه بک‌اند و فول‌استک';

    private const ABOUT_PARAGRAPH_ONE_START = 'در این سال‌ها هم در یک تیم ثابت';

    private const ABOUT_PARAGRAPH_ONE_EN = <<<'TEXT'
Over these years I have worked in a stable team on developing and maintaining real products, and taken on freelance projects alongside. That experience has put me in touch with almost every part of a product — from analysing and building an application's foundation from scratch to rewriting key parts, optimising performance, solving scalability challenges and putting legacy systems in order.

For me, software development is not just implementing a new feature. What fascinates me more is building a system that is designed properly, is easier to maintain and can be extended later without pain. That is why I have always tried to cut unnecessary complexity, keep the code cleaner and more efficient, and find solutions that do not only solve today's problem but stay useful in the future.
TEXT;

    private const ABOUT_PARAGRAPH_TWO = [
        'fa' => 'علاقه اصلی من کم کردن پیچیدگی، استانداردسازی خروجی APIها، بهینه سازی کوئری ها و طراحی جریان های پایدار برای پردازش های سنگین است.',
        'en' => 'What I care about most is cutting complexity, standardising API output, optimising queries and designing stable flows for heavy workloads.',
    ];

    /** Persian service-card title => [English title, English description]. */
    private const SERVICE_CARDS = [
        'تحلیل و توسعه زیرساخت از صفر' => [
            'Analysis and infrastructure from scratch',
            'In some projects my focus is the technical foundation — building infrastructure that is ready for growth and future development from day one.',
        ],
        'بازنویسی بخش‌های قدیمی و پرچالش' => [
            'Rewriting old, challenging modules',
            'When part of a system is old, complex or hard to extend, I redesign it to be cleaner, more maintainable and easier to evolve.',
        ],
        'توسعه REST API' => [
            'REST API development',
            'I focus on designing and building APIs with a clean, understandable structure that will not cause trouble for future development and maintenance.',
        ],
    ];

    /** Persian resume-item title => [English title, English description]. */
    private const RESUME_ITEMS = [
        'توسعه‌دهنده ارشد بک‌اند و فول‌استک | وسام آکادمی' => [
            'Senior Backend & Full-Stack Developer | Vesam Academy',
            'At Vesam Academy I worked on developing and maintaining educational products and live applications. Along the way I focused mostly on rewriting legacy parts, optimising performance, building REST APIs, improving queries and increasing system stability.',
        ],
        'توسعه‌دهنده بک‌اند و فول‌استک | آکادمی پرورش افکار' => [
            'Backend & Full-Stack Developer | Parvaresh-e Afkar Academy',
            'In this team I migrated a legacy, framework-less system to Laravel. The project included rewriting custom modules, reconciling the old and new database structures, and moving users, payments and invoices to the new version. The migration was completed with the data fully preserved, and the new version successfully replaced the previous system.',
        ],
        'توسعه‌دهنده فریلنس بک‌اند و فول‌استک' => [
            'Freelance Backend & Full-Stack Developer',
            'In freelance projects I mostly worked on backend development, designing admin panels, building Telegram bots and implementing custom services. My focus has been on a sound technical structure, API development, tools such as Redis and Filament, and delivering solutions that are maintainable and extensible in practice.',
        ],
    ];

    public function up(): void
    {
        $this->translateAbout();
        $this->translateServiceCards();
        $this->translateResumeItems();

        DB::table('portfolio_sections')
            ->where(fn ($query) => $query->whereNull('title_en')->orWhere('title_en', ''))
            ->update(['title_en' => 'Selected work']);

        PublicApiCache::flush();
    }

    public function down(): void
    {
        // Translations are ordinary content now; the admin owns them from here on.
    }

    private function translateAbout(): void
    {
        foreach (DB::table('about_sections')->get(['id', 'title', 'paragraph_one', 'paragraph_one_en', 'paragraph_two', 'paragraph_two_en']) as $row) {
            $update = [];

            if (
                $this->isEmpty($row->paragraph_one_en)
                && $this->normalize($row->title) === self::ABOUT_TITLE
                && str_starts_with($this->normalize($row->paragraph_one), self::ABOUT_PARAGRAPH_ONE_START)
            ) {
                $update['paragraph_one_en'] = self::ABOUT_PARAGRAPH_ONE_EN;
            }

            if (
                $this->isEmpty($row->paragraph_two_en)
                && $this->normalize($row->paragraph_two) === self::ABOUT_PARAGRAPH_TWO['fa']
            ) {
                $update['paragraph_two_en'] = self::ABOUT_PARAGRAPH_TWO['en'];
            }

            if ($update !== []) {
                DB::table('about_sections')->where('id', $row->id)->update($update);
            }
        }
    }

    private function translateServiceCards(): void
    {
        foreach (DB::table('about_service_cards')->get(['id', 'title', 'title_en', 'description_en']) as $row) {
            [$titleEn, $descriptionEn] = self::SERVICE_CARDS[$this->normalize($row->title)] ?? [null, null];

            if ($titleEn === null) {
                continue;
            }

            DB::table('about_service_cards')->where('id', $row->id)->update([
                'title_en' => $this->isEmpty($row->title_en) ? $titleEn : $row->title_en,
                'description_en' => $this->isEmpty($row->description_en) ? $descriptionEn : $row->description_en,
            ]);
        }
    }

    private function translateResumeItems(): void
    {
        foreach (DB::table('resume_items')->get(['id', 'title', 'title_en', 'description_en']) as $row) {
            [$titleEn, $descriptionEn] = self::RESUME_ITEMS[$this->normalize($row->title)] ?? [null, null];

            if ($titleEn === null) {
                continue;
            }

            DB::table('resume_items')->where('id', $row->id)->update([
                'title_en' => $this->isEmpty($row->title_en) ? $titleEn : $row->title_en,
                'description_en' => $this->isEmpty($row->description_en) ? $descriptionEn : $row->description_en,
            ]);
        }
    }

    /** Collapse whitespace so stray double spaces or line breaks do not break a match. */
    private function normalize(?string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    private function isEmpty(?string $value): bool
    {
        return trim((string) $value) === '';
    }
};
