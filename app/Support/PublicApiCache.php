<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class PublicApiCache
{
    public const VERSION_KEY = 'public_api.version';
    public const SITE_KEY = 'public_api.site';
    public const PORTFOLIO_KEY = 'public_api.portfolio';

    public static function remember(string $key, Closure $callback, int $ttlSeconds = 1800): mixed
    {
        $versionedKey = self::buildKey($key);

        return Cache::remember($versionedKey, now()->addSeconds($ttlSeconds), $callback);
    }

    public static function flush(): void
    {
        Cache::increment(self::VERSION_KEY);
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [
            self::SITE_KEY,
            self::PORTFOLIO_KEY,
        ];
    }

    public static function portfolioProjectKey(string $slug): string
    {
        return self::PORTFOLIO_KEY . '.show.' . $slug;
    }

    private static function buildKey(string $key): string
    {
        return $key . '.v' . self::version();
    }

    private static function version(): int
    {
        return (int) Cache::rememberForever(self::VERSION_KEY, fn (): int => 1);
    }
}
