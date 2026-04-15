<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AboutSection extends Model
{
    public const DEFAULT_SKILL_CATEGORIES = [
        ['key' => 'frontend', 'label' => 'FRONTEND', 'is_active' => true],
        ['key' => 'backend', 'label' => 'BACKEND', 'is_active' => true],
        ['key' => 'database', 'label' => 'DATABASE', 'is_active' => true],
        ['key' => 'tools', 'label' => 'TOOLS', 'is_active' => true],
    ];

    protected $fillable = [
        'title',
        'paragraph_one',
        'paragraph_two',
        'skill_categories',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'skill_categories' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function serviceCards(): HasMany
    {
        return $this->hasMany(AboutServiceCard::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function normalizedSkillCategories(?array $categories = null): array
    {
        $categories = is_array($categories) ? $categories : self::DEFAULT_SKILL_CATEGORIES;

        $normalized = collect($categories)
            ->map(function (mixed $item): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $key = trim((string) ($item['key'] ?? ''));
                $label = trim((string) ($item['label'] ?? ''));

                if ($label === '') {
                    return null;
                }

                if ($key === '') {
                    $key = Str::slug($label);
                }

                return [
                    'key' => $key,
                    'label' => $label,
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return $normalized !== [] ? $normalized : self::DEFAULT_SKILL_CATEGORIES;
    }

    public static function skillCategoryOptions(?array $categories = null): array
    {
        return collect(self::normalizedSkillCategories($categories))
            ->filter(fn (array $item): bool => $item['is_active'])
            ->mapWithKeys(fn (array $item): array => [$item['key'] => $item['label']])
            ->all();
    }
}
