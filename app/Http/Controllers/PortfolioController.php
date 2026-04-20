<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\HeroSection;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $hero = HeroSection::query()->latest('id')->first();
        $about = AboutSection::query()->active()->latest('id')->first();
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());

        $themeStyle = in_array(
            (string) ($siteSettings[SiteSetting::KEY_THEME_STYLE] ?? 'gold'),
            ['gold', 'green'],
            true
        ) ? (string) $siteSettings[SiteSetting::KEY_THEME_STYLE] : 'gold';

        $name = trim((string) ($hero?->name ?: 'پوریا جاهدی'));
        $role = trim((string) ($hero?->role ?: 'برنامه‌نویس بک‌اند و فول‌استک'));
        $descriptionSource = trim((string) ($about?->paragraph_one ?: 'پورتفولیوی شخصی برای معرفی مهارت‌ها، رزومه، نمونه‌کارها و مقالات تخصصی.'));

        $socialPreviewPath = trim((string) ($siteSettings[SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE] ?? ''));
        $fallbackAvatarPath = trim((string) ($hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg'));

        $metaImage = $this->toPublicUrl($socialPreviewPath !== '' ? $socialPreviewPath : $fallbackAvatarPath);

        $metaTitle = $name !== '' && $role !== ''
            ? "{$name} | {$role}"
            : ($name !== '' ? $name : 'پوریا جاهدی | برنامه‌نویس بک‌اند و فول‌استک');

        $metaDescription = Str::limit(preg_replace('/\s+/u', ' ', strip_tags($descriptionSource)) ?: '', 180, '...');
        $metaUrl = url('/');

        return view('welcome', [
            'themeStyle' => $themeStyle,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage' => $metaImage,
            'metaUrl' => $metaUrl,
        ]);
    }

    private function toPublicUrl(?string $path): string
    {
        $value = trim((string) $path);

        if ($value === '') {
            return url('/images/hero/pooria-hero.jpeg');
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        if (Str::startsWith($value, '/')) {
            return url($value);
        }

        return url('/storage/' . ltrim($value, '/'));
    }
}
