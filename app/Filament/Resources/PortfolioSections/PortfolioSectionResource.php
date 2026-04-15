<?php

namespace App\Filament\Resources\PortfolioSections;

use App\Filament\Resources\PortfolioSections\Pages\CreatePortfolioSection;
use App\Filament\Resources\PortfolioSections\Pages\EditPortfolioSection;
use App\Filament\Resources\PortfolioSections\Pages\ListPortfolioSections;
use App\Models\PortfolioSection;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioSectionResource extends Resource
{
    protected static ?string $model = PortfolioSection::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static ?string $navigationLabel = 'نمونه کارها';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'نمونه کارها';

    protected static ?string $pluralModelLabel = 'نمونه کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('portfolio-editor-tabs')
                    ->persistTabInQueryString('portfolio-tab')
                    ->tabs([
                        Tab::make('مدیریت دسته‌بندی‌ها')
                            ->schema([
                                Repeater::make('categories_manager')
                                    ->label('دسته‌بندی‌ها')
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->itemNumbers()
                                    ->reorderableWithButtons()
                                    ->collapsed()
                                    ->collapsible()
                                    ->cloneable()
                                    ->addActionLabel('افزودن دسته‌بندی')
                                    ->table([
                                        TableColumn::make('عنوان')->markAsRequired(),
                                        TableColumn::make('کلید'),
                                        TableColumn::make('وضعیت'),
                                    ])
                                    ->schema([
                                        Hidden::make('id'),
                                        Hidden::make('sort_order'),
                                        TextInput::make('title')
                                            ->label('عنوان دسته‌بندی')
                                            ->required()
                                            ->maxLength(120)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                                if (filled($get('slug'))) {
                                                    return;
                                                }

                                                $set('slug', Str::slug((string) $state));
                                            }),
                                        TextInput::make('slug')
                                            ->label('کلید دسته (لاتین)')
                                            ->required()
                                            ->maxLength(120)
                                            ->helperText('برای نمایش دسته در فرانت استفاده می‌شود.'),
                                        Toggle::make('is_active')
                                            ->label('فعال')
                                            ->default(true),
                                    ])
                                    ->mutateDehydratedStateUsing(function (?array $state): array {
                                        return collect($state ?? [])
                                            ->map(function (array $item): array {
                                                return [
                                                    'id' => $item['id'] ?? null,
                                                    'title' => trim((string) ($item['title'] ?? '')),
                                                    'slug' => Str::slug((string) ($item['slug'] ?? '')),
                                                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                                                    'is_active' => (bool) ($item['is_active'] ?? true),
                                                ];
                                            })
                                            ->filter(fn (array $item): bool => $item['title'] !== '' && $item['slug'] !== '')
                                            ->values()
                                            ->all();
                                    })
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('مدیریت نمونه کارها')
                            ->schema([
                                Repeater::make('projects_manager')
                                    ->label('نمونه‌کارها')
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->itemNumbers()
                                    ->reorderableWithButtons()
                                    ->collapsed()
                                    ->collapsible()
                                    ->cloneable()
                                    ->addActionLabel('افزودن نمونه کار')
                                    ->schema([
                                        Hidden::make('id'),
                                        Hidden::make('sort_order'),
                                        TextInput::make('title')
                                            ->label('عنوان پروژه')
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('project_category_id')
                                            ->label('دسته‌بندی')
                                            ->options(fn (): array => \App\Models\ProjectCategory::query()
                                                ->where('is_active', true)
                                                ->orderBy('sort_order')
                                                ->orderBy('id')
                                                ->pluck('title', 'id')
                                                ->all())
                                            ->placeholder('ابتدا یک دسته‌بندی ایجاد کنید')
                                            ->noOptionsMessage('هیچ دسته‌بندی فعالی وجود ندارد.')
                                            ->noSearchResultsMessage('دسته‌بندی مورد نظر پیدا نشد.')
                                            ->searchPrompt('برای جستجو تایپ کنید...')
                                            ->helperText('اگر لیست خالی است، ابتدا در تب مدیریت دسته‌بندی‌ها یک دسته فعال بسازید.')
                                            ->searchable()
                                            ->native(false),
                                        TextInput::make('project_url')
                                            ->label('لینک پروژه')
                                            ->url()
                                            ->maxLength(255),
                                        TagsInput::make('tags')
                                            ->label('تگ‌ها'),
                                        FileUpload::make('image_path')
                                            ->label('تصویر پروژه')
                                            ->image()
                                            ->disk('public')
                                            ->directory('projects')
                                            ->visibility('public')
                                            ->imagePreviewHeight('220')
                                            ->panelLayout('compact')
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('فرمت‌های مجاز: jpg, png, webp - حداکثر ۴ مگابایت')
                                            ->columnSpanFull(),
                                        Textarea::make('description')
                                            ->label('توضیح پروژه')
                                            ->rows(4)
                                            ->required()
                                            ->columnSpanFull(),
                                        Toggle::make('is_active')
                                            ->label('وضعیت نمایش')
                                            ->onIcon('heroicon-o-check')
                                            ->offIcon('heroicon-o-x-mark')
                                            ->default(true),
                                    ])
                                    ->mutateDehydratedStateUsing(function (?array $state): array {
                                        return collect($state ?? [])
                                            ->map(function (array $item): array {
                                                return [
                                                    'id' => $item['id'] ?? null,
                                                    'title' => trim((string) ($item['title'] ?? '')),
                                                    'description' => trim((string) ($item['description'] ?? '')),
                                                    'tags' => collect($item['tags'] ?? [])->filter()->values()->all(),
                                                    'project_url' => filled($item['project_url'] ?? null) ? trim((string) $item['project_url']) : null,
                                                    'image_path' => filled($item['image_path'] ?? null) ? (string) $item['image_path'] : null,
                                                    'project_category_id' => filled($item['project_category_id'] ?? null) ? (int) $item['project_category_id'] : null,
                                                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                                                    'is_active' => (bool) ($item['is_active'] ?? true),
                                                ];
                                            })
                                            ->filter(fn (array $item): bool => $item['title'] !== '' && $item['description'] !== '')
                                            ->values()
                                            ->all();
                                    })
                                    ->deleteAction(fn ($action) => $action->label('حذف نمونه کار'))
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان'),
            ]);
    }

    public static function getNavigationUrl(): string
    {
        $record = PortfolioSection::query()->firstOrCreate(
            ['id' => 1],
            ['title' => 'نمونه کارها'],
        );

        return static::getUrl('edit', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortfolioSections::route('/'),
            'create' => CreatePortfolioSection::route('/create'),
            'edit' => EditPortfolioSection::route('/{record}/edit'),
        ];
    }
}
