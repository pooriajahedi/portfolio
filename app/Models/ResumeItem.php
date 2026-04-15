<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ResumeItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_year',
        'start_month',
        'start_day',
        'end_year',
        'end_month',
        'end_day',
        'is_current',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'start_month' => 'integer',
            'start_day' => 'integer',
            'end_year' => 'integer',
            'end_month' => 'integer',
            'end_day' => 'integer',
            'is_current' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
