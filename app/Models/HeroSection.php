<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    protected $fillable = [
        'name',
        'role',
        'avatar_image',
        'headline',
        'intro',
        'highlight_one',
        'highlight_two',
        'highlight_three',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'avatar_image' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
