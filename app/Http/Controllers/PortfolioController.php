<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\Project;
use App\Models\ResumeItem;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $hero = HeroSection::query()->active()->latest('id')->first();
        $about = AboutSection::query()->active()->latest('id')->first();
        $contact = ContactSection::query()->active()->latest('id')->first();

        $skills = Skill::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description'])
            ->toArray();

        $resumeItems = ResumeItem::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description'])
            ->toArray();

        $projects = Project::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description', 'tags', 'project_url'])
            ->map(function (Project $project): array {
                return [
                    'title' => $project->title,
                    'text' => $project->description,
                    'tags' => $project->tags ?? [],
                    'projectUrl' => $project->project_url,
                ];
            })
            ->toArray();

        $portfolioData = [
            'profile' => [
                'name' => $hero?->name ?: 'پوریا جاهدی',
                'role' => $hero?->role ?: 'برنامه نویس ارشد بک اند و فول استک',
                'headline' => $hero?->headline ?: 'توسعه دهنده ای که سیستم های واقعی را سریع تر، پایدارتر و قابل توسعه تر می کند.',
                'intro' => $hero?->intro ?: 'حدود ۱۰ سال تجربه توسعه نرم افزار دارم. تمرکز اصلی من روی بهینه سازی سیستم های بزرگ، بازنویسی معماری های فرسوده و تحویل خروجی پایدار در شرایط واقعی کسب وکار است.',
                'highlights' => array_values(array_filter([
                    $hero?->highlight_one,
                    $hero?->highlight_two,
                    $hero?->highlight_three,
                ])),
            ],
            'about' => [
                'title' => $about?->title ?: 'درباره من',
                'paragraphOne' => $about?->paragraph_one ?: 'من در طی یک دهه فعالیت حرفه ای، تقریبا تمام چرخه حیات یک محصول را تجربه کرده ام: از نگهداری کدهای قدیمی و پرچالش تا بازنویسی ماژول های کلیدی و مهاجرت کامل سیستم های Legacy به معماری مدرن. رویکرد من همیشه این بوده که علاوه بر حل مشکل امروز، مسیر توسعه فردا را هم باز نگه دارم.',
                'paragraphTwo' => $about?->paragraph_two ?: 'علاقه اصلی من کم کردن پیچیدگی، استانداردسازی خروجی APIها، بهینه سازی کوئری ها و طراحی جریان های پایدار برای پردازش های سنگین است.',
            ],
            'skills' => $skills ?: [
                [
                    'title' => 'Laravel و معماری بک اند',
                    'description' => 'طراحی ماژولار، API Resource، Queue، Cache، تست و نگهداری سیستم های قدیمی.',
                ],
                [
                    'title' => 'بهینه سازی دیتابیس',
                    'description' => 'تحلیل کوئری های پرتکرار، ایندکس گذاری هدفمند، بهبود ساختار جداول و ستون ها.',
                ],
            ],
            'timeline' => array_map(fn (array $item): array => [
                'title' => $item['title'],
                'text' => $item['description'],
            ], $resumeItems) ?: [
                [
                    'title' => 'تکامل پروژه در یک شرکت',
                    'text' => 'در طول سال ها روی کدهای چند نسل مختلف کار کردم؛ تمرکزم بازنویسی بخش های پرریسک و کاهش پیچیدگی بود.',
                ],
            ],
            'projects' => $projects ?: [
                [
                    'title' => 'سیستم گزارش گیری مقاوم',
                    'text' => 'گزارش گیری اکسل با Stream و Queue برای حذف Timeout، نمایش وضعیت دریافت و نگهداری تاریخچه فایل ها.',
                    'tags' => ['Laravel', 'Queue', 'Stream'],
                    'projectUrl' => null,
                ],
            ],
            'contacts' => [
                'title' => $contact?->title ?: 'تماس با من',
                'description' => $contact?->description ?: 'اگر دنبال همکاری برای بهینه سازی یک محصول در حال اجرا، بازنویسی بخش های حساس یا توسعه فیچر جدید هستید، خوشحال می شوم گفتگو کنیم.',
                'email' => $contact?->email ?: 'you@example.com',
                'github' => $contact?->github ?: 'github.com/your-username',
                'linkedin' => $contact?->linkedin ?: 'linkedin.com/in/your-username',
                'telegram' => $contact?->telegram ?: '@yourid',
            ],
        ];

        if (empty($portfolioData['profile']['highlights'])) {
            $portfolioData['profile']['highlights'] = ['۱۰ سال تجربه عملی', '۱ میلیون کاربر فعال', 'تمرکز روی Laravel و Vue'];
        }

        return view('welcome', [
            'portfolioData' => $portfolioData,
        ]);
    }
}
