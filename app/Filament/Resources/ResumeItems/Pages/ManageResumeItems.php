<?php

namespace App\Filament\Resources\ResumeItems\Pages;

use App\Filament\Resources\ResumeItems\ResumeItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResumeItems extends ManageRecords
{
    protected static string $resource = ResumeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
