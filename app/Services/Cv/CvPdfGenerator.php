<?php

namespace App\Services\Cv;

use App\Models\AboutSection;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\Project;
use App\Models\ResumeItem;
use App\Models\SiteSetting;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class CvPdfGenerator
{
    public function generate(): string
    {
        $data = $this->previewDataForPdf();
        $relativePath = $this->nextVersionedRelativePath();
        $absolutePath = Storage::disk('public')->path($relativePath);
        $targetDirectory = dirname($absolutePath);

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }

        $tempDir = storage_path('app/browsershot-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $html = view('cv.template', $data + ['isPdf' => true])->render();

        $browser = Browsershot::html($html)
            ->setChromePath($this->resolveChromePath())
            ->setOption('waitUntil', 'networkidle0')
            ->setOption('printBackground', true)
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->format('A4')
            ->timeout(120)
            ->setTemporaryDirectory($tempDir)
            ->newHeadless()
            ->noSandbox();

        if ($nodeBinary = $this->resolveNodeBinary()) {
            $browser->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = $this->resolveNpmBinary()) {
            $browser->setNpmBinary($npmBinary);
        }

        $browser->setIncludePath($this->resolveIncludePath());
        $browser->setNodeModulePath(base_path('node_modules'));

        $browser->savePdf($absolutePath);

        return $relativePath;
    }

    private function nextVersionedRelativePath(): string
    {
        $files = Storage::disk('public')->files('resume-files');
        $maxVersion = 0;

        foreach ($files as $file) {
            $basename = basename($file);

            if (preg_match('/^cv-v(\d+)\.pdf$/i', $basename, $matches) !== 1) {
                continue;
            }

            $version = (int) $matches[1];

            if ($version > $maxVersion) {
                $maxVersion = $version;
            }
        }

        $nextVersion = $maxVersion + 1;

        return sprintf('resume-files/cv-v%03d.pdf', $nextVersion);
    }

    /**
     * @return array<string, mixed>
     */
    public function previewDataForWeb(): array
    {
        return $this->buildPayload(false) + ['isPdf' => false];
    }

    /**
     * @return array<string, mixed>
     */
    public function previewDataForPdf(): array
    {
        return $this->buildPayload(true) + ['isPdf' => true];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(bool $forPdf): array
    {
        $hero = HeroSection::query()->latest('id')->first();
        $about = AboutSection::query()->active()->latest('id')->first();
        $contact = ContactSection::query()->active()->latest('id')->first();
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());
        $cvSideImage = (string) ($siteSettings[SiteSetting::KEY_CV_SIDE_IMAGE] ?? '');
        $cvSideImagePosition = (string) ($siteSettings[SiteSetting::KEY_CV_SIDE_IMAGE_POSITION] ?? 'left');

        $skillCategoryLabels = AboutSection::skillCategoryOptions($about?->skill_categories);

        $skillsByCategory = Skill::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'category'])
            ->groupBy(fn (Skill $skill): string => (string) $skill->category)
            ->map(function ($items): string {
                return $items->pluck('title')->filter()->implode('، ');
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
                'end_year',
                'end_month',
                'is_current',
            ])
            ->map(function (ResumeItem $item): array {
                return [
                    'title' => (string) $item->title,
                    'description' => (string) $item->description,
                    'period' => $this->formatResumePeriod($item),
                ];
            })
            ->all();

        $projects = Project::query()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(6)
            ->get(['title', 'description', 'tags', 'project_url'])
            ->map(function (Project $project): array {
                $tags = collect($project->tags ?? [])
                    ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
                    ->values()
                    ->all();

                $projectUrl = $this->normalizeLinkUrl((string) ($project->project_url ?? ''), 'web');

                return [
                    'title' => (string) $project->title,
                    'description' => trim((string) $project->description),
                    'tags' => $tags,
                    'url' => $projectUrl,
                ];
            })
            ->all();

        return [
            'name' => (string) ($hero?->name ?: 'پوریا جاهدی'),
            'role' => (string) ($hero?->role ?: 'Senior Backend & Full-Stack Developer'),
            'avatarPath' => $this->resolveImageSource((string) ($hero?->avatar_image ?? ''), $forPdf),
            'summary' => trim(implode("\n\n", array_filter([
                (string) ($about?->paragraph_one ?? ''),
                (string) ($about?->paragraph_two ?? ''),
            ]))),
            'codePatternPath' => $this->resolveSideImageSource($cvSideImage, $forPdf),
            'codePatternPosition' => in_array($cvSideImagePosition, ['left', 'center', 'right'], true) ? $cvSideImagePosition : 'left',
            'fontMediumSrc' => $this->resolveFontSource('fonts/pinar/Pinar-DS1-FD-Medium.ttf', $forPdf),
            'fontBoldSrc' => $this->resolveFontSource('fonts/pinar/Pinar-DS1-FD-Bold.ttf', $forPdf),
            'skillsByCategory' => $skillsByCategory,
            'skillCategoryLabels' => $skillCategoryLabels,
            'resumeItems' => $resumeItems,
            'projects' => $projects,
            'contactItems' => $this->buildContactItems($contact, $forPdf),
        ];
    }

    private function resolveWebImagePath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return url('/storage/' . ltrim($path, '/'));
    }

    private function resolvePublicAssetUrl(string $path): string
    {
        return asset(ltrim($path, '/'));
    }

    private function resolveImageSource(string $path, bool $forPdf): ?string
    {
        $normalized = trim($path);

        if ($normalized === '') {
            return null;
        }

        if (str_starts_with($normalized, 'data:')) {
            return $normalized;
        }

        if (! $forPdf) {
            $absolutePath = $this->resolveAbsolutePath($normalized);

            if ($absolutePath !== null) {
                if (str_starts_with($absolutePath, public_path())) {
                    $relativeToPublic = ltrim(str_replace(public_path(), '', $absolutePath), '/');

                    return url('/' . $relativeToPublic);
                }

                if (str_starts_with($absolutePath, Storage::disk('public')->path(''))) {
                    $relativeToStorage = ltrim(str_replace(Storage::disk('public')->path(''), '', $absolutePath), '/');

                    return url('/storage/' . $relativeToStorage);
                }
            }

            return $this->resolveWebImagePath($normalized);
        }

        $absolutePath = $this->resolveAbsolutePath($normalized);

        if ($absolutePath !== null) {
            return $this->fileToDataUri($absolutePath);
        }

        return $this->resolveWebImagePath($normalized);
    }

    private function resolveSideImageSource(string $path, bool $forPdf): string
    {
        $normalized = trim($path);
        $fallback = 'images/backgrounds/code-dark.svg';

        if ($normalized !== '') {
            return $this->resolveImageSource($normalized, $forPdf)
                ?? $this->resolveImageSource($fallback, $forPdf)
                ?? $this->resolvePublicAssetUrl($fallback);
        }

        return $this->resolveImageSource($fallback, $forPdf)
            ?? $this->resolvePublicAssetUrl($fallback);
    }

    private function resolveFontSource(string $relativePublicPath, bool $forPdf): string
    {
        if (! $forPdf) {
            return url('/' . ltrim($relativePublicPath, '/'));
        }

        $absolutePath = public_path(ltrim($relativePublicPath, '/'));

        if (is_file($absolutePath)) {
            return $this->fileToDataUri($absolutePath);
        }

        return url('/' . ltrim($relativePublicPath, '/'));
    }

    private function resolveAbsolutePath(string $path): ?string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        $normalized = ltrim($path, '/');

        if (Storage::disk('public')->exists($normalized)) {
            return Storage::disk('public')->path($normalized);
        }

        $publicPath = public_path($normalized);

        if (is_file($publicPath)) {
            return $publicPath;
        }

        return null;
    }

    private function fileToDataUri(string $absolutePath): ?string
    {
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $contents = @file_get_contents($absolutePath);

        if ($contents === false) {
            return null;
        }

        $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return sprintf('data:%s;base64,%s', $mime, base64_encode($contents));
    }

    /**
     * @return array<int, array{label: string, value: string, icon: string, icon_src: string, href: string|null}>
     */
    private function buildContactItems(?ContactSection $contact, bool $forPdf): array
    {
        $items = [
            [
                'label' => 'ایمیل',
                'value' => (string) ($contact?->email ?? ''),
                'icon' => 'email',
                'icon_src' => $this->contactIconSource('email', $forPdf),
                'href' => $this->normalizeLinkUrl((string) ($contact?->email ?? ''), 'email'),
            ],
            [
                'label' => 'گیت‌هاب',
                'value' => (string) ($contact?->github ?? ''),
                'icon' => 'github',
                'icon_src' => $this->contactIconSource('github', $forPdf),
                'href' => $this->normalizeLinkUrl((string) ($contact?->github ?? ''), 'github'),
            ],
            [
                'label' => 'لینکدین',
                'value' => (string) ($contact?->linkedin ?? ''),
                'icon' => 'linkedin',
                'icon_src' => $this->contactIconSource('linkedin', $forPdf),
                'href' => $this->normalizeLinkUrl((string) ($contact?->linkedin ?? ''), 'linkedin'),
            ],
            [
                'label' => 'تلگرام',
                'value' => (string) ($contact?->telegram ?? ''),
                'icon' => 'telegram',
                'icon_src' => $this->contactIconSource('telegram', $forPdf),
                'href' => $this->normalizeLinkUrl((string) ($contact?->telegram ?? ''), 'telegram'),
            ],
            [
                'label' => 'وب‌سایت',
                //'value' => (string) parse_url((string) config('app.url'), PHP_URL_HOST),
                'value' => 'pooriajahedi.ir',
                'icon' => 'web',
                'icon_src' => $this->contactIconSource('web', $forPdf),
                'href' => $this->normalizeLinkUrl('pooriajahedi.ir', 'web'),
            ],
        ];

        return array_values(array_filter($items, fn (array $item): bool => $item['value'] !== ''));
    }

    private function contactIconSource(string $icon, bool $forPdf): string
    {
        $map = [
            'email' => 'icons/cv--email.svg',
            'github' => 'icons/cv--github.svg',
            'linkedin' => 'icons/cv--linkedin.svg',
            'telegram' => 'icons/cv--telegram.svg',
            'web' => 'icons/cv--web.svg',
        ];

        $path = $map[$icon] ?? 'icons/cv--web.svg';

        if (! $forPdf) {
            return asset($path);
        }

        $absolutePath = public_path(ltrim($path, '/'));

        if (is_file($absolutePath)) {
            return $this->fileToDataUri($absolutePath) ?? asset($path);
        }

        return asset($path);
    }

    private function normalizeLinkUrl(string $value, string $type): ?string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        return match ($type) {
            'email' => str_contains($trimmed, '@') ? 'mailto:' . $trimmed : null,
            'telegram' => $this->normalizeTelegramUrl($trimmed),
            default => $this->ensureHttpScheme($trimmed),
        };
    }

    private function normalizeTelegramUrl(string $value): string
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $username = ltrim($value, '@');

        return 'https://t.me/' . $username;
    }

    private function ensureHttpScheme(string $value): string
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return 'https://' . ltrim($value, '/');
    }

    private function formatResumePeriod(ResumeItem $item): string
    {
        $start = $this->formatJalaliMonthYear($item->start_year, $item->start_month);

        if ($item->is_current) {
            return trim(($start ? $start . ' - ' : '') . 'تا امروز');
        }

        $end = $this->formatJalaliMonthYear($item->end_year, $item->end_month);

        return trim(implode(' - ', array_filter([$start, $end])));
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

        if ($monthLabel === null) {
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

    private function resolveChromePath(): string
    {
        $fromEnv = trim((string) env('BROWSERSHOT_CHROME_PATH', ''));

        if ($fromEnv !== '' && is_file($fromEnv)) {
            return $fromEnv;
        }

        return '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
    }

    private function resolveNodeBinary(): ?string
    {
        $fromEnv = trim((string) env('BROWSERSHOT_NODE_BINARY', ''));

        if ($fromEnv !== '' && is_file($fromEnv) && $this->isSupportedNodePath($fromEnv)) {
            return $fromEnv;
        }

        foreach ($this->candidateNodePaths() as $path) {
            if (is_file($path) && $this->isSupportedNodePath($path)) {
                return $path;
            }
        }

        return null;
    }

    private function resolveNpmBinary(): ?string
    {
        $fromEnv = trim((string) env('BROWSERSHOT_NPM_BINARY', ''));

        if ($fromEnv !== '' && is_file($fromEnv)) {
            return $fromEnv;
        }

        foreach ($this->candidateNpmPaths() as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function resolveIncludePath(): string
    {
        $paths = array_filter(array_unique(array_merge(
            explode(':', (string) getenv('PATH')),
            ['/usr/local/bin', '/opt/homebrew/bin', '/opt/local/bin'],
            $this->candidateNodeBinDirectories()
        )));

        return implode(':', $paths);
    }

    /**
     * @return array<int, string>
     */
    private function candidateNodePaths(): array
    {
        return array_values(array_unique(array_merge(
            ['/usr/local/bin/node', '/opt/homebrew/bin/node', '/opt/local/bin/node'],
            $this->globHomePaths('/.nvm/versions/node/*/bin/node')
        )));
    }

    /**
     * @return array<int, string>
     */
    private function candidateNpmPaths(): array
    {
        return array_values(array_unique(array_merge(
            ['/usr/local/bin/npm', '/opt/homebrew/bin/npm', '/opt/local/bin/npm'],
            $this->globHomePaths('/.nvm/versions/node/*/bin/npm')
        )));
    }

    /**
     * @return array<int, string>
     */
    private function candidateNodeBinDirectories(): array
    {
        return array_values(array_unique(array_map('dirname', array_merge(
            $this->globHomePaths('/.nvm/versions/node/*/bin/node'),
            $this->globHomePaths('/.nvm/versions/node/*/bin/npm')
        ))));
    }

    /**
     * @return array<int, string>
     */
    private function globHomePaths(string $suffixPattern): array
    {
        $home = rtrim((string) getenv('HOME'), '/');

        if ($home === '') {
            return [];
        }

        $matches = glob($home . $suffixPattern) ?: [];
        usort($matches, function (string $a, string $b): int {
            return version_compare($this->extractNodeVersionFromPath($b), $this->extractNodeVersionFromPath($a));
        });

        return $matches;
    }

    private function extractNodeVersionFromPath(string $path): string
    {
        if (preg_match('#/node/v([0-9]+(?:\.[0-9]+){0,2})/#', $path, $matches) === 1) {
            return $matches[1];
        }

        return '0.0.0';
    }

    private function isSupportedNodePath(string $path): bool
    {
        if (preg_match('#/node/v([0-9]+)(?:\.[0-9]+){0,2}/#', $path, $matches) === 1) {
            return (int) $matches[1] >= 18;
        }

        return true;
    }
}
