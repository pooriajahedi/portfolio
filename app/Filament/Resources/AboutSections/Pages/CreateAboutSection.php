<?php

namespace App\Filament\Resources\AboutSections\Pages;

use App\Filament\Resources\AboutSections\AboutSectionResource;
use App\Models\Skill;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class CreateAboutSection extends CreateRecord
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->skillsManagerState = $data['skills_manager'] ?? [];
        unset($data['skills_manager']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $payload = collect($this->skillsManagerState)
            ->map(function (array $item): array {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'category' => trim((string) ($item['category'] ?? '')),
                    'icon' => filled($item['icon'] ?? null) ? (string) $item['icon'] : null,
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ];
            })
            ->filter(fn (array $item): bool => $item['title'] !== '' && $item['category'] !== '')
            ->values();

        Skill::query()->delete();

        foreach ($payload as $index => $item) {
            Skill::query()->create([
                ...$item,
                'sort_order' => $index + 1,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
