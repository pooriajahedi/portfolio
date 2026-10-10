<?php

namespace App\Http\Resources;

use App\Models\ResumeItem;
use App\Support\JalaliCalendar;
use App\Support\LocalizedText;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ResumeItem
 */
class ExperienceItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'title' => LocalizedText::pair($this->title, $this->title_en),
            'text' => LocalizedText::pair($this->description, $this->description_en),
            'period' => [
                'start' => $this->year($this->start_year, $this->start_month, $this->start_day),
                'end' => $this->is_current ? null : $this->year($this->end_year, $this->end_month, $this->end_day),
                'current' => (bool) $this->is_current,
            ],
        ];
    }

    /**
     * The same moment as a Jalali year (Persian site) and a Gregorian year (English site).
     *
     * @return array{fa: int, en: int}|null
     */
    private function year(?int $year, ?int $month, ?int $day): ?array
    {
        if (! $year) {
            return null;
        }

        return [
            'fa' => $year,
            'en' => JalaliCalendar::toGregorianYear($year, $month ?: 1, $day ?: 1),
        ];
    }
}
