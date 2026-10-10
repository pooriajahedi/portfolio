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
            'skillGroups' => $this['skillGroups'] ?? [],
            'links' => $this['links'] ?? [],
            'appearance' => $this['appearance'] ?? ['starfield' => ['enabled' => true, 'opacity' => 56]],
            'appVersion' => $this['appVersion'] ?? '1.0.0',
        ];
    }
}
