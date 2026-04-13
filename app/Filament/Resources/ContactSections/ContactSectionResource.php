<?php

namespace App\Filament\Resources\ContactSections;

use App\Filament\Resources\ContactSections\Pages\ManageContactSections;
use App\Models\ContactSection;
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

class ContactSectionResource extends Resource
{
    protected static ?string $model = ContactSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'تماس با من';

    protected static ?string $modelLabel = 'تماس با من';

    protected static ?string $pluralModelLabel = 'تماس با من';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوای تماس')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('توضیح')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('github')
                            ->label('لینک GitHub')
                            ->maxLength(255),
                        TextInput::make('linkedin')
                            ->label('لینک LinkedIn')
                            ->maxLength(255),
                        TextInput::make('telegram')
                            ->label('آیدی تلگرام')
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
            ->recordTitleAttribute('title')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('ایمیل')
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
            'index' => ManageContactSections::route('/'),
        ];
    }
}
