<?php

namespace Database\Seeders;

use App\Models\AboutSection;
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
                'is_active' => true,
            ],
        );

        ContactSection::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'تماس با من',
                'description' => 'اگر دنبال همکاری برای بهینه سازی یک محصول در حال اجرا، بازنویسی بخش های حساس یا توسعه فیچر جدید هستید، خوشحال می شوم گفتگو کنیم.',
                'email' => 'you@example.com',
                'github' => 'github.com/your-username',
                'linkedin' => 'linkedin.com/in/your-username',
                'telegram' => '@yourid',
                'is_active' => true,
            ],
        );

        $skills = [
            ['title' => 'Laravel و معماری بک اند', 'description' => 'طراحی ماژولار، API Resource، Queue، Cache، تست و نگهداری سیستم های قدیمی.'],
            ['title' => 'بهینه سازی دیتابیس', 'description' => 'تحلیل کوئری های پرتکرار، ایندکس گذاری هدفمند، بهبود ساختار جداول و ستون ها.'],
            ['title' => 'مقیاس پذیری و پایداری', 'description' => 'طراحی صف برای پردازش های سنگین مثل Push Notification و گزارش گیری.'],
            ['title' => 'Vue.js و SPA', 'description' => 'ساخت رابط کاربری مدرن، سریع و قابل توسعه با رویکرد کامپوننت محور.'],
        ];

        foreach ($skills as $index => $skill) {
            Skill::query()->updateOrCreate(
                ['title' => $skill['title']],
                [
                    'description' => $skill['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
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
