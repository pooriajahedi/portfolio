<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public const COVER_WEB = 'web';
    public const COVER_APP = 'app';
    public const COVER_BOTH = 'both';

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'tags',
        'brand_color',
        'cover_type',
        'project_url',
        'image_path',
        'image_path_en',
        'gallery_paths',
        'gallery_paths_en',
        'project_category_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'gallery_paths' => 'array',
            'gallery_paths_en' => 'array',
            'project_category_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function coverTypeOptions(): array
    {
        return [
            self::COVER_WEB => 'وب‌سایت',
            self::COVER_APP => 'اپلیکیشن',
            self::COVER_BOTH => 'وب‌سایت و اپلیکیشن',
        ];
    }

    /**
     * The four image attributes (cover and gallery, per language) out of a form item.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, string|array<int, string>|null>
     */
    public static function mediaFromForm(array $item): array
    {
        $path = fn (string $key): ?string => filled($item[$key] ?? null) ? (string) $item[$key] : null;
        $paths = fn (string $key): array => collect($item[$key] ?? [])->filter()->values()->all();

        return [
            'image_path' => $path('image_path'),
            'gallery_paths' => $paths('gallery_paths'),
            'image_path_en' => $path('image_path_en'),
            'gallery_paths_en' => $paths('gallery_paths_en'),
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
