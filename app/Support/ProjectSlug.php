<?php

namespace App\Support;

use Illuminate\Support\Str;

class ProjectSlug
{
    public static function make(string $title, int|string|null $id = null): string
    {
        $slug = Str::slug(trim($title));

        if ($slug !== '') {
            return $id ? "{$slug}-{$id}" : $slug;
        }

        return $id ? "project-{$id}" : 'project';
    }
}

