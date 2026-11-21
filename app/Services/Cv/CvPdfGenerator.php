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
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CvPdfGenerator
{
    private const STRIP_WIDTH_MM = 13;

    /**
     * Render the CV, store it as the next numbered file in `resume-files/` and return its path on the public disk.
     */
    public function generate(): string
    {
        $relativePath = $this->nextVersionedRelativePath();
        $absolutePath = Storage::disk('public')->path($relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        $data = $this->previewDataForPdf();

        file_put_contents($absolutePath, $this->pdfFromHtml(view('cv.template', $data)->render(), sideStrip: $data['sideStrip']));

        return $relativePath;
    }

    /**
     * The PDF bytes for an HTML document, laid out right-to-left in Vazirmatn.
     *
     * `useOTL` and `useKashida` have to sit inside the font's own `fontdata` entry: mPDF reads them only there and
     * silently ignores them anywhere else. Without them every Persian letter is drawn in its isolated form instead
     * of joining its neighbours, and a low-resolution preview hides the difference.
     */
    public function pdfFromHtml(string $html, bool $compress = true, ?string $sideStrip = null): string
    {
        $tempDir = storage_path('app/mpdf-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $paths = (new ConfigVariables())->getDefaults();
        $fonts = (new FontVariables())->getDefaults();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'tempDir' => $tempDir,
            'fontDir' => array_merge($paths['fontDir'], [resource_path('fonts/vazirmatn')]),
            'fontdata' => $fonts['fontdata'] + [
                'vazirmatn' => [
                    'R' => 'Vazirmatn-Regular.ttf',
                    'B' => 'Vazirmatn-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'vazirmatn',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => false,
        ]);

        $mpdf->SetCompression($compress);
        $mpdf->SetTitle('رزومه');

        if ($sideStrip !== null) {
            // A watermark image repeats on every page, which `position: fixed` does not do in mPDF.
            $mpdf->SetWatermarkImage($sideStrip, 1, [self::STRIP_WIDTH_MM, 297], [210 - self::STRIP_WIDTH_MM, 0]);
            $mpdf->showWatermarkImage = true;
            $mpdf->watermarkImgBehind = true;
        }

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
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
                    'description' => $this->projectSummary((string) $project->description),
                    'tags' => $tags,
                    'url' => $projectUrl,
                ];
            })
            ->all();

        return [
            'name' => (string) ($hero?->name ?: 'پوریا جاهدی'),
            'role' => (string) ($hero?->role ?: 'Senior Backend & Full-Stack Developer'),
            'avatarPath' => $forPdf
                ? ($this->roundedAvatar((string) ($hero?->avatar_image ?? '')) ?? $this->resolveImageSource((string) ($hero?->avatar_image ?? ''), true))
                : $this->resolveImageSource((string) ($hero?->avatar_image ?? ''), false),
            'summary' => trim(implode("\n\n", array_filter([
                (string) ($about?->paragraph_one ?? ''),
                (string) ($about?->paragraph_two ?? ''),
            ]))),
            'codePatternPath' => $forPdf ? null : $this->resolveSideImageSource($cvSideImage, false),
            'sideStrip' => $forPdf ? $this->sideStrip($cvSideImage, $cvSideImagePosition) : null,
            'codePatternPosition' => in_array($cvSideImagePosition, ['left', 'center', 'right'], true) ? $cvSideImagePosition : 'left',
            'skillsByCategory' => $skillsByCategory,
            'skillCategoryLabels' => $skillCategoryLabels,
            'resumeItems' => $resumeItems,
            'projects' => $projects,
            'contactItems' => $this->buildContactItems($contact, $forPdf),
        ];
    }

    /**
     * The first sentences of a project's description, up to about $max characters. The portfolio texts run to
     * several paragraphs, which would turn a two-page CV into four.
     */
    private function projectSummary(string $text, int $max = 360): string
    {
        $sentences = preg_split('/(?<=[.!؟])\s+/u', trim((string) preg_replace('/\s+/u', ' ', $text)), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $summary = '';

        foreach ($sentences as $sentence) {
            $next = trim($summary.' '.$sentence);

            if ($summary !== '' && mb_strlen($next) > $max) {
                break;
            }

            $summary = $next;
        }

        return $summary;
    }

    /**
     * The narrow vertical strip down the page edge, as a JPEG data URI: the side image cropped like CSS
     * `object-fit: cover` would, laid over the dark base colour. Without a usable raster image (none set, or an
     * SVG, which GD cannot read) the strip is just the base colour.
     */
    private function sideStrip(string $path, string $position): string
    {
        $width = self::STRIP_WIDTH_MM * 10;
        $height = 2970;
        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 11, 18, 32));

        $absolutePath = trim($path) === '' ? null : $this->resolveAbsolutePath(trim($path));
        $source = $absolutePath !== null ? @imagecreatefromstring((string) @file_get_contents($absolutePath)) : false;

        if ($source !== false) {
            $cropWidth = max(1, (int) round(imagesy($source) * $width / $height));
            $cropWidth = min($cropWidth, imagesx($source));
            $spare = imagesx($source) - $cropWidth;
            $x = match ($position) {
                'right' => $spare,
                'center' => intdiv($spare, 2),
                default => 0,
            };

            $layer = imagecreatetruecolor($width, $height);
            imagecopyresampled($layer, $source, 0, 0, $x, 0, $width, $height, $cropWidth, imagesy($source));
            imagecopymerge($canvas, $layer, 0, 0, 0, 0, $width, $height, 92);
        }

        ob_start();
        imagejpeg($canvas, null, 88);

        return 'data:image/jpeg;base64,'.base64_encode((string) ob_get_clean());
    }

    /**
     * The avatar as a square, circle-masked PNG data URI. mPDF ignores `border-radius` on images, so the circle is
     * cut here; any failure (no GD, unreadable file) returns null and the original image is used instead.
     */
    private function roundedAvatar(string $path): ?string
    {
        $absolutePath = trim($path) === '' ? null : $this->resolveAbsolutePath(trim($path));

        if ($absolutePath === null || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring((string) @file_get_contents($absolutePath));

        if ($source === false) {
            return null;
        }

        $size = 360;
        $side = min(imagesx($source), imagesy($source));
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 255, 255, 255, 127));
        imagecopyresampled($canvas, $source, 0, 0, intdiv(imagesx($source) - $side, 2), intdiv(imagesy($source) - $side, 2), $size, $size, $side, $side);

        $radius = $size / 2;

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $distance = hypot($x + .5 - $radius, $y + .5 - $radius);

                if ($distance > $radius) {
                    imagesetpixel($canvas, $x, $y, imagecolorallocatealpha($canvas, 255, 255, 255, 127));
                } elseif ($distance > $radius - 1.5) {
                    $rgb = imagecolorsforindex($canvas, imagecolorat($canvas, $x, $y));
                    $alpha = (int) round(127 * ($distance - ($radius - 1.5)) / 1.5);
                    imagesetpixel($canvas, $x, $y, imagecolorallocatealpha($canvas, $rgb['red'], $rgb['green'], $rgb['blue'], $alpha));
                }
            }
        }

        ob_start();
        imagepng($canvas);

        return 'data:image/png;base64,'.base64_encode((string) ob_get_clean());
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
}
