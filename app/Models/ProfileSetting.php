<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileSetting extends Model
{
    public const STATUS_UNEMPLOYED = 'unemployed';
    public const STATUS_LOOKING_FOR_JOB = 'looking_for_job';
    public const STATUS_RESTING = 'resting';

    protected $fillable = [
        'current_status',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_UNEMPLOYED => 'بیکارم',
            self::STATUS_LOOKING_FOR_JOB => 'دنبال کارم',
            self::STATUS_RESTING => 'در حال استراحتم',
        ];
    }

    public static function statusLabel(string $status): string
    {
        return self::statusOptions()[$status] ?? self::statusOptions()[self::STATUS_LOOKING_FOR_JOB];
    }
}
