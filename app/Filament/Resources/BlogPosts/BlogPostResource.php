<?php

namespace App\Filament\Resources\BlogPosts;

use App\Filament\Resources\BlogPosts\Pages\ManageBlogPosts;
use App\Models\BlogPost;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
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
use Illuminate\Support\Carbon;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'وبلاگ';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'مقاله';

    protected static ?string $pluralModelLabel = 'وبلاگ';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوای مقاله')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان مقاله')
                            ->required()
                            ->maxLength(255),
                        Hidden::make('sort_order')
                            ->default(fn (): int => ((int) BlogPost::query()->max('sort_order')) + 1),
                        Textarea::make('excerpt')
                            ->label('خلاصه کوتاه')
                            ->rows(3)
                            ->helperText('اگر خالی بماند، خلاصه از متن مقاله ساخته می‌شود.')
                            ->columnSpanFull(),
                        FileUpload::make('image_path')
                            ->label('تصویر مقاله')
                            ->image()
                            ->disk('public')
                            ->directory('blog')
                            ->visibility('public')
                            ->imagePreviewHeight('220')
                            ->panelLayout('compact')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('محتوای مقاله')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog/content')
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
                TextColumn::make('published_at')
                    ->label('تاریخ انتشار')
                    ->state(fn (BlogPost $record): string => self::formatCreatedAtJalali($record->created_at)),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
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
            'index' => ManageBlogPosts::route('/'),
        ];
    }

    private static function formatCreatedAtJalali(?Carbon $createdAt): string
    {
        if (! $createdAt) {
            return '-';
        }

        [$jy, $jm, $jd] = self::gregorianToJalali(
            (int) $createdAt->format('Y'),
            (int) $createdAt->format('m'),
            (int) $createdAt->format('d'),
        );

        $monthLabel = self::jalaliMonthOptions()[$jm] ?? '';

        return self::toPersianDigits((string) $jd) . ' ' . $monthLabel . ' ' . self::toPersianDigits((string) $jy);
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

    private static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy2 = $gy - 1600;
        $gm2 = $gm - 1;
        $gd2 = $gd - 1;

        $gDayNo = 365 * $gy2 + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400);

        for ($i = 0; $i < $gm2; $i++) {
            $gDayNo += $gDaysInMonth[$i];
        }

        if ($gm2 > 1 && (($gy % 4 === 0 && $gy % 100 !== 0) || $gy % 400 === 0)) {
            $gDayNo++;
        }

        $gDayNo += $gd2;
        $jDayNo = $gDayNo - 79;
        $jNp = intdiv($jDayNo, 12053);
        $jDayNo %= 12053;
        $jy = 979 + 33 * $jNp + 4 * intdiv($jDayNo, 1461);
        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += intdiv($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        for ($i = 0; $i < 11 && $jDayNo >= $jDaysInMonth[$i]; $i++) {
            $jDayNo -= $jDaysInMonth[$i];
        }

        $jm = $i + 1;
        $jd = $jDayNo + 1;

        return [$jy, $jm, $jd];
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
