<?php

namespace App\Http\Resources;

use App\Models\Project;
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
        $descriptionEn = trim((string) $this->description_en);

        return [
            'id' => (int) $this->id,
            'slug' => ProjectSlug::resolve($this->slug, (string) $this->title, $this->id),
            'title' => [
                'fa' => (string) $this->title,
                'en' => $this->title_en ?: (string) $this->title,
            ],
            'text' => [
                'fa' => $description,
                'en' => $descriptionEn !== '' ? $descriptionEn : $description,
            ],
            'tags' => $this->tags ?? [],
            'brandColor' => $this->resolveBrandColor(),
            'coverType' => $this->resolveCoverType(),
            'projectUrl' => $this->project_url,
            'image' => $this->localizedImage(),
            'gallery' => $this->localizedGallery(),
            'category' => $this->category ? new ProjectCategoryResource($this->category) : null,
            'readingMinutes' => $this->estimateReadingMinutes($description),
            'publishedAt' => optional($this->created_at)?->toDateString(),
        ];
    }

    private function resolveBrandColor(): ?string
    {
        $color = trim((string) $this->brand_color);

        return preg_match('/^#[0-9a-f]{6}$/i', $color) === 1 ? strtolower($color) : null;
    }

    private function resolveCoverType(): string
    {
        return array_key_exists((string) $this->cover_type, Project::coverTypeOptions())
            ? (string) $this->cover_type
            : Project::COVER_WEB;
    }

    private function estimateReadingMinutes(string $text): int
    {
        $words = preg_split('/\s+/u', trim(strip_tags($text))) ?: [];
        $count = count(array_filter($words, fn ($word): bool => $word !== ''));

        return max(1, (int) ceil($count / 80));
    }

    /**
     * The cover for each language; a language without its own cover borrows the other one.
     *
     * @return array{fa: string|null, en: string|null}
     */
    private function localizedImage(): array
    {
        $fa = $this->resolvePath($this->image_path);
        $en = $this->resolvePath($this->image_path_en);

        return ['fa' => $fa ?? $en, 'en' => $en ?? $fa];
    }

    /**
     * @return array{fa: array<int, string>, en: array<int, string>}
     */
    private function localizedGallery(): array
    {
        $fa = $this->resolveGallery($this->gallery_paths);
        $en = $this->resolveGallery($this->gallery_paths_en);

        return ['fa' => $fa !== [] ? $fa : $en, 'en' => $en !== [] ? $en : $fa];
    }

    /**
     * @return array<int, string>
     */
    private function resolveGallery(mixed $items): array
    {
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
