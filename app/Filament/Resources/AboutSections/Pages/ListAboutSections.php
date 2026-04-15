<?php

namespace App\Filament\Resources\AboutSections\Pages;

use App\Filament\Resources\AboutSections\AboutSectionResource;
use Filament\Resources\Pages\ListRecords;

class ListAboutSections extends ListRecords
{
    protected static string $resource = AboutSectionResource::class;

    public function mount(): void
    {
        abort(404);
    }
}
