<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public const CATEGORY_FRONTEND = 'frontend';
    public const CATEGORY_BACKEND = 'backend';
    public const CATEGORY_DATABASE = 'database';
    public const CATEGORY_TOOLS = 'tools';

    protected $fillable = [
        'title',
        'description',
        'category',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => 'string',
            'icon' => 'string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_FRONTEND => 'Frontend',
            self::CATEGORY_BACKEND => 'Backend',
            self::CATEGORY_DATABASE => 'Database',
            self::CATEGORY_TOOLS => 'Tools',
        ];
    }

    public static function iconOptions(): array
    {
        return [
            'logos:javascript' => 'JavaScript',
            'logos:typescript-icon' => 'TypeScript',
            'logos:react' => 'React',
            'logos:nextjs-icon' => 'Next.js',
            'logos:vue' => 'Vue.js',
            'logos:nuxt-icon' => 'Nuxt',
            'logos:tailwindcss-icon' => 'Tailwind',
            'logos:bootstrap' => 'Bootstrap',
            'logos:sass' => 'Sass',
            'logos:gsap' => 'GSAP',
            'logos:nodejs-icon' => 'Node.js',
            'logos:nestjs' => 'NestJS',
            'logos:laravel' => 'Laravel',
            'logos:php' => 'PHP',
            'logos:express' => 'Express',
            'logos:mysql' => 'MySQL',
            'logos:postgresql' => 'PostgreSQL',
            'logos:mongodb-icon' => 'MongoDB',
            'logos:redis' => 'Redis',
            'logos:prisma' => 'Prisma',
            'logos:docker-icon' => 'Docker',
            'logos:git-icon' => 'Git',
            'logos:linux-tux' => 'Linux',
            'logos:aws' => 'AWS',
        ];
    }

    public static function iconPreviewOptions(): array
    {
        $options = [];

        foreach (self::iconOptions() as $icon => $label) {
            $iconUrl = 'https://api.iconify.design/' . rawurlencode($icon) . '.svg?width=20&height=20';
            $options[$icon] = sprintf(
                '<span style="display:inline-flex;align-items:center;gap:.5rem;"><img src="%s" alt="%s" style="width:20px;height:20px;object-fit:contain;"><span>%s</span></span>',
                $iconUrl,
                e($label),
                e($label)
            );
        }

        return $options;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
