<?php

namespace App\Filament\Resources\HeroSections\Pages;

use App\Filament\Resources\HeroSections\HeroSectionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class CreateHeroSection extends CreateRecord
{
    protected static string $resource = HeroSectionResource::class;
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
