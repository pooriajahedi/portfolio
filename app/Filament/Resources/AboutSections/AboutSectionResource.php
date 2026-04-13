<?php

namespace App\Filament\Resources\AboutSections;

use App\Filament\Resources\AboutSections\Pages\ManageAboutSections;
use App\Models\AboutSection;
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

class AboutSectionResource extends Resource
{
    protected static ?string $model = AboutSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'درباره من';

    protected static ?string $modelLabel = 'درباره من';

    protected static ?string $pluralModelLabel = 'درباره من';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوای بخش درباره من')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('paragraph_one')
                            ->label('پاراگراف اول')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                        Textarea::make('paragraph_two')
                            ->label('پاراگراف دوم')
                            ->rows(5)
                            ->columnSpanFull(),
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
            ->recordTitleAttribute('title')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان')
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
            'index' => ManageAboutSections::route('/'),
        ];
    }
}
