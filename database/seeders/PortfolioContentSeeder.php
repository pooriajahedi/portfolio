<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\AboutServiceCard;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\ProfileSetting;
use App\Models\Project;
use App\Models\ResumeItem;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        ProfileSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'current_status' => ProfileSetting::STATUS_LOOKING_FOR_JOB,
            ],
        );

        HeroSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'پوریا جاهدی',
                'role' => 'برنامه نویس ارشد بک اند و فول استک',
                'avatar_image' => '/images/hero/pooria-hero.jpeg',
                'headline' => 'توسعه دهنده ای که سیستم های واقعی را سریع تر، پایدارتر و قابل توسعه تر می کند.',
                'intro' => 'حدود ۱۰ سال تجربه توسعه نرم افزار دارم. تمرکز اصلی من روی بهینه سازی سیستم های بزرگ، بازنویسی معماری های فرسوده و تحویل خروجی پایدار در شرایط واقعی کسب وکار است.',
                'highlight_one' => '۱۰ سال تجربه عملی',
                'highlight_two' => '۱ میلیون کاربر فعال',
                'highlight_three' => 'تمرکز روی Laravel و Vue',
                'is_active' => true,
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
            ['title' => 'تکامل پروژه در یک شرکت', 'description' => 'در طول سال ها روی کدهای چند نسل مختلف کار کردم؛ تمرکزم بازنویسی بخش های پرریسک و کاهش پیچیدگی بود.'],
            ['title' => 'گزارش گیری چانک/استریم', 'description' => 'سیستم خروجی اکسل را بازطراحی کردم تا انتظار طولانی و خطای 504 حذف شود و تاریخچه گزارش ها ثبت بماند.'],
            ['title' => 'بازنویسی منطق قیمت گذاری', 'description' => 'منطق هاردکد قدیمی را به ساختار توسعه پذیر تبدیل کردم تا اضافه کردن قوانین جدید ساده شود.'],
            ['title' => 'بهینه سازی زیرساخت داده', 'description' => 'روی دیتابیسی با 180 جدول، با تحلیل لاگ و کوئری های پرتکرار، ایندکس گذاری و اصلاح ساختار انجام دادم.'],
        ];

        foreach ($resumeItems as $index => $item) {
            ResumeItem::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => $item['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }

        $projects = [
            [
                'title' => 'سیستم گزارش گیری مقاوم',
                'description' => 'گزارش گیری اکسل با Stream و Queue برای حذف Timeout، نمایش وضعیت دریافت و نگهداری تاریخچه فایل ها.',
                'tags' => ['Laravel', 'Queue', 'Stream', 'Excel'],
            ],
            [
                'title' => 'سامانه Push مقیاس پذیر',
                'description' => 'ارسال پوش دسته ای با فاصله زمانی، ثبت دقیق تاریخچه و جلوگیری از ارسال تکراری برای کاربران.',
                'tags' => ['Batch', 'Queue', 'Notification', 'Scalability'],
            ],
            [
                'title' => 'آپلود رمزگذاری شده ویدیو',
                'description' => 'آپلود تکه ای، رمزگذاری هر Chunk با aes-128-ctr و اتصال نهایی با پردازش صف برای سبک سازی سرور.',
                'tags' => ['AES-128-CTR', 'Chunk Upload', 'Queue'],
            ],
        ];

        foreach ($projects as $index => $project) {
            Project::query()->updateOrCreate(
                ['title' => $project['title']],
                [
                    'description' => $project['description'],
                    'tags' => $project['tags'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
