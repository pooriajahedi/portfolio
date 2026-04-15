<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    public const STATUS_CURRENTLY_WORKING = 'currently_working';
    public const STATUS_UNEMPLOYED = 'unemployed';
    public const STATUS_LOOKING_FOR_JOB = 'looking_for_job';
    public const STATUS_RESTING = 'resting';

    protected $fillable = [
        'name',
        'role',
        'avatar_image',
        'current_status',
    ];

    protected function casts(): array
    {
        return [
            'avatar_image' => 'string',
            'current_status' => 'string',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_CURRENTLY_WORKING => 'مشغول همکاری حرفه‌ای در یک تیم فعال',
            self::STATUS_UNEMPLOYED => 'در حال برنامه‌ریزی برای موقعیت حرفه‌ای بعدی',
            self::STATUS_LOOKING_FOR_JOB => 'آماده همکاری در فرصت‌های حرفه‌ای جدید',
            self::STATUS_RESTING => 'در دوره بازآموزی و ارتقای مهارت‌ها',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusOptions()[$status ?? self::STATUS_LOOKING_FOR_JOB] ?? self::statusOptions()[self::STATUS_LOOKING_FOR_JOB];
    }
}
