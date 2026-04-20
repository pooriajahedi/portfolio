<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteStateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile' => $this['profile'] ?? [],
            'about' => $this['about'] ?? [],
            'services' => $this['services'] ?? [],
            'skillCategoryLabels' => $this['skillCategoryLabels'] ?? [],
            'skills' => $this['skills'] ?? [],
            'contacts' => $this['contacts'] ?? [],
            'appearance' => $this['appearance'] ?? ['themeStyle' => 'gold'],
            'appVersion' => $this['appVersion'] ?? '1.0.0',
        ];
    }
}
