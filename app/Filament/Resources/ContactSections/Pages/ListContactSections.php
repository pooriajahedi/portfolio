<?php

namespace App\Filament\Resources\ContactSections\Pages;

use App\Filament\Resources\ContactSections\ContactSectionResource;
use App\Models\ContactSection;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactSections extends ListRecords
{
    protected static string $resource = ContactSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => ContactSection::query()->count() === 0),
        ];
    }
}
