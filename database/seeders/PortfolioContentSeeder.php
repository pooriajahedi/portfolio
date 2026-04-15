<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\AboutServiceCard;
use App\Models\BlogPost;
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
                'role' => 'برنامه نویس ارشد بک اند و فول استک',
                'avatar_image' => '/images/hero/pooria-hero.jpeg',
                'current_status' => HeroSection::STATUS_LOOKING_FOR_JOB,
            ],
        );

        AboutSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'درباره من',
                'paragraph_one' => 'من در طی یک دهه فعالیت حرفه ای، تقریبا تمام چرخه حیات یک محصول را تجربه کرده ام: از نگهداری کدهای قدیمی و پرچالش تا بازنویسی ماژول های کلیدی و مهاجرت کامل سیستم های Legacy به معماری مدرن. رویکرد من همیشه این بوده که علاوه بر حل مشکل امروز، مسیر توسعه فردا را هم باز نگه دارم.',
                'paragraph_two' => 'علاقه اصلی من کم کردن پیچیدگی، استانداردسازی خروجی APIها، بهینه سازی کوئری ها و طراحی جریان های پایدار برای پردازش های سنگین است.',
                'skill_categories' => AboutSection::DEFAULT_SKILL_CATEGORIES,
                'is_active' => true,
            ],
        );

        $aboutSectionId = AboutSection::query()->value('id');

        if ($aboutSectionId) {
            $serviceCards = [
                ['title' => 'بهینه‌سازی سیستم‌های در حال اجرا', 'description' => 'تحلیل گلوگاه‌ها، کاهش خطاهای زمان‌بر و افزایش پایداری سرویس‌ها.'],
                ['title' => 'بازنویسی بخش‌های Legacy', 'description' => 'تبدیل منطق‌های قدیمی و هاردکد به معماری قابل توسعه و تست‌پذیر.'],
                ['title' => 'طراحی API و خروجی استاندارد', 'description' => 'بازطراحی خروجی وب‌سرویس‌ها برای مصرف‌پذیری بهتر و نگهداری ساده‌تر.'],
                ['title' => 'بهینه‌سازی دیتابیس و کوئری', 'description' => 'ایندکس‌گذاری، بازنویسی کوئری‌های پرتکرار و استفاده هدفمند از کش.'],
            ];

            foreach ($serviceCards as $index => $card) {
                AboutServiceCard::query()->updateOrCreate(
                    [
                        'about_section_id' => $aboutSectionId,
                        'title' => $card['title'],
                    ],
                    [
                        'description' => $card['description'],
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
                'description' => 'اگر دنبال همکاری برای بهینه سازی یک محصول در حال اجرا، بازنویسی بخش های حساس یا توسعه فیچر جدید هستید، خوشحال می شوم گفتگو کنیم.',
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
                'description' => 'در طول سال ها روی کدهای چند نسل مختلف کار کردم؛ تمرکزم بازنویسی بخش های پرریسک و کاهش پیچیدگی بود.',
                'start_year' => 1394,
                'start_month' => 1,
                'start_day' => 1,
                'is_current' => true,
            ],
            [
                'title' => 'گزارش گیری چانک/استریم',
                'description' => 'سیستم خروجی اکسل را بازطراحی کردم تا انتظار طولانی و خطای 504 حذف شود و تاریخچه گزارش ها ثبت بماند.',
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
                'description' => 'منطق هاردکد قدیمی را به ساختار توسعه پذیر تبدیل کردم تا اضافه کردن قوانین جدید ساده شود.',
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
                'description' => 'روی دیتابیسی با 180 جدول، با تحلیل لاگ و کوئری های پرتکرار، ایندکس گذاری و اصلاح ساختار انجام دادم.',
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
                    'description' => $item['description'],
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
            ['title' => 'اپلیکیشن‌ها', 'slug' => 'applications'],
            ['title' => 'توسعه وب', 'slug' => 'web-development'],
            ['title' => 'زیرساخت', 'slug' => 'infrastructure'],
        ];

        $categoryMap = [];

        foreach ($projectCategories as $index => $category) {
            $record = ProjectCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'title' => $category['title'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            $categoryMap[$category['slug']] = $record->id;
        }

        $projects = [
            [
                'title' => 'سیستم گزارش گیری مقاوم',
                'description' => 'گزارش گیری اکسل با Stream و Queue برای حذف Timeout، نمایش وضعیت دریافت و نگهداری تاریخچه فایل ها.',
                'tags' => ['Laravel', 'Queue', 'Stream', 'Excel'],
                'category_slug' => 'web-development',
            ],
            [
                'title' => 'سامانه Push مقیاس پذیر',
                'description' => 'ارسال پوش دسته ای با فاصله زمانی، ثبت دقیق تاریخچه و جلوگیری از ارسال تکراری برای کاربران.',
                'tags' => ['Batch', 'Queue', 'Notification', 'Scalability'],
                'category_slug' => 'applications',
            ],
            [
                'title' => 'آپلود رمزگذاری شده ویدیو',
                'description' => 'آپلود تکه ای، رمزگذاری هر Chunk با aes-128-ctr و اتصال نهایی با پردازش صف برای سبک سازی سرور.',
                'tags' => ['AES-128-CTR', 'Chunk Upload', 'Queue'],
                'category_slug' => 'infrastructure',
            ],
        ];

        foreach ($projects as $index => $project) {
            Project::query()->updateOrCreate(
                ['title' => $project['title']],
                [
                    'description' => $project['description'],
                    'tags' => $project['tags'],
                    'project_category_id' => $categoryMap[$project['category_slug']] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }

        $blogPosts = [
            [
                'title' => 'Using Custom Tailwind Classes in the Filament Panel (Laravel 12 + Filament 3)',
                'excerpt' => 'راهنمای حل ناسازگاری Tailwind v4 در Laravel 12 با Filament 3 (Tailwind v3) و پیاده‌سازی تم سفارشی بدون شکستن استایل پیش‌فرض پنل.',
                'content' => '<p>این مقاله تجربه عملی من برای استفاده از کلاس‌های سفارشی Tailwind در پنل Filament روی Laravel 12 است. چالش اصلی، ناسازگاری نسخه‌ها بود: Laravel 12 به‌صورت پیش‌فرض Tailwind v4 دارد اما Filament 3 روی Tailwind v3 کار می‌کند.</p><p>در این مطلب مسیر کامل را توضیح داده‌ام: ساخت theme اختصاصی، ثبت آن در Panel Provider، اضافه‌کردن فایل theme در ورودی‌های Vite، و تنظیم preset/preflight برای جلوگیری از override شدن استایل‌های پیش‌فرض Filament.</p><p><a href=\"https://medium.com/@pooria.jahedi/using-custom-tailwind-classes-in-the-filament-panel-laravel-12-filament-3-4c49473a2048?source=user_profile_page---------0-------------b7419a727440----------------------\" target=\"_blank\" rel=\"noopener noreferrer\">مشاهده مقاله کامل در Medium</a></p>',
            ],
            [
                'title' => 'چطور یک ماژول Legacy را بدون توقف سرویس بازنویسی کردم',
                'excerpt' => 'تجربه عملی بازنویسی تدریجی ماژول‌های قدیمی با کمترین ریسک برای کاربران نهایی.',
                'content' => '<p>در این مقاله تجربه بازنویسی تدریجی یک ماژول Legacy را بررسی می‌کنم. تمرکز اصلی روی کاهش ریسک انتشار، جلوگیری از Downtime و حفظ سازگاری با داده‌های قبلی بود.</p><p>اول از همه جریان‌های پرترافیک را با لاگ دقیق شناسایی کردیم، سپس مسیرهای جدید را به صورت Feature Flag وارد کردیم تا بتوانیم تدریجی سوئیچ کنیم.</p>',
            ],
            [
                'title' => 'بهینه‌سازی کوئری‌های سنگین در دیتابیس‌های بزرگ',
                'excerpt' => 'چک‌لیست عملی برای تحلیل، بازنویسی و ایندکس‌گذاری کوئری‌های پرتکرار در سیستم‌های واقعی.',
                'content' => '<p>وقتی تعداد جدول‌ها بالا می‌رود، بهینه‌سازی کورکورانه جواب نمی‌دهد. در این مسیر ابتدا از لاگ کوئری برای پیدا کردن مسیرهای پرتکرار استفاده کردم.</p><p>بعد از بازنویسی Queryهای پرهزینه، ایندکس‌ها بر اساس الگوی مصرف واقعی تعریف شدند و بار سیستم به شکل محسوسی کاهش پیدا کرد.</p>',
            ],
        ];

        foreach ($blogPosts as $index => $post) {
            BlogPost::query()->updateOrCreate(
                ['title' => $post['title']],
                [
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
