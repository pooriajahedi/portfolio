<?php

namespace App\Filament\Resources\PortfolioSections\Pages;

use App\Filament\Resources\PortfolioSections\PortfolioSectionResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditPortfolioSection extends EditRecord
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['categories_manager'] = ProjectCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'sort_order', 'is_active'])
            ->map(fn (ProjectCategory $category): array => [
                'id' => (string) $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
            ])
            ->all();

        $data['projects_manager'] = Project::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'title',
                'description',
                'tags',
                'project_url',
                'image_path',
                'project_category_id',
                'sort_order',
                'is_active',
            ])
            ->map(fn (Project $project): array => [
                'id' => (string) $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'tags' => $project->tags ?? [],
                'project_url' => $project->project_url,
                'image_path' => $project->image_path,
                'project_category_id' => $project->project_category_id,
                'sort_order' => $project->sort_order,
                'is_active' => $project->is_active,
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->categoriesManagerState = $data['categories_manager'] ?? [];
        $this->projectsManagerState = $data['projects_manager'] ?? [];

        unset($data['categories_manager'], $data['projects_manager']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncCategories($this->categoriesManagerState);
        $this->syncProjects($this->projectsManagerState);
    }

    protected function syncCategories(array $items): void
    {
        $payload = collect($items)
            ->filter(fn (array $item): bool => filled($item['title'] ?? null) && filled($item['slug'] ?? null))
            ->values();

        $seenIds = [];

        foreach ($payload as $index => $item) {
            $data = [
                'title' => trim((string) ($item['title'] ?? '')),
                'slug' => trim((string) ($item['slug'] ?? '')),
                'sort_order' => $index + 1,
                'is_active' => (bool) ($item['is_active'] ?? true),
            ];

            $id = filled($item['id'] ?? null) ? (int) $item['id'] : null;

            if ($id) {
                $category = ProjectCategory::query()->find($id);

                if ($category) {
                    $category->update($data);
                    $seenIds[] = $category->id;
                    continue;
                }
            }

            $seenIds[] = ProjectCategory::query()->create($data)->id;
        }

        ProjectCategory::query()
            ->when($seenIds !== [], fn ($query) => $query->whereNotIn('id', $seenIds))
            ->delete();
    }

    protected function syncProjects(array $items): void
    {
        $validCategoryIds = ProjectCategory::query()->pluck('id')->all();

        $payload = collect($items)
            ->filter(fn (array $item): bool => filled($item['title'] ?? null) && filled($item['description'] ?? null))
            ->values();

        $seenIds = [];

        foreach ($payload as $index => $item) {
            $data = [
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
            ];

            $id = filled($item['id'] ?? null) ? (int) $item['id'] : null;

            if ($id) {
                $project = Project::query()->find($id);

                if ($project) {
                    $project->update($data);
                    $seenIds[] = $project->id;
                    continue;
                }
            }

            $seenIds[] = Project::query()->create($data)->id;
        }

        Project::query()
            ->when($seenIds !== [], fn ($query) => $query->whereNotIn('id', $seenIds))
            ->delete();
    }
}
