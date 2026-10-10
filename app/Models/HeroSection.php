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
        'name_en',
        'role',
        'role_en',
        'tagline',
        'tagline_en',
        'avatar_image',
        'resume_file',
        'current_status',
    ];

    protected function casts(): array
    {
        return [
            'avatar_image' => 'string',
            'resume_file' => 'string',
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

    public static function statusOptionsEn(): array
    {
        return [
            self::STATUS_CURRENTLY_WORKING => 'Currently working with a team',
            self::STATUS_UNEMPLOYED => 'Planning my next role',
            self::STATUS_LOOKING_FOR_JOB => 'Available for new opportunities',
            self::STATUS_RESTING => 'On a learning break',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusOptions()[$status ?? self::STATUS_LOOKING_FOR_JOB] ?? self::statusOptions()[self::STATUS_LOOKING_FOR_JOB];
    }

    public static function statusLabelEn(?string $status): string
    {
        return self::statusOptionsEn()[$status ?? self::STATUS_LOOKING_FOR_JOB] ?? self::statusOptionsEn()[self::STATUS_LOOKING_FOR_JOB];
    }
}
