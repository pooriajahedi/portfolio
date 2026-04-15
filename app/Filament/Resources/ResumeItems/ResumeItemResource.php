<?php

namespace App\Filament\Resources\ResumeItems;

use App\Filament\Resources\ResumeItems\Pages\ManageResumeItems;
use App\Models\ResumeItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResumeItemResource extends Resource
{
    protected static ?string $model = ResumeItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'رزومه';
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'آیتم رزومه';

    protected static ?string $pluralModelLabel = 'رزومه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('آیتم رزومه')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),
                        Hidden::make('sort_order')
                            ->default(fn () => ((int) ResumeItem::query()->max('sort_order')) + 1),
                        Textarea::make('description')
                            ->label('توضیح')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                        Section::make('بازه زمانی رزومه (شمسی)')
                            ->schema([
                                Select::make('start_year')
                                    ->label('سال شروع')
                                    ->options(fn (): array => self::jalaliYearOptions())
                                    ->required()
                                    ->native(false),
                                Select::make('start_month')
                                    ->label('ماه شروع')
                                    ->options(fn (): array => self::jalaliMonthOptions())
                                    ->required()
                                    ->native(false),
                                Select::make('start_day')
                                    ->label('روز شروع')
                                    ->options(fn (): array => self::jalaliDayOptions())
                                    ->required()
                                    ->native(false),
                                Toggle::make('is_current')
                                    ->label('در حال حاضر مشغول هستم')
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, bool $state): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $set('end_year', null);
                                        $set('end_month', null);
                                        $set('end_day', null);
                                    })
                                    ->default(false),
                                Select::make('end_year')
                                    ->label('سال پایان')
                                    ->options(fn (): array => self::jalaliYearOptions())
                                    ->required(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->hidden(fn (Get $get): bool => (bool) $get('is_current'))
                                    ->dehydrated(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->native(false),
                                Select::make('end_month')
                                    ->label('ماه پایان')
                                    ->options(fn (): array => self::jalaliMonthOptions())
                                    ->required(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->hidden(fn (Get $get): bool => (bool) $get('is_current'))
                                    ->dehydrated(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->native(false),
                                Select::make('end_day')
                                    ->label('روز پایان')
                                    ->options(fn (): array => self::jalaliDayOptions())
                                    ->required(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->hidden(fn (Get $get): bool => (bool) $get('is_current'))
                                    ->dehydrated(fn (Get $get): bool => ! (bool) $get('is_current'))
                                    ->native(false),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('period')
                    ->label('بازه زمانی')
                    ->state(fn (ResumeItem $record): string => self::formatPeriod($record))
                    ->wrap(),
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
            'index' => ManageResumeItems::route('/'),
        ];
    }

    private static function jalaliYearOptions(): array
    {
        $years = range(1380, 1455);

        return collect($years)
            ->mapWithKeys(fn (int $year): array => [$year => self::toPersianDigits((string) $year)])
            ->all();
    }

    private static function jalaliMonthOptions(): array
    {
        return [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
    }

    private static function jalaliDayOptions(): array
    {
        $days = range(1, 31);

        return collect($days)
            ->mapWithKeys(fn (int $day): array => [$day => self::toPersianDigits((string) $day)])
            ->all();
    }

    private static function formatPeriod(ResumeItem $record): string
    {
        $start = self::formatJalaliDate($record->start_year, $record->start_month, $record->start_day);

        if (! $record->is_current) {
            $end = self::formatJalaliDate($record->end_year, $record->end_month, $record->end_day);

            return collect([$start, $end])->filter()->implode(' تا ');
        }

        return collect([$start, 'تا اکنون'])->filter()->implode(' تا ');
    }

    private static function formatJalaliDate(?int $year, ?int $month, ?int $day): ?string
    {
        if (! $year || ! $month || ! $day) {
            return null;
        }

        $monthLabel = self::jalaliMonthOptions()[$month] ?? null;

        if (! $monthLabel) {
            return null;
        }

        return self::toPersianDigits((string) $day) . ' ' . $monthLabel . ' ' . self::toPersianDigits((string) $year);
    }

    private static function toPersianDigits(string $value): string
    {
        return strtr($value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ]);
    }
}
