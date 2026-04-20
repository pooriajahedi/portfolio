<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => (string) $this->title,
            'text' => (string) $this->description,
            'tags' => $this->tags ?? [],
            'projectUrl' => $this->project_url,
            'imageUrl' => $this->resolveImageUrl(),
            'category' => $this->category ? new ProjectCategoryResource($this->category) : null,
        ];
    }

    private function resolveImageUrl(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        $path = trim((string) $this->image_path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/storage/' . ltrim($path, '/');
    }
}
