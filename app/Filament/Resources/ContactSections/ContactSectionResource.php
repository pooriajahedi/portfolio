<?php

namespace App\Filament\Resources\ContactSections;

use App\Filament\Resources\ContactSections\Pages\CreateContactSection;
use App\Filament\Resources\ContactSections\Pages\EditContactSection;
use App\Filament\Resources\ContactSections\Pages\ListContactSections;
use App\Models\ContactSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
    protected static ?int $navigationSort = 7;

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
                            ->prefixIcon(Heroicon::OutlinedAtSymbol)
                            ->extraInputAttributes(['style' => 'text-align: left;', 'dir' => 'ltr'])
                            ->maxLength(255),
                        Select::make('email_icon')
                            ->label('آیکن ایمیل')
                            ->options(ContactSection::contactIconPreviewOptions())
                            ->default(ContactSection::ICON_EMAIL)
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
                        TextInput::make('github')
                            ->label('لینک GitHub')
                            ->prefixIcon(Heroicon::OutlinedCodeBracketSquare)
                            ->extraInputAttributes(['style' => 'text-align: left;', 'dir' => 'ltr'])
                            ->maxLength(255),
                        Select::make('github_icon')
                            ->label('آیکن GitHub')
                            ->options(ContactSection::contactIconPreviewOptions())
                            ->default(ContactSection::ICON_GITHUB)
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
                        TextInput::make('linkedin')
                            ->label('لینک LinkedIn')
                            ->prefixIcon(Heroicon::OutlinedUserCircle)
                            ->extraInputAttributes(['style' => 'text-align: left;', 'dir' => 'ltr'])
                            ->maxLength(255),
                        Select::make('linkedin_icon')
                            ->label('آیکن LinkedIn')
                            ->options(ContactSection::contactIconPreviewOptions())
                            ->default(ContactSection::ICON_LINKEDIN)
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
                        TextInput::make('telegram')
                            ->label('آیدی تلگرام')
                            ->prefixIcon(Heroicon::OutlinedPaperAirplane)
                            ->extraInputAttributes(['style' => 'text-align: left;', 'dir' => 'ltr'])
                            ->maxLength(255),
                        Select::make('telegram_icon')
                            ->label('آیکن تلگرام')
                            ->options(ContactSection::contactIconPreviewOptions())
                            ->default(ContactSection::ICON_TELEGRAM)
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
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

    public static function getNavigationUrl(): string
    {
        $record = ContactSection::query()->latest('id')->first();

        if ($record) {
            return static::getUrl('edit', ['record' => $record]);
        }

        return static::getUrl('create');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactSections::route('/'),
            'create' => CreateContactSection::route('/create'),
            'edit' => EditContactSection::route('/{record}/edit'),
        ];
    }
}
