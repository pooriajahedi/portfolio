<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $hero = HeroSection::query()->latest('id')->first();
        $about = AboutSection::query()
            ->active()
            ->with(['serviceCards' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('id')])
            ->latest('id')
            ->first();
        $contact = ContactSection::query()->active()->latest('id')->first();
        $portfolioSection = PortfolioSection::query()->latest('id')->first();

        $skills = Skill::query()
            ->active()
            ->whereNotNull('icon')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description', 'category', 'icon'])
            ->toArray();

        $resumeItems = ResumeItem::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'title',
                'description',
                'start_year',
                'start_month',
                'start_day',
                'end_year',
                'end_month',
                'end_day',
                'is_current',
            ])
            ->map(fn (ResumeItem $item): array => [
                'title' => $item->title,
                'text' => $item->description,
                'period' => $this->formatResumePeriod($item),
            ])
            ->toArray();

        $projects = Project::query()
            ->active()
            ->with('category:id,title,slug')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'title',
                'description',
                'tags',
                'project_url',
                'image_path',
                'project_category_id',
            ])
            ->map(function (Project $project): array {
                $imageUrl = null;

                if (filled($project->image_path)) {
                    $imagePath = trim((string) $project->image_path);
                    $imageUrl = str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')
                        ? $imagePath
                        : '/storage/' . ltrim($imagePath, '/');
                }

                return [
                    'title' => $project->title,
                    'text' => $project->description,
                    'tags' => $project->tags ?? [],
                    'projectUrl' => $project->project_url,
                    'imageUrl' => $imageUrl,
                    'category' => [
                        'title' => $project->category?->title,
                        'slug' => $project->category?->slug,
                    ],
                ];
            })
            ->toArray();

        $projectCategories = ProjectCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'slug'])
            ->map(fn (ProjectCategory $category): array => [
                'title' => $category->title,
                'slug' => $category->slug,
            ])
            ->toArray();

        $portfolioData = [
            'profile' => [
                'name' => $hero?->name ?: 'پوریا جاهدی',
                'role' => $hero?->role ?: 'برنامه نویس ارشد بک اند و فول استک',
                'avatarImage' => $hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg',
                'currentStatus' => [
                    'key' => $hero?->current_status ?: HeroSection::STATUS_LOOKING_FOR_JOB,
                    'label' => HeroSection::statusLabel($hero?->current_status ?: HeroSection::STATUS_LOOKING_FOR_JOB),
                ],
            ],
            'about' => [
                'title' => $about?->title ?: 'درباره من',
                'paragraphOne' => $about?->paragraph_one ?: 'من در طی یک دهه فعالیت حرفه ای، تقریبا تمام چرخه حیات یک محصول را تجربه کرده ام: از نگهداری کدهای قدیمی و پرچالش تا بازنویسی ماژول های کلیدی و مهاجرت کامل سیستم های Legacy به معماری مدرن. رویکرد من همیشه این بوده که علاوه بر حل مشکل امروز، مسیر توسعه فردا را هم باز نگه دارم.',
                'paragraphTwo' => $about?->paragraph_two ?: 'علاقه اصلی من کم کردن پیچیدگی، استانداردسازی خروجی APIها، بهینه سازی کوئری ها و طراحی جریان های پایدار برای پردازش های سنگین است.',
            ],
            'services' => $about?->serviceCards
                ?->map(fn ($card): array => [
                    'title' => $card->title,
                    'description' => $card->description,
                ])
                ->values()
                ->all() ?? [],
            'skillCategoryLabels' => AboutSection::skillCategoryOptions($about?->skill_categories),
            'skills' => $skills ?: [
                [
                    'title' => 'Laravel و معماری بک اند',
                    'description' => 'طراحی ماژولار، API Resource، Queue، Cache، تست و نگهداری سیستم های قدیمی.',
                    'category' => Skill::CATEGORY_BACKEND,
                    'icon' => 'logos:laravel',
                ],
                [
                    'title' => 'بهینه سازی دیتابیس',
                    'description' => 'تحلیل کوئری های پرتکرار، ایندکس گذاری هدفمند، بهبود ساختار جداول و ستون ها.',
                    'category' => Skill::CATEGORY_DATABASE,
                    'icon' => 'logos:mysql',
                ],
            ],
            'timeline' => $resumeItems ?: [
                [
                    'title' => 'تکامل پروژه در یک شرکت',
                    'text' => 'در طول سال ها روی کدهای چند نسل مختلف کار کردم؛ تمرکزم بازنویسی بخش های پرریسک و کاهش پیچیدگی بود.',
                    'period' => 'فروردین ۱۳۹۴ تا اکنون',
                ],
            ],
            'projects' => $projects,
            'projectCategories' => $projectCategories ?: [
                [
                    'title' => 'همه',
                    'slug' => 'all',
                ],
            ],
            'portfolio' => [
                'title' => $portfolioSection?->title ?: 'نمونه کارها',
            ],
            'contacts' => [
                'title' => $contact?->title ?: 'تماس با من',
                'description' => $contact?->description ?: 'اگر دنبال همکاری برای بهینه سازی یک محصول در حال اجرا، بازنویسی بخش های حساس یا توسعه فیچر جدید هستید، خوشحال می شوم گفتگو کنیم.',
                'email' => $contact?->email ?: 'you@example.com',
                'emailIcon' => $contact?->email_icon ?: ContactSection::ICON_EMAIL,
                'github' => $contact?->github ?: 'github.com/your-username',
                'githubIcon' => $contact?->github_icon ?: ContactSection::ICON_GITHUB,
                'linkedin' => $contact?->linkedin ?: 'linkedin.com/in/your-username',
                'linkedinIcon' => $contact?->linkedin_icon ?: ContactSection::ICON_LINKEDIN,
                'telegram' => $contact?->telegram ?: '@yourid',
                'telegramIcon' => $contact?->telegram_icon ?: ContactSection::ICON_TELEGRAM,
            ],
        ];

        if (empty($portfolioData['services'])) {
            $portfolioData['services'] = collect($portfolioData['skills'])->take(4)->map(fn (array $skill): array => [
                'title' => $skill['title'],
                'description' => $skill['description'],
            ])->values()->all();
        }

        return view('welcome', [
            'portfolioData' => $portfolioData,
        ]);
    }

    private function formatResumePeriod(ResumeItem $item): ?string
    {
        $start = $this->formatJalaliDate($item->start_year, $item->start_month, $item->start_day);

        if ($item->is_current) {
            return collect([$start, 'تا اکنون'])->filter()->implode(' تا ');
        }

        $end = $this->formatJalaliDate($item->end_year, $item->end_month, $item->end_day);

        return collect([$start, $end])->filter()->implode(' تا ');
    }

    private function formatJalaliDate(?int $year, ?int $month, ?int $day): ?string
    {
        if (! $year || ! $month || ! $day) {
            return null;
        }

        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        $monthLabel = $months[$month] ?? null;

        if (! $monthLabel) {
            return null;
        }

        return $this->toPersianDigits((string) $day) . ' ' . $monthLabel . ' ' . $this->toPersianDigits((string) $year);
    }

    private function toPersianDigits(string $value): string
    {
        return strtr($value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ]);
    }
}
