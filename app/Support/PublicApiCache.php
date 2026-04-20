<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class PublicApiCache
{
    public const SITE_KEY = 'public_api.site';
    public const RESUME_KEY = 'public_api.resume';
    public const PORTFOLIO_KEY = 'public_api.portfolio';
    public const BLOG_KEY = 'public_api.blog';

    public static function remember(string $key, Closure $callback, int $ttlSeconds = 1800): mixed
    {
        return Cache::remember($key, now()->addSeconds($ttlSeconds), $callback);
    }

    public static function flush(): void
    {
        foreach (self::keys() as $key) {
            Cache::forget($key);
        }
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [
            self::SITE_KEY,
            self::RESUME_KEY,
            self::PORTFOLIO_KEY,
            self::BLOG_KEY,
        ];
    }
}
