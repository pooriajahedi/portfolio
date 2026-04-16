<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\BlogPost;
use App\Models\ContactSection;
use App\Models\ContactRequest;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\SiteSetting;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

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
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());

        $backgroundMode = in_array(
            (string) ($siteSettings[SiteSetting::KEY_BACKGROUND_MODE] ?? 'gradient'),
            ['gradient', 'image'],
            true
        ) ? (string) $siteSettings[SiteSetting::KEY_BACKGROUND_MODE] : 'gradient';

        $backgroundImage = trim((string) ($siteSettings[SiteSetting::KEY_BACKGROUND_IMAGE] ?? ''));
        $backgroundImageUrl = null;

        if ($backgroundImage !== '') {
            $backgroundImageUrl = str_starts_with($backgroundImage, 'http://') || str_starts_with($backgroundImage, 'https://') || str_starts_with($backgroundImage, '/')
                ? $backgroundImage
                : '/storage/' . ltrim($backgroundImage, '/');
        }

        $backgroundImageOpacity = $this->sanitizePercentage($siteSettings[SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY] ?? 55);

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
                    'title' => $skill->title,
                    'description' => $skill->description,
                    'category' => $skill->category,
                    'icon' => $icon,
                ];
            })
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

        $blogPosts = BlogPost::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'title',
                'excerpt',
                'content',
                'image_path',
                'created_at',
            ])
            ->map(function (BlogPost $post): array {
                $imageUrl = null;

                if (filled($post->image_path)) {
                    $imagePath = trim((string) $post->image_path);
                    $imageUrl = str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')
                        ? $imagePath
                        : '/storage/' . ltrim($imagePath, '/');
                }

                $excerpt = trim((string) ($post->excerpt ?? ''));

                if ($excerpt === '') {
                    $excerpt = mb_substr(trim(strip_tags((string) $post->content)), 0, 180);
                }

                return [
                    'title' => $post->title,
                    'excerpt' => $excerpt,
                    'content' => (string) $post->content,
                    'imageUrl' => $imageUrl,
                    'date' => $this->formatJalaliFromGregorianDateTime($post->created_at),
                ];
            })
            ->toArray();

        $portfolioData = [
            'profile' => [
                'name' => $hero?->name ?: 'پوریا جاهدی',
                'role' => $hero?->role ?: 'برنامه نویس ارشد بک اند و فول استک',
                'avatarImage' => $hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg',
                'resumeFile' => $hero?->resume_file,
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
            'blogPosts' => $blogPosts,
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
                'mode' => $backgroundMode,
                'backgroundImageUrl' => $backgroundImageUrl,
                'backgroundImageOpacity' => $backgroundImageOpacity / 100,
                'backgroundColorFrom' => $this->sanitizeHexColor((string) ($siteSettings[SiteSetting::KEY_BACKGROUND_COLOR_FROM] ?? '#101829'), '#101829'),
                'backgroundColorTo' => $this->sanitizeHexColor((string) ($siteSettings[SiteSetting::KEY_BACKGROUND_COLOR_TO] ?? '#0b0f19'), '#0b0f19'),
                'primaryColor' => $this->sanitizeHexColor((string) ($siteSettings[SiteSetting::KEY_PRIMARY_COLOR] ?? '#f4c64f'), '#f4c64f'),
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

    private function formatJalaliFromGregorianDateTime($dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        $gy = (int) $dateTime->format('Y');
        $gm = (int) $dateTime->format('m');
        $gd = (int) $dateTime->format('d');

        [$jy, $jm, $jd] = $this->gregorianToJalali($gy, $gm, $gd);

        return $this->formatJalaliDate($jy, $jm, $jd);
    }

    private function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy2 = $gy - 1600;
        $gm2 = $gm - 1;
        $gd2 = $gd - 1;

        $gDayNo = 365 * $gy2 + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400);

        for ($i = 0; $i < $gm2; $i++) {
            $gDayNo += $gDaysInMonth[$i];
        }

        if ($gm2 > 1 && (($gy % 4 === 0 && $gy % 100 !== 0) || $gy % 400 === 0)) {
            $gDayNo++;
        }

        $gDayNo += $gd2;
        $jDayNo = $gDayNo - 79;
        $jNp = intdiv($jDayNo, 12053);
        $jDayNo %= 12053;
        $jy = 979 + 33 * $jNp + 4 * intdiv($jDayNo, 1461);
        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += intdiv($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        for ($i = 0; $i < 11 && $jDayNo >= $jDaysInMonth[$i]; $i++) {
            $jDayNo -= $jDaysInMonth[$i];
        }

        $jm = $i + 1;
        $jd = $jDayNo + 1;

        return [$jy, $jm, $jd];
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

    private function sanitizeHexColor(string $value, string $fallback): string
    {
        $value = trim($value);

        if (! preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return $fallback;
        }

        return strtoupper($value);
    }

    private function sanitizePercentage(mixed $value): int
    {
        return max(0, min(100, (int) $value));
    }

    public function storeContactRequest(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'company_website' => ['nullable', 'string', 'max:1'],
        ], [
            'required' => ':attribute الزامی است.',
            'email' => 'فرمت :attribute معتبر نیست.',
            'min.string' => ':attribute باید حداقل :min کاراکتر باشد.',
            'max.string' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
        ], [
            'name' => 'نام',
            'email' => 'ایمیل',
            'subject' => 'موضوع',
            'message' => 'پیام',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'اطلاعات فرم کامل یا صحیح نیست.',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            return redirect()->to(url('/') . '#contact')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Honeypot: bots usually fill this hidden field.
        if (filled($validated['company_website'] ?? null)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'ارسال نامعتبر شناسایی شد.',
                ], 422);
            }

            return redirect()->to(url('/') . '#contact')->with('contact_error', 'ارسال نامعتبر شناسایی شد.');
        }

        $rateKey = 'contact-request:' . sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'درخواست‌های پیاپی زیاد بود. لطفا کمی بعد دوباره تلاش کنید.',
                ], 429);
            }

            return redirect()->to(url('/') . '#contact')->with('contact_error', 'درخواست‌های پیاپی زیاد بود. لطفا کمی بعد دوباره تلاش کنید.');
        }

        RateLimiter::hit($rateKey, 60);

        ContactRequest::query()->create([
            'name' => trim((string) $validated['name']),
            'email' => trim((string) $validated['email']),
            'subject' => trim((string) $validated['subject']),
            'message' => trim((string) $validated['message']),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'is_read' => false,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'پیام شما با موفقیت ثبت شد.',
            ]);
        }

        return redirect()->to(url('/') . '#contact')->with('contact_success', 'پیام شما با موفقیت ثبت شد.');
    }
}
