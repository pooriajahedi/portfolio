<?php

namespace App\Http\Resources;

use App\Support\ProjectSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $description = trim((string) $this->description);

        return [
            'id' => (int) $this->id,
            'slug' => ProjectSlug::make((string) $this->title, $this->id),
            'title' => (string) $this->title,
            'text' => $description,
            'tags' => $this->tags ?? [],
            'projectUrl' => $this->project_url,
            'imageUrl' => $this->resolveImageUrl(),
            'galleryImages' => $this->resolveGalleryImages(),
            'category' => $this->category ? new ProjectCategoryResource($this->category) : null,
            'readingMinutes' => $this->estimateReadingMinutes($description),
            'publishedAt' => optional($this->created_at)?->toDateString(),
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

    private function estimateReadingMinutes(string $text): int
    {
        $words = preg_split('/\s+/u', trim(strip_tags($text))) ?: [];
        $count = count(array_filter($words, fn ($word): bool => $word !== ''));

        return max(1, (int) ceil($count / 80));
    }

    /**
     * @return array<int, string>
     */
    private function resolveGalleryImages(): array
    {
        $items = $this->gallery_paths ?? [];

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn ($path): ?string => $this->resolvePath($path))
            ->filter(fn (?string $value): bool => filled($value))
            ->values()
            ->all();
    }

    private function resolvePath(mixed $path): ?string
    {
        $value = trim((string) $path);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        return '/storage/' . ltrim($value, '/');
    }
}
