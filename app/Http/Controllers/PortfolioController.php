<?php

namespace App\Http\Controllers;

use App\Models\HeroSection;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $hero = HeroSection::query()->latest('id')->first();
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());

        $name = trim((string) ($hero?->name ?: 'پوریا جاهدی'));
        $role = trim((string) ($hero?->role ?: 'برنامه‌نویس بک‌اند و فول‌استک'));
        $tagline = trim((string) ($hero?->tagline ?: 'پورتفولیوی شخصی برای معرفی مهارت‌ها، نمونه‌کارها و رزومه.'));

        $socialPreviewPath = trim((string) ($siteSettings[SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE] ?? ''));
        $fallbackAvatarPath = trim((string) ($hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg'));

        $metaImage = $this->toPublicUrl($socialPreviewPath !== '' ? $socialPreviewPath : $fallbackAvatarPath);

        $metaTitle = $name !== '' && $role !== ''
            ? "{$name} | {$role}"
            : ($name !== '' ? $name : 'پوریا جاهدی | برنامه‌نویس بک‌اند و فول‌استک');

        $metaDescription = Str::limit(preg_replace('/\s+/u', ' ', strip_tags($tagline)) ?: '', 180, '...');

        return view('welcome', [
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage' => $metaImage,
            'metaUrl' => url('/'),
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
