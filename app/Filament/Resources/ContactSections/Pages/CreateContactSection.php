<?php

namespace App\Filament\Resources\ContactSections\Pages;

use App\Filament\Resources\ContactSections\ContactSectionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class CreateContactSection extends CreateRecord
{
    protected static string $resource = ContactSectionResource::class;
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
