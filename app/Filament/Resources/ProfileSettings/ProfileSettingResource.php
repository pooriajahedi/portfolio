<?php

namespace App\Filament\Resources\ProfileSettings;

use App\Filament\Resources\ProfileSettings\Pages\CreateProfileSetting;
use App\Filament\Resources\ProfileSettings\Pages\EditProfileSetting;
use App\Filament\Resources\ProfileSettings\Pages\ListProfileSettings;
use App\Models\ProfileSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProfileSettingResource extends Resource
{
    protected static ?string $model = ProfileSetting::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'وضعیت فعلی';

    protected static UnitEnum | string | null $navigationGroup = 'تنظیمات سایت';

    protected static ?int $navigationSort = 100;

    protected static ?string $modelLabel = 'وضعیت';

    protected static ?string $pluralModelLabel = 'تنظیم وضعیت فعلی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تنظیمات وضعیت فعالیت')
                    ->schema([
                        Select::make('current_status')
                            ->label('وضعیت فعلی')
                            ->options(ProfileSetting::statusOptions())
                            ->default(ProfileSetting::STATUS_LOOKING_FOR_JOB)
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('current_status')
                    ->label('وضعیت فعلی')
                    ->formatStateUsing(fn (string $state): string => ProfileSetting::statusLabel($state)),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
                    ->since(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfileSettings::route('/'),
            'create' => CreateProfileSetting::route('/create'),
            'edit' => EditProfileSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        $record = ProfileSetting::query()->latest('id')->first();

        if ($record) {
            return static::getUrl('edit', ['record' => $record]);
        }

        return static::getUrl('create');
    }
}
