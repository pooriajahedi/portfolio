<?php

namespace App\Filament\Resources\AboutSections\Pages;

use App\Filament\Resources\AboutSections\AboutSectionResource;
use App\Models\AboutSection;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutSections extends ListRecords
{
    protected static string $resource = AboutSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => AboutSection::query()->count() === 0),
        ];
    }
}
