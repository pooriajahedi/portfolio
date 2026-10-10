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
            'about' => $this['about'] ?? null,
            'skillGroups' => $this['skillGroups'] ?? [],
            'experience' => $this['experience'] ?? [],
            'contact' => $this['contact'] ?? null,
            'links' => $this['links'] ?? [],
            'appVersion' => $this['appVersion'] ?? '1.0.0',
        ];
    }
}
