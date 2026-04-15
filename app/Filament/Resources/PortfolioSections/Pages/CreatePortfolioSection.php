<?php

namespace App\Filament\Resources\PortfolioSections\Pages;

use App\Filament\Resources\PortfolioSections\PortfolioSectionResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class CreatePortfolioSection extends CreateRecord
{
    protected static string $resource = PortfolioSectionResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    protected array $categoriesManagerState = [];

    protected array $projectsManagerState = [];

    public function getHeading(): string | Htmlable
    {
        return 'مدیریت بخش نمونه کارها';
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->categoriesManagerState = $data['categories_manager'] ?? [];
        $this->projectsManagerState = $data['projects_manager'] ?? [];

        unset($data['categories_manager'], $data['projects_manager']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncCategories($this->categoriesManagerState);
        $this->syncProjects($this->projectsManagerState);
    }

    protected function syncCategories(array $items): void
    {
        $payload = collect($items)
            ->filter(fn (array $item): bool => filled($item['title'] ?? null) && filled($item['slug'] ?? null))
            ->values();

        ProjectCategory::query()->delete();

        foreach ($payload as $index => $item) {
            ProjectCategory::query()->create([
                'title' => trim((string) ($item['title'] ?? '')),
                'slug' => trim((string) ($item['slug'] ?? '')),
                'sort_order' => $index + 1,
                'is_active' => (bool) ($item['is_active'] ?? true),
            ]);
        }
    }

    protected function syncProjects(array $items): void
    {
        $validCategoryIds = ProjectCategory::query()->pluck('id')->all();

        $payload = collect($items)
            ->filter(fn (array $item): bool => filled($item['title'] ?? null) && filled($item['description'] ?? null))
            ->values();

        Project::query()->delete();

        foreach ($payload as $index => $item) {
            Project::query()->create([
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'project_url' => filled($item['project_url'] ?? null) ? trim((string) $item['project_url']) : null,
                'tags' => collect($item['tags'] ?? [])->filter()->values()->all(),
                'image_path' => filled($item['image_path'] ?? null) ? (string) $item['image_path'] : null,
                'project_category_id' => in_array((int) ($item['project_category_id'] ?? 0), $validCategoryIds, true)
                    ? (int) $item['project_category_id']
                    : null,
                'sort_order' => $index + 1,
                'is_active' => (bool) ($item['is_active'] ?? true),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
