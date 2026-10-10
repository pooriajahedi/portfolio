<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AboutSection extends Model
{
    public const DEFAULT_SKILL_CATEGORIES = [
        ['key' => 'frontend', 'label' => 'FRONTEND', 'label_en' => 'Frontend', 'is_active' => true],
        ['key' => 'backend', 'label' => 'BACKEND', 'label_en' => 'Backend', 'is_active' => true],
        ['key' => 'database', 'label' => 'DATABASE', 'label_en' => 'Database', 'is_active' => true],
        ['key' => 'tools', 'label' => 'TOOLS', 'label_en' => 'Tools', 'is_active' => true],
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
                $labelEn = trim((string) ($item['label_en'] ?? ''));

                if ($label === '') {
                    return null;
                }

                if ($key === '') {
                    $key = Str::slug($label);
                }

                return [
                    'key' => $key,
                    'label' => $label,
                    'label_en' => $labelEn !== '' ? $labelEn : $label,
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

    /**
     * Active categories in display order, each carrying both language labels.
     *
     * @return array<int, array{key: string, label: string, label_en: string}>
     */
    public static function skillCategoryList(?array $categories = null): array
    {
        return collect(self::normalizedSkillCategories($categories))
            ->filter(fn (array $item): bool => $item['is_active'])
            ->map(fn (array $item): array => [
                'key' => $item['key'],
                'label' => $item['label'],
                'label_en' => $item['label_en'],
            ])
            ->values()
            ->all();
    }
}
