<?php

namespace App\Filament\Resources\ContactRequests;

use App\Filament\Resources\ContactRequests\Pages\ManageContactRequests;
use App\Models\ContactRequest;
use BackedEnum;
use Filament\Actions\Action;
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

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static ?string $navigationLabel = 'درخواست‌های تماس';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'درخواست تماس';

    protected static ?string $pluralModelLabel = 'درخواست‌های تماس';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات درخواست')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('ایمیل')
                            ->disabled()
                            ->extraInputAttributes(['style' => 'text-align: left;', 'dir' => 'ltr']),
                        TextInput::make('subject')
                            ->label('موضوع')
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->label('متن پیام')
                            ->disabled()
                            ->rows(8)
                            ->columnSpanFull(),
                        TextInput::make('ip_address')
                            ->label('IP')
                            ->disabled(),
                        TextInput::make('created_at')
                            ->label('زمان ثبت')
                            ->disabled(),
                        Toggle::make('is_read')
                            ->label('خوانده شده')
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('موضوع')
                    ->searchable()
                    ->limit(36),
                IconColumn::make('is_read')
                    ->label('خوانده شده')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('زمان ثبت')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label('علامت‌گذاری به‌عنوان خوانده‌شده')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (ContactRequest $record): bool => ! $record->is_read)
                    ->action(fn (ContactRequest $record) => $record->update(['is_read' => true])),
                EditAction::make()
                    ->label('مشاهده/وضعیت'),
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
            'index' => ManageContactRequests::route('/'),
        ];
    }
}
