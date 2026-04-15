<?php

namespace App\Filament\Resources\ProfileSettings\Pages;

use App\Filament\Resources\ProfileSettings\ProfileSettingResource;
use App\Models\ProfileSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfileSettings extends ListRecords
{
    protected static string $resource = ProfileSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('ایجاد وضعیت')
                ->visible(fn (): bool => ProfileSetting::query()->count() === 0),
        ];
    }
}
