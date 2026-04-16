<?php

namespace App\Models;

use App\Support\IconAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactSection extends Model
{
    public const ICON_EMAIL = 'logos:google-gmail';
    public const ICON_GITHUB = 'mdi:github';
    public const ICON_LINKEDIN = 'mdi:linkedin';
    public const ICON_TELEGRAM = 'mdi:telegram';

    protected $fillable = [
        'title',
        'description',
        'email',
        'email_icon',
        'github',
        'github_icon',
        'linkedin',
        'linkedin_icon',
        'telegram',
        'telegram_icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_icon' => 'string',
            'github_icon' => 'string',
            'linkedin_icon' => 'string',
            'telegram_icon' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public static function contactIconOptions(): array
    {
        return [
            self::ICON_EMAIL => 'Gmail',
            'mdi:email-outline' => 'Email',
            self::ICON_GITHUB => 'GitHub',
            'mdi:github-circle' => 'GitHub Circle',
            self::ICON_LINKEDIN => 'LinkedIn',
            'mdi:linkedin-box' => 'LinkedIn Box',
            self::ICON_TELEGRAM => 'Telegram',
            'mdi:phone-outline' => 'Phone',
            'mdi:web' => 'Web',
        ];
    }

    public static function contactIconPreviewOptions(): array
    {
        $options = [];

        foreach (self::contactIconOptions() as $icon => $label) {
            $options[$icon] = sprintf(
                '<span style="display:inline-flex;align-items:center;gap:.5rem;">%s<span>%s</span></span>',
                IconAsset::img($icon, $label, 20),
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
