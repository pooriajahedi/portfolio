<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const KEY_BACKGROUND_MODE = 'site_background_mode';
    public const KEY_BACKGROUND_IMAGE = 'site_background_image';
    public const KEY_BACKGROUND_IMAGE_OPACITY = 'site_background_image_opacity';
    public const KEY_BACKGROUND_COLOR_FROM = 'site_background_color_from';
    public const KEY_BACKGROUND_COLOR_TO = 'site_background_color_to';
    public const KEY_PRIMARY_COLOR = 'site_primary_color';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function defaults(): array
    {
        return [
            self::KEY_BACKGROUND_MODE => 'gradient',
            self::KEY_BACKGROUND_IMAGE => null,
            self::KEY_BACKGROUND_IMAGE_OPACITY => '55',
            self::KEY_BACKGROUND_COLOR_FROM => '#101829',
            self::KEY_BACKGROUND_COLOR_TO => '#0B0F19',
            self::KEY_PRIMARY_COLOR => '#F4C64F',
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
