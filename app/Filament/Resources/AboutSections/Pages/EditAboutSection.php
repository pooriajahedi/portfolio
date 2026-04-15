<?php

namespace App\Filament\Resources\AboutSections\Pages;

use App\Filament\Resources\AboutSections\AboutSectionResource;
use App\Models\Skill;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditAboutSection extends EditRecord
{
    protected static string $resource = AboutSectionResource::class;
    protected Width | string | null $maxContentWidth = Width::Full;
    protected array $skillsManagerState = [];

    public function getHeading(): string | Htmlable
    {
        return 'محتوای بخش درباره من';
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
        $data['skills_manager'] = Skill::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'category', 'icon', 'is_active'])
            ->map(fn (Skill $skill): array => [
                'id' => (string) $skill->id,
                'title' => $skill->title,
                'description' => $skill->description,
                'category' => $skill->category,
                'icon' => $skill->icon,
                'is_active' => $skill->is_active,
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->skillsManagerState = $data['skills_manager'] ?? [];
        unset($data['skills_manager']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncSkillsManager($this->skillsManagerState);
    }

    protected function syncSkillsManager(array $items): void
    {
        $payload = collect($items)
            ->map(function (array $item): array {
                return [
                    'id' => filled($item['id'] ?? null) ? (int) $item['id'] : null,
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'category' => trim((string) ($item['category'] ?? '')),
                    'icon' => filled($item['icon'] ?? null) ? (string) $item['icon'] : null,
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ];
            })
            ->filter(fn (array $item): bool => $item['title'] !== '' && $item['category'] !== '')
            ->values();

        $seenIds = [];

        foreach ($payload as $index => $item) {
            $data = [
                'title' => $item['title'],
                'description' => $item['description'],
                'category' => $item['category'],
                'icon' => $item['icon'],
                'sort_order' => $index + 1,
                'is_active' => $item['is_active'],
            ];

            if ($item['id']) {
                $skill = Skill::query()->find($item['id']);

                if ($skill) {
                    $skill->update($data);
                    $seenIds[] = $skill->id;
                    continue;
                }
            }

            $seenIds[] = Skill::query()->create($data)->id;
        }

        Skill::query()
            ->when($seenIds !== [], fn ($query) => $query->whereNotIn('id', $seenIds))
            ->delete();
    }

}
