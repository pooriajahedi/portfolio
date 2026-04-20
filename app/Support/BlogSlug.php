<?php

namespace App\Support;

use Illuminate\Support\Str;

class BlogSlug
{
    public static function resolve(?string $manualSlug, string $title, int|string|null $id = null): string
    {
        $manual = Str::slug(trim((string) ($manualSlug ?? '')));

        if ($manual !== '') {
            return $manual;
        }

        return self::make($title, $id);
    }

    public static function make(string $title, int|string|null $id = null): string
    {
        $slug = Str::slug(trim($title));

        if ($slug !== '') {
            return $id ? "{$slug}-{$id}" : $slug;
        }

        return $id ? "blog-{$id}" : 'blog';
    }
}
