<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => (string) $this->title,
            'text' => (string) $this->description,
            'period' => (string) ($this->period ?? ''),
        ];
    }
}
