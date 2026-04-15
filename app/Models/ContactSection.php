<?php

namespace App\Models;

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
            $iconUrl = 'https://api.iconify.design/' . rawurlencode($icon) . '.svg?width=20&height=20';
            $options[$icon] = sprintf(
                '<span style="display:inline-flex;align-items:center;gap:.5rem;"><img src="%s" alt="%s" style="width:20px;height:20px;object-fit:contain;"><span>%s</span></span>',
                $iconUrl,
                e($label),
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
