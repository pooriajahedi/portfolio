<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\AboutServiceCard;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        HeroSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'پوریا جاهدی',
                'name_en' => 'Pooria Jahedi',
                'role' => 'برنامه نویس ارشد بک اند و فول استک',
                'role_en' => 'Senior Backend & Full-Stack Developer',
                'tagline' => 'بک‌اند و فول‌استک می‌نویسم؛ تمرکزم روی کد تمیز، API استاندارد و سیستم‌هایی است که زیر بار هم سرپا می‌مانند.',
                'tagline_en' => 'I build fast, dependable web products — clean backends, standard APIs and systems that hold up under load.',
                'avatar_image' => '/images/hero/pooria-hero.jpeg',
                'current_status' => HeroSection::STATUS_LOOKING_FOR_JOB,
            ],
        );

        AboutSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'درباره من',
                'headline' => 'یک دهه تجربه؛ از نگهداری کدهای قدیمی تا مهاجرت کامل به معماری مدرن.',
                'headline_en' => 'A decade across the whole product lifecycle — from legacy upkeep to full migrations onto modern architecture.',
                'paragraph_one' => 'من در طی یک دهه فعالیت حرفه ای، تقریبا تمام چرخه حیات یک محصول را تجربه کرده ام: از نگهداری کدهای قدیمی و پرچالش تا بازنویسی ماژول های کلیدی و مهاجرت کامل سیستم های Legacy به معماری مدرن. رویکرد من همیشه این بوده که علاوه بر حل مشکل امروز، مسیر توسعه فردا را هم باز نگه دارم.',
                'paragraph_one_en' => 'Over a decade of professional work I have lived through nearly the entire life of a product: maintaining old, difficult codebases, rewriting key modules, and migrating legacy systems onto a modern architecture. My approach has always been to solve today’s problem while keeping tomorrow’s path open.',
                'paragraph_two' => 'علاقه اصلی من کم کردن پیچیدگی، استانداردسازی خروجی APIها، بهینه سازی کوئری ها و طراحی جریان های پایدار برای پردازش های سنگین است.',
                'paragraph_two_en' => 'What I care about most is cutting complexity, standardising API output, optimising queries and designing stable flows for heavy workloads.',
                'skill_categories' => AboutSection::DEFAULT_SKILL_CATEGORIES,
                'is_active' => true,
            ],
        );

        $aboutSectionId = AboutSection::query()->value('id');

        if ($aboutSectionId) {
            $serviceCards = [
                [
                    'title' => 'بهینه‌سازی سیستم‌های در حال اجرا',
                    'title_en' => 'Optimising running systems',
                    'description' => 'تحلیل گلوگاه‌ها، کاهش خطاهای زمان‌بر و افزایش پایداری سرویس‌ها.',
                    'description_en' => 'Bottleneck analysis, fewer timeouts and more stable services.',
                ],
                [
                    'title' => 'بازنویسی بخش‌های Legacy',
                    'title_en' => 'Rewriting legacy modules',
                    'description' => 'تبدیل منطق‌های قدیمی و هاردکد به معماری قابل توسعه و تست‌پذیر.',
                    'description_en' => 'Turning old, hard-coded logic into an extensible, testable architecture.',
                ],
                [
                    'title' => 'طراحی API و خروجی استاندارد',
                    'title_en' => 'API design & standard output',
                    'description' => 'بازطراحی خروجی وب‌سرویس‌ها برای مصرف‌پذیری بهتر و نگهداری ساده‌تر.',
                    'description_en' => 'Reshaping web-service responses for easier consumption and simpler upkeep.',
                ],
                [
                    'title' => 'بهینه‌سازی دیتابیس و کوئری',
                    'title_en' => 'Database & query tuning',
                    'description' => 'ایندکس‌گذاری، بازنویسی کوئری‌های پرتکرار و استفاده هدفمند از کش.',
                    'description_en' => 'Indexing, rewriting hot queries and using caching with intent.',
                ],
            ];

            foreach ($serviceCards as $index => $card) {
                AboutServiceCard::query()->updateOrCreate(
                    [
                        'about_section_id' => $aboutSectionId,
                        'title' => $card['title'],
                    ],
                    [
                        'title_en' => $card['title_en'],
                        'description' => $card['description'],
                        'description_en' => $card['description_en'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ],
                );
            }
        }

        ContactSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'تماس با من',
                'headline' => 'بیایید چیزی بسازیم که زیر بار هم سرپا بماند.',
                'headline_en' => 'Let’s build something that holds up under load.',
                'description' => 'اگر دنبال همکاری برای بهینه سازی یک محصول در حال اجرا، بازنویسی بخش های حساس یا توسعه فیچر جدید هستید، خوشحال می شوم گفتگو کنیم.',
                'description_en' => 'If you are looking for help optimising a running product, rewriting sensitive parts or building a new feature, I would be glad to talk.',
                'email' => 'you@example.com',
                'email_icon' => ContactSection::ICON_EMAIL,
                'github' => 'github.com/your-username',
                'github_icon' => ContactSection::ICON_GITHUB,
                'linkedin' => 'linkedin.com/in/your-username',
                'linkedin_icon' => ContactSection::ICON_LINKEDIN,
                'telegram' => '@yourid',
                'telegram_icon' => ContactSection::ICON_TELEGRAM,
                'is_active' => true,
            ],
        );

        PortfolioSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'نمونه کارها',
                'title_en' => 'Selected work',
            ],
        );

        $skills = [
            ['title' => 'JavaScript', 'description' => 'توسعه رابط کاربری پویا و مدرن.', 'category' => Skill::CATEGORY_FRONTEND, 'icon' => 'logos:javascript'],
            ['title' => 'TypeScript', 'description' => 'کدنویسی type-safe برای فرانت‌اند.', 'category' => Skill::CATEGORY_FRONTEND, 'icon' => 'logos:typescript-icon'],
            ['title' => 'Vue.js', 'description' => 'ساخت SPA و رابط کاربری قابل توسعه.', 'category' => Skill::CATEGORY_FRONTEND, 'icon' => 'logos:vue'],
            ['title' => 'Tailwind CSS', 'description' => 'طراحی سریع و سیستماتیک رابط کاربری.', 'category' => Skill::CATEGORY_FRONTEND, 'icon' => 'logos:tailwindcss-icon'],
            ['title' => 'GSAP', 'description' => 'انیمیشن‌های حرفه‌ای و روان.', 'category' => Skill::CATEGORY_FRONTEND, 'icon' => 'logos:gsap'],
            ['title' => 'Laravel', 'description' => 'توسعه بک‌اند ماژولار و API محور.', 'category' => Skill::CATEGORY_BACKEND, 'icon' => 'logos:laravel'],
            ['title' => 'PHP', 'description' => 'منطق تجاری و توسعه سرویس‌های وب.', 'category' => Skill::CATEGORY_BACKEND, 'icon' => 'logos:php'],
            ['title' => 'Node.js', 'description' => 'پیاده‌سازی سرویس‌های realtime و مقیاس‌پذیر.', 'category' => Skill::CATEGORY_BACKEND, 'icon' => 'logos:nodejs-icon'],
            ['title' => 'MySQL', 'description' => 'طراحی و بهینه‌سازی دیتابیس رابطه‌ای.', 'category' => Skill::CATEGORY_DATABASE, 'icon' => 'logos:mysql'],
            ['title' => 'PostgreSQL', 'description' => 'مدیریت داده‌های ساختاریافته با کارایی بالا.', 'category' => Skill::CATEGORY_DATABASE, 'icon' => 'logos:postgresql'],
            ['title' => 'MongoDB', 'description' => 'مدیریت داده NoSQL برای سناریوهای منعطف.', 'category' => Skill::CATEGORY_DATABASE, 'icon' => 'logos:mongodb-icon'],
            ['title' => 'Redis', 'description' => 'کش و صف برای سرعت و مقیاس‌پذیری.', 'category' => Skill::CATEGORY_DATABASE, 'icon' => 'logos:redis'],
            ['title' => 'Git', 'description' => 'کنترل نسخه و همکاری تیمی.', 'category' => Skill::CATEGORY_TOOLS, 'icon' => 'logos:git-icon'],
            ['title' => 'Docker', 'description' => 'یکپارچه‌سازی محیط توسعه و استقرار.', 'category' => Skill::CATEGORY_TOOLS, 'icon' => 'logos:docker-icon'],
            ['title' => 'Linux', 'description' => 'مدیریت سرور و محیط عملیاتی.', 'category' => Skill::CATEGORY_TOOLS, 'icon' => 'logos:linux-tux'],
            ['title' => 'AWS', 'description' => 'زیرساخت ابری و سرویس‌های مقیاس‌پذیر.', 'category' => Skill::CATEGORY_TOOLS, 'icon' => 'logos:aws'],
        ];

        // Keep this section strictly as English skills with icons only.
        Skill::query()->delete();

        foreach ($skills as $index => $skill) {
            Skill::query()->create([
                'title' => $skill['title'],
                'description' => $skill['description'],
                'category' => $skill['category'],
                'icon' => $skill['icon'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $resumeItems = [
            [
                'title' => 'تکامل پروژه در یک شرکت',
                'title_en' => 'Evolving one product inside a company',
                'description' => 'در طول سال ها روی کدهای چند نسل مختلف کار کردم؛ تمرکزم بازنویسی بخش های پرریسک و کاهش پیچیدگی بود.',
                'description_en' => 'Over the years I worked across several generations of code, focusing on rewriting high-risk parts and reducing complexity.',
                'start_year' => 1394,
                'start_month' => 1,
                'start_day' => 1,
                'is_current' => true,
            ],
            [
                'title' => 'گزارش گیری چانک/استریم',
                'title_en' => 'Chunked / streamed reporting',
                'description' => 'سیستم خروجی اکسل را بازطراحی کردم تا انتظار طولانی و خطای 504 حذف شود و تاریخچه گزارش ها ثبت بماند.',
                'description_en' => 'Redesigned the Excel export so long waits and 504 errors disappear and report history is kept.',
                'start_year' => 1398,
                'start_month' => 4,
                'start_day' => 1,
                'end_year' => 1400,
                'end_month' => 12,
                'end_day' => 29,
                'is_current' => false,
            ],
            [
                'title' => 'بازنویسی منطق قیمت گذاری',
                'title_en' => 'Pricing-logic rewrite',
                'description' => 'منطق هاردکد قدیمی را به ساختار توسعه پذیر تبدیل کردم تا اضافه کردن قوانین جدید ساده شود.',
                'description_en' => 'Turned hard-coded legacy logic into an extensible structure so new rules are easy to add.',
                'start_year' => 1401,
                'start_month' => 1,
                'start_day' => 1,
                'end_year' => 1402,
                'end_month' => 12,
                'end_day' => 29,
                'is_current' => false,
            ],
            [
                'title' => 'بهینه سازی زیرساخت داده',
                'title_en' => 'Data infrastructure tuning',
                'description' => 'روی دیتابیسی با 180 جدول، با تحلیل لاگ و کوئری های پرتکرار، ایندکس گذاری و اصلاح ساختار انجام دادم.',
                'description_en' => 'On a 180-table database, I analysed logs and hot queries, then re-indexed and fixed the structure.',
                'start_year' => 1403,
                'start_month' => 1,
                'start_day' => 1,
                'is_current' => true,
            ],
        ];

        foreach ($resumeItems as $index => $item) {
            ResumeItem::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'title_en' => $item['title_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'start_year' => $item['start_year'] ?? null,
                    'start_month' => $item['start_month'] ?? null,
                    'start_day' => $item['start_day'] ?? null,
                    'end_year' => $item['end_year'] ?? null,
                    'end_month' => $item['end_month'] ?? null,
                    'end_day' => $item['end_day'] ?? null,
                    'is_current' => (bool) ($item['is_current'] ?? false),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }

        $projectCategories = [
            ['title' => 'اپلیکیشن‌ها', 'title_en' => 'Applications', 'slug' => 'applications'],
            ['title' => 'توسعه وب', 'title_en' => 'Web development', 'slug' => 'web-development'],
            ['title' => 'زیرساخت', 'title_en' => 'Infrastructure', 'slug' => 'infrastructure'],
        ];

        $categoryMap = [];

        foreach ($projectCategories as $index => $category) {
            $record = ProjectCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'title' => $category['title'],
                    'title_en' => $category['title_en'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            $categoryMap[$category['slug']] = $record->id;
        }

        $projects = [
            [
                'title' => 'سیستم گزارش گیری مقاوم',
                'title_en' => 'Resilient reporting system',
                'description' => 'گزارش گیری اکسل با Stream و Queue برای حذف Timeout، نمایش وضعیت دریافت و نگهداری تاریخچه فایل ها.',
                'description_en' => 'Excel reporting built on streams and queues — no more timeouts, live download status and a kept history of files.',
                'tags' => ['Laravel', 'Queue', 'Stream', 'Excel'],
                'brand_color' => '#1d9bf0',
                'cover_type' => Project::COVER_WEB,
                'category_slug' => 'web-development',
            ],
            [
                'title' => 'سامانه Push مقیاس پذیر',
                'title_en' => 'Scalable push platform',
                'description' => 'ارسال پوش دسته ای با فاصله زمانی، ثبت دقیق تاریخچه و جلوگیری از ارسال تکراری برای کاربران.',
                'description_en' => 'Batched push delivery with pacing, exact history logging and duplicate-send protection.',
                'tags' => ['Batch', 'Queue', 'Notification', 'Scalability'],
                'brand_color' => '#8b5cf6',
                'cover_type' => Project::COVER_APP,
                'category_slug' => 'applications',
            ],
            [
                'title' => 'آپلود رمزگذاری شده ویدیو',
                'title_en' => 'Encrypted video upload',
                'description' => 'آپلود تکه ای، رمزگذاری هر Chunk با aes-128-ctr و اتصال نهایی با پردازش صف برای سبک سازی سرور.',
                'description_en' => 'Chunked upload, per-chunk aes-128-ctr encryption and final assembly through queue processing to keep the server light.',
                'tags' => ['AES-128-CTR', 'Chunk Upload', 'Queue'],
                'brand_color' => '#10b981',
                'cover_type' => Project::COVER_BOTH,
                'category_slug' => 'infrastructure',
            ],
        ];

        foreach ($projects as $index => $project) {
            Project::query()->updateOrCreate(
                ['title' => $project['title']],
                [
                    'title_en' => $project['title_en'],
                    'description' => $project['description'],
                    'description_en' => $project['description_en'],
                    'tags' => $project['tags'],
                    'brand_color' => $project['brand_color'],
                    'cover_type' => $project['cover_type'],
                    'project_category_id' => $categoryMap[$project['category_slug']] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }

    }
}
