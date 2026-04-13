<?php

namespace App\Filament\Resources\HeroSections;

use App\Filament\Resources\HeroSections\Pages\ManageHeroSections;
use App\Models\HeroSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSectionResource extends Resource
{
    protected static ?string $model = HeroSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?string $recordTitleAttribute = 'headline';

    protected static ?string $navigationLabel = 'بخش هیرو';

    protected static ?string $modelLabel = 'هیرو';

    protected static ?string $pluralModelLabel = 'بخش هیرو';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوای بخش Hero')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->maxLength(255),
                        TextInput::make('role')
                            ->label('عنوان شغلی')
                            ->maxLength(255),
                        TextInput::make('headline')
                            ->label('تیتر اصلی')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('intro')
                            ->label('توضیح کوتاه')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('highlight_one')
                            ->label('نکته برجسته ۱')
                            ->maxLength(255),
                        TextInput::make('highlight_two')
                            ->label('نکته برجسته ۲')
                            ->maxLength(255),
                        TextInput::make('highlight_three')
                            ->label('نکته برجسته ۳')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('headline')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('headline')
                    ->label('تیتر')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageHeroSections::route('/'),
        ];
    }
}
