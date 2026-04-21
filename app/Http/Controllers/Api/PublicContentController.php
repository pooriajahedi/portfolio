<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\PortfolioFeedResource;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectCategoryResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ResumeItemResource;
use App\Http\Resources\SiteStateResource;
use App\Models\AboutSection;
use App\Models\BlogPost;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Support\BlogSlug;
use App\Support\ProjectSlug;
use App\Support\PublicApiCache;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicContentController extends Controller
{
    public function site(): SiteStateResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::SITE_KEY, fn (): array => $this->buildSitePayload());

        return new SiteStateResource($payload);
    }

    public function resume(): AnonymousResourceCollection
    {
        $items = PublicApiCache::remember(PublicApiCache::RESUME_KEY, function (): array {
            return ResumeItem::query()
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
                ->map(function (ResumeItem $item): ResumeItem {
                    $item->period = $this->formatResumePeriod($item);
                    return $item;
                })
                ->all();
        });

        return ResumeItemResource::collection(collect($items));
    }

    public function portfolio(): PortfolioFeedResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::PORTFOLIO_KEY, function (): array {
            $portfolioSection = PortfolioSection::query()->latest('id')->first();

            $categories = ProjectCategory::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['title', 'slug']);

            $projects = Project::query()
                ->active()
                ->with('category:id,title,slug')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'slug',
                    'description',
                    'tags',
                    'project_url',
                    'image_path',
                    'gallery_paths',
                    'project_category_id',
                    'created_at',
                    'updated_at',
                ]);

            return [
                'title' => $portfolioSection?->title ?: 'نمونه کارها',
                'categories' => ProjectCategoryResource::collection($categories)->resolve(),
                'projects' => ProjectResource::collection($projects)->resolve(),
            ];
        });

        return new PortfolioFeedResource($payload);
    }

    public function blogPosts(): AnonymousResourceCollection
    {
        $posts = PublicApiCache::remember(PublicApiCache::BLOG_KEY, function (): array {
            return BlogPost::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'image_path',
                    'created_at',
                ])
                ->all();
        });

        return BlogPostResource::collection(collect($posts));
    }

    public function blogPost(string $slug): BlogPostResource
    {
        $post = PublicApiCache::remember(PublicApiCache::blogPostKey($slug), function () use ($slug): BlogPost {
            $posts = BlogPost::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'image_path',
                    'created_at',
                ]);

            $matched = $posts->first(
                fn (BlogPost $item): bool => BlogSlug::resolve($item->slug, (string) $item->title, $item->id) === $slug
            );

            abort_unless($matched, 404);

            return $matched;
        });

        return new BlogPostResource($post);
    }

    public function portfolioProject(string $slug): ProjectDetailResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::portfolioProjectKey($slug), function () use ($slug): array {
            $projects = Project::query()
                ->active()
                ->with('category:id,title,slug')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'slug',
                    'description',
                    'tags',
                    'project_url',
                    'image_path',
                    'gallery_paths',
                    'project_category_id',
                    'created_at',
                    'updated_at',
                ]);

            $project = $projects->first(
                fn (Project $item): bool => ProjectSlug::resolve($item->slug, (string) $item->title, $item->id) === $slug
            );

            abort_unless($project, 404);

            return [
                'project' => $project,
            ];
        });

        return new ProjectDetailResource($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSitePayload(): array
    {
        $hero = HeroSection::query()->latest('id')->first();
        $about = AboutSection::query()
            ->active()
            ->with(['serviceCards' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('id')])
            ->latest('id')
            ->first();
        $contact = ContactSection::query()->active()->latest('id')->first();
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());

        $themeStyle = in_array(
            (string) ($siteSettings[SiteSetting::KEY_THEME_STYLE] ?? 'gold'),
            ['gold', 'green'],
            true
        ) ? (string) $siteSettings[SiteSetting::KEY_THEME_STYLE] : 'gold';

        $skills = Skill::query()
            ->active()
            ->whereNotNull('icon')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description', 'category', 'icon'])
            ->map(function (Skill $skill): array {
                $icon = (string) ($skill->icon ?? '');

                if ($icon === 'simple-icons:httptoolkit') {
                    $icon = 'mdi:toolbox-outline';
                }

                return [
                    'title' => (string) $skill->title,
                    'description' => (string) $skill->description,
                    'category' => (string) $skill->category,
                    'icon' => $icon,
                ];
            })
            ->toArray();

        $services = $about?->serviceCards
            ?->map(fn ($card): array => [
                'title' => (string) $card->title,
                'description' => (string) $card->description,
            ])
            ->values()
            ->all() ?? [];

        return [
            'profile' => [
                'name' => $hero?->name ?: 'پوریا جاهدی',
                'role' => $hero?->role ?: 'برنامه نویس ارشد بک اند و فول استک',
                'avatarImage' => $hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg',
                'resumeFile' => $hero?->resume_file,
                'resumeFileVersion' => $hero?->updated_at?->timestamp,
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
            'services' => $services,
            'skillCategoryLabels' => AboutSection::skillCategoryOptions($about?->skill_categories),
            'skills' => $skills,
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
            'appearance' => [
                'themeStyle' => $themeStyle,
            ],
            'appVersion' => (string) config('app.version', '1.0.0'),
        ];
    }

    private function formatResumePeriod(ResumeItem $item): ?string
    {
        $start = $this->formatJalaliMonthYear($item->start_year, $item->start_month);

        if ($item->is_current) {
            return collect([$start, 'تا اکنون'])->filter()->implode(' تا ');
        }

        $end = $this->formatJalaliMonthYear($item->end_year, $item->end_month);

        return collect([$start, $end])->filter()->implode(' تا ');
    }

    private function formatJalaliMonthYear(?int $year, ?int $month): ?string
    {
        if (! $year || ! $month) {
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

        return $monthLabel . ' ' . $this->toPersianDigits((string) $year);
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
