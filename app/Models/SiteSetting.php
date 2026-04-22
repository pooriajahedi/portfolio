<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const KEY_THEME_STYLE = 'site_theme_style';
    public const KEY_SOCIAL_PREVIEW_IMAGE = 'site_social_preview_image';
    public const KEY_CV_SIDE_IMAGE = 'site_cv_side_image';
    public const KEY_CV_SIDE_IMAGE_POSITION = 'site_cv_side_image_position';
    public const KEY_MATRIX_ENABLED = 'site_matrix_enabled';
    public const KEY_MATRIX_OPACITY = 'site_matrix_opacity';
    public const KEY_BACKGROUND_MODE = 'site_background_mode';
    public const KEY_BACKGROUND_IMAGE = 'site_background_image';
    public const KEY_BACKGROUND_IMAGE_OPACITY = 'site_background_image_opacity';
    public const KEY_BACKGROUND_SOLID_COLOR = 'site_background_solid_color';
    public const KEY_BACKGROUND_GRADIENT_FROM = 'site_background_gradient_from';
    public const KEY_BACKGROUND_GRADIENT_TO = 'site_background_gradient_to';

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
            self::KEY_MATRIX_ENABLED => '1',
            self::KEY_MATRIX_OPACITY => '56',
            // When matrix is disabled, this controls what shows instead.
            self::KEY_BACKGROUND_MODE => 'gradient',
            self::KEY_BACKGROUND_IMAGE => '',
            self::KEY_BACKGROUND_IMAGE_OPACITY => '100',
            self::KEY_BACKGROUND_SOLID_COLOR => '#0a0b0f',
            self::KEY_BACKGROUND_GRADIENT_FROM => '#0a0b0f',
            self::KEY_BACKGROUND_GRADIENT_TO => '#101827',
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
