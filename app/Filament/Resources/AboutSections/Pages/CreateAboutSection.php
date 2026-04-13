<?php

namespace App\Filament\Resources\AboutSections\Pages;

use App\Filament\Resources\AboutSections\AboutSectionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class CreateAboutSection extends CreateRecord
{
    protected static string $resource = AboutSectionResource::class;
    protected Width | string | null $maxContentWidth = Width::Full;

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
