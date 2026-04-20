<?php

namespace App\Http\Resources;

use App\Support\BlogSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'slug' => BlogSlug::resolve($this->slug, (string) $this->title, $this->id),
            'title' => (string) $this->title,
            'excerpt' => $this->resolveExcerpt(),
            'content' => (string) $this->content,
            'imageUrl' => $this->resolveImageUrl(),
            'date' => $this->formatJalaliDate(),
        ];
    }

    private function resolveExcerpt(): string
    {
        $excerpt = trim((string) ($this->excerpt ?? ''));

        if ($excerpt !== '') {
            return $excerpt;
        }

        return mb_substr(trim(strip_tags((string) $this->content)), 0, 180);
    }

    private function resolveImageUrl(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        $path = trim((string) $this->image_path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/storage/' . ltrim($path, '/');
    }

    private function formatJalaliDate(): ?string
    {
        if (! $this->created_at) {
            return null;
        }

        $gy = (int) $this->created_at->format('Y');
        $gm = (int) $this->created_at->format('m');
        $gd = (int) $this->created_at->format('d');

        [$jy, $jm, $jd] = $this->gregorianToJalali($gy, $gm, $gd);

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

        return sprintf('%s %s %s', $this->toPersianDigits((string) $jd), $months[$jm] ?? '', $this->toPersianDigits((string) $jy));
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy -= 1600;
        $gm -= 1;
        $gd -= 1;

        $gDayNo = 365 * $gy + intdiv($gy + 3, 4) - intdiv($gy + 99, 100) + intdiv($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $gDayNo += $gDaysInMonth[$i];
        }

        if ($gm > 1 && (($gy + 1600) % 4 === 0 && (($gy + 1600) % 100 !== 0 || ($gy + 1600) % 400 === 0))) {
            $gDayNo += 1;
        }

        $gDayNo += $gd;

        $jDayNo = $gDayNo - 79;

        $jNp = intdiv($jDayNo, 12053);
        $jDayNo %= 12053;

        $jy = 979 + 33 * $jNp + 4 * intdiv($jDayNo, 1461);
        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += intdiv($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        $jm = 0;

        while ($jm < 11 && $jDayNo >= $jDaysInMonth[$jm]) {
            $jDayNo -= $jDaysInMonth[$jm];
            ++$jm;
        }

        $jd = $jDayNo + 1;

        return [$jy, $jm + 1, $jd];
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
