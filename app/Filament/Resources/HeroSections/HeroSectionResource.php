<?php

namespace App\Filament\Resources\HeroSections;

use App\Filament\Resources\HeroSections\Pages\CreateHeroSection;
use App\Filament\Resources\HeroSections\Pages\EditHeroSection;
use App\Filament\Resources\HeroSections\Pages\ListHeroSections;
use App\Models\HeroSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSectionResource extends Resource
{
    protected static ?string $model = HeroSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'نمایه شخصی';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'نمایه شخصی';

    protected static ?string $pluralModelLabel = 'نمایه شخصی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('current_status')
                            ->label('وضعیت فعلی')
                            ->options(HeroSection::statusOptions())
                            ->default(HeroSection::STATUS_LOOKING_FOR_JOB)
                            ->required()
                            ->native(false),
                        TextInput::make('name')
                            ->label('نام')
                            ->maxLength(255),
                        TextInput::make('role')
                            ->label('عنوان شغلی')
                            ->maxLength(255),
                        FileUpload::make('avatar_image')
                            ->label('عکس پروفایل')
                            ->image()
                            ->disk('public')
                            ->directory('hero')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('تصویر مربعی (مثلا 512x512) بهترین نتیجه را می‌دهد.')
                            ->columnSpanFull(),
                        FileUpload::make('resume_file')
                            ->label('فایل رزومه (PDF / DOC / DOCX)')
                            ->disk('public')
                            ->directory('resume-files')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->helperText('حداکثر حجم: 10 مگابایت')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('role')
                    ->label('عنوان شغلی')
                    ->limit(50),
                TextColumn::make('current_status')
                    ->label('وضعیت فعلی')
                    ->formatStateUsing(fn (?string $state): string => HeroSection::statusLabel($state))
                    ->badge(),
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
        $record = HeroSection::query()->latest('id')->first();

        if ($record) {
            return static::getUrl('edit', ['record' => $record]);
        }

        return static::getUrl('create');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeroSections::route('/'),
            'create' => CreateHeroSection::route('/create'),
            'edit' => EditHeroSection::route('/{record}/edit'),
        ];
    }
}
