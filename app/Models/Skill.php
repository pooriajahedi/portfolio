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
        $about = AboutSection::query()->active()->latest('id')->first();
        $options = AboutSection::skillCategoryOptions($about?->skill_categories);

        if ($options !== []) {
            return $options;
        }

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
            // Frontend
            'logos:javascript' => 'JavaScript',
            'logos:typescript-icon' => 'TypeScript',
            'logos:react' => 'React',
            'logos:nextjs-icon' => 'Next.js',
            'logos:vue' => 'Vue.js',
            'logos:nuxt-icon' => 'Nuxt',
            'logos:pinia' => 'Pinia',
            'logos:redux' => 'Redux',
            'logos:tailwindcss-icon' => 'Tailwind',
            'logos:bootstrap' => 'Bootstrap',
            'logos:sass' => 'Sass',
            'logos:gsap' => 'GSAP',
            'logos:vitest' => 'Vitest',
            'logos:jest' => 'Jest',

            // Backend
            'logos:nodejs-icon' => 'Node.js',
            'logos:nestjs' => 'NestJS',
            'logos:laravel' => 'Laravel',
            'logos:php' => 'PHP',
            'logos:express' => 'Express',
            'logos:graphql' => 'GraphQL',

            // Database
            'logos:mysql' => 'MySQL',
            'logos:postgresql' => 'PostgreSQL',
            'logos:mongodb-icon' => 'MongoDB',
            'logos:redis' => 'Redis',
            'logos:prisma' => 'Prisma',

            // Tools / DevOps
            'logos:docker-icon' => 'Docker',
            'logos:git-icon' => 'Git',
            'logos:github-icon' => 'GitHub',
            'logos:gitlab' => 'GitLab',
            'logos:linux-tux' => 'Linux',
            'logos:aws' => 'AWS',
            'logos:google-cloud' => 'Google Cloud',
            'logos:firebase' => 'Firebase',
            'logos:figma' => 'Figma',
            'logos:postman-icon' => 'Postman',
            'simple-icons:androidstudio' => 'Android Studio',
            'simple-icons:bruno' => 'Bruno',
            'mdi:toolbox-outline' => 'HTTP Toolkit',
            'simple-icons:insomnia' => 'Insomnia',
            'simple-icons:swagger' => 'Swagger',
            'simple-icons:vite' => 'Vite',
            'simple-icons:pnpm' => 'pnpm',
            'simple-icons:npm' => 'npm',
            'simple-icons:yarn' => 'Yarn',
            'simple-icons:filament' => 'Filament',
            'logos:visual-studio-code' => 'VS Code',
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
