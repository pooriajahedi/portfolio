<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const KEY_THEME_STYLE = 'site_theme_style';
    public const KEY_SOCIAL_PREVIEW_IMAGE = 'site_social_preview_image';
    public const KEY_CV_SIDE_IMAGE = 'site_cv_side_image';
    public const KEY_CV_SIDE_IMAGE_POSITION = 'site_cv_side_image_position';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function defaults(): array
    {
        return [
            self::KEY_THEME_STYLE => 'gold',
            self::KEY_SOCIAL_PREVIEW_IMAGE => '',
            self::KEY_CV_SIDE_IMAGE => '',
            self::KEY_CV_SIDE_IMAGE_POSITION => 'left',
        ];
    }

    /**
     * @param array<string, mixed> $defaults
     * @return array<string, mixed>
     */
    public static function getMany(array $defaults): array
    {
        $values = static::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        return collect($defaults)
            ->mapWithKeys(fn (mixed $default, string $key): array => [
                $key => (array_key_exists($key, $values) && filled($values[$key])) ? $values[$key] : $default,
            ])
            ->all();
    }

    /**
     * @param array<string, mixed> $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => (string) $key],
                ['value' => filled($value) ? (string) $value : null],
            );
        }
    }
}
