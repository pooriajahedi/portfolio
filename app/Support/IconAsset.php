<?php

namespace App\Support;

class IconAsset
{
    protected static array $cache = [];

    public static function normalize(string $icon): string
    {
        $icon = trim($icon);

        return match ($icon) {
            'simple-icons:httptoolkit' => 'mdi:toolbox-outline',
            'logos:gsap' => 'simple-icons:greensock',
            'mdi:telegram-plane' => 'mdi:telegram',
            default => $icon,
        };
    }

    public static function path(?string $icon): ?string
    {
        $icon = self::normalize((string) $icon);

        if ($icon === '') {
            return null;
        }

        return '/icons/' . str_replace(':', '--', $icon) . '.svg';
    }

    public static function img(?string $icon, string $alt = '', int $size = 20, string $class = 'inline-icon'): string
    {
        $path = self::path($icon);

        if (! $path) {
            return '';
        }

        $absolutePath = self::resolveFilesystemPath($path);

        if (! $absolutePath || ! is_file($absolutePath)) {
            return '';
        }

        $svg = self::$cache[$absolutePath] ??= file_get_contents($absolutePath) ?: '';

        if ($svg === '') {
            return '';
        }

        $attributes = [
            'class' => $class,
            'width' => (string) $size,
            'height' => (string) $size,
        ];

        if ($alt !== '') {
            $attributes['role'] = 'img';
            $attributes['aria-label'] = $alt;
        } else {
            $attributes['aria-hidden'] = 'true';
        }

        $attributeString = collect($attributes)
            ->map(fn (string $value, string $key): string => sprintf('%s="%s"', $key, e($value)))
            ->implode(' ');

        return preg_replace('/<svg\b([^>]*)>/i', '<svg$1 ' . $attributeString . '>', $svg, 1) ?: '';
    }

    protected static function resolveFilesystemPath(string $path): ?string
    {
        $relativePath = ltrim($path, '/');

        $candidates = [
            public_path($relativePath),
        ];

        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;

        if (is_string($documentRoot) && $documentRoot !== '') {
            $candidates[] = rtrim($documentRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
