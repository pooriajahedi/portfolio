<?php

namespace App\Filament\Resources\ProfileSettings\Pages;

use App\Filament\Resources\ProfileSettings\ProfileSettingResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditProfileSetting extends EditRecord
{
    protected static string $resource = ProfileSettingResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
