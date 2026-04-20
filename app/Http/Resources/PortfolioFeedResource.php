<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioFeedResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => (string) ($this['title'] ?? 'نمونه کارها'),
            'categories' => $this['categories'] ?? [],
            'projects' => $this['projects'] ?? [],
        ];
    }
}
