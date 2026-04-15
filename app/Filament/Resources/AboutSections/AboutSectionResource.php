<?php

namespace App\Filament\Resources\AboutSections;

use App\Filament\Resources\AboutSections\Pages\CreateAboutSection;
use App\Filament\Resources\AboutSections\Pages\EditAboutSection;
use App\Filament\Resources\AboutSections\Pages\ListAboutSections;
use App\Models\AboutSection;
use App\Models\Skill;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AboutSectionResource extends Resource
{
    protected static ?string $model = AboutSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'درباره من';
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'درباره من';

    protected static ?string $pluralModelLabel = 'درباره من';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('about-editor-tabs')
                    ->persistTabInQueryString('about-tab')
                    ->tabs([
                        Tab::make('درباره من')
                            ->schema([
                                TextInput::make('title')
                                    ->label('عنوان درباره من')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('paragraph_one')
                                    ->label('متن درباره من')
                                    ->required()
                                    ->rows(8)
                                    ->columnSpanFull(),
                                Toggle::make('is_active')
                                    ->label('فعال')
                                    ->default(true),
                            ])
                            ->columns(2),
                        Tab::make('در حال انجام چه کارهایی هستم')
                            ->schema([
                                Repeater::make('serviceCards')
                                    ->label('کارت‌ها')
                                    ->relationship('serviceCards')
                                    ->orderColumn('sort_order')
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->itemNumbers()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('عنوان')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')
                                            ->label('توضیح')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),
                                        Toggle::make('is_active')
                                            ->label('فعال')
                                            ->default(true),
                                    ])
                                    ->collapsed()
                                    ->addActionLabel('افزودن کارت')
                                    ->reorderableWithButtons()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Tab::make('بخش مهارت‌ها')
                            ->schema([
                                Repeater::make('skill_categories')
                                    ->label('دسته‌بندی‌ها')
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->itemNumbers()
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('عنوان دسته')
                                            ->required()
                                            ->maxLength(120)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                                if (filled($get('key'))) {
                                                    return;
                                                }

                                                $set('key', Str::slug((string) $state));
                                            }),
                                        TextInput::make('key')
                                            ->label('کلید دسته')
                                            ->required()
                                            ->helperText('فقط برای اتصال مهارت‌ها استفاده می‌شود (مثل: frontend)')
                                            ->maxLength(120),
                                        Toggle::make('is_active')
                                            ->label('فعال')
                                            ->default(true),
                                    ])
                                    ->mutateDehydratedStateUsing(function (?array $state): array {
                                        return collect($state ?? [])
                                            ->map(function (array $item): array {
                                                return [
                                                    'key' => Str::slug((string) ($item['key'] ?? '')),
                                                    'label' => trim((string) ($item['label'] ?? '')),
                                                    'is_active' => (bool) ($item['is_active'] ?? true),
                                                ];
                                            })
                                            ->filter(fn (array $item): bool => $item['key'] !== '' && $item['label'] !== '')
                                            ->values()
                                            ->all();
                                    })
                                    ->default(AboutSection::DEFAULT_SKILL_CATEGORIES)
                                    ->addActionLabel('افزودن دسته جدید')
                                    ->reorderableWithButtons()
                                    ->collapsed()
                                    ->collapsible()
                                    ->cloneable()
                                    ->deleteAction(fn ($action) => $action->label('حذف دسته'))
                                    ->collapseAllAction(fn ($action) => $action->label('جمع‌کردن همه'))
                                    ->expandAllAction(fn ($action) => $action->label('بازکردن همه'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Tab::make('مدیریت مهارت‌ها')
                            ->schema([
                                Repeater::make('skills_manager')
                                    ->label('مهارت‌ها')
                                    ->itemLabel(function (array $state): ?HtmlString {
                                        $title = trim((string) ($state['title'] ?? ''));

                                        if ($title === '') {
                                            return null;
                                        }

                                        $icon = trim((string) ($state['icon'] ?? ''));
                                        $icon = $icon !== '' ? $icon : 'mdi:star-four-points-circle';
                                        $iconUrl = 'https://api.iconify.design/' . rawurlencode($icon) . '.svg?width=18&height=18';

                                        return new HtmlString(
                                            '<span style="display:inline-flex;align-items:center;gap:.4rem;">' .
                                            '<span>' . e($title) . '</span>' .
                                            '<img src="' . e($iconUrl) . '" alt="" style="width:18px;height:18px;object-fit:contain;" />' .
                                            '</span>'
                                        );
                                    })
                                    ->itemNumbers()
                                    ->table([
                                        TableColumn::make('عنوان')->markAsRequired(),
                                        TableColumn::make('دسته‌بندی')->markAsRequired(),
                                        TableColumn::make('آیکن'),
                                        TableColumn::make('وضعیت'),
                                    ])
                                    ->schema([
                                        TextInput::make('id')
                                            ->hidden(),
                                        Hidden::make('description')
                                            ->dehydrated(),
                                        TextInput::make('title')
                                            ->label('عنوان مهارت')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->maxLength(255),
                                        Select::make('category')
                                            ->label('دسته‌بندی')
                                            ->options(fn (): array => Skill::categoryOptions())
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->native(false),
                                        Select::make('icon')
                                            ->label('آیکن')
                                            ->options(Skill::iconPreviewOptions())
                                            ->searchable()
                                            ->disabled()
                                            ->dehydrated()
                                            ->allowHtml()
                                            ->native(false),
                                        Toggle::make('is_active')
                                            ->label('فعال')
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(true),
                                    ])
                                    ->addActionLabel('افزودن مهارت')
                                    ->addAction(function (Action $action): Action {
                                        return $action
                                            ->slideOver()
                                            ->modalHeading('ایجاد مهارت جدید')
                                            ->modalSubmitActionLabel('ثبت مهارت')
                                            ->form([
                                                TextInput::make('title')
                                                    ->label('عنوان مهارت')
                                                    ->required()
                                                    ->maxLength(255),
                                                Select::make('category')
                                                    ->label('دسته‌بندی')
                                                    ->options(fn (): array => Skill::categoryOptions())
                                                    ->required()
                                                    ->native(false),
                                                Select::make('icon')
                                                    ->label('آیکن')
                                                    ->options(Skill::iconPreviewOptions())
                                                    ->searchable()
                                                    ->allowHtml()
                                                    ->native(false),
                                                Textarea::make('description')
                                                    ->label('توضیح')
                                                    ->rows(3)
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Toggle::make('is_active')
                                                    ->label('فعال')
                                                    ->default(true),
                                            ])
                                            ->action(function (array $data, Repeater $component): void {
                                                $items = $component->getRawState() ?? [];
                                                $items[] = [
                                                    'id' => null,
                                                    'title' => trim((string) ($data['title'] ?? '')),
                                                    'description' => trim((string) ($data['description'] ?? '')),
                                                    'category' => trim((string) ($data['category'] ?? '')),
                                                    'icon' => $data['icon'] ?? null,
                                                    'is_active' => (bool) ($data['is_active'] ?? true),
                                                ];

                                                $component->rawState($items);
                                                $component->callAfterStateUpdated();
                                            });
                                    })
                                    ->reorderableWithButtons()
                                    ->cloneable()
                                    ->extraItemActions([
                                        Action::make('editSkill')
                                            ->label('ویرایش')
                                            ->icon('heroicon-o-pencil-square')
                                            ->slideOver()
                                            ->modalHeading('ویرایش مهارت')
                                            ->modalSubmitActionLabel('ذخیره تغییرات')
                                            ->fillForm(fn (array $arguments, Repeater $component): array => $component->getRawItemState($arguments['item']))
                                            ->form([
                                                TextInput::make('title')
                                                    ->label('عنوان مهارت')
                                                    ->required()
                                                    ->maxLength(255),
                                                Select::make('category')
                                                    ->label('دسته‌بندی')
                                                    ->options(fn (): array => Skill::categoryOptions())
                                                    ->required()
                                                    ->native(false),
                                                Select::make('icon')
                                                    ->label('آیکن')
                                                    ->options(Skill::iconPreviewOptions())
                                                    ->searchable()
                                                    ->allowHtml()
                                                    ->native(false),
                                                Textarea::make('description')
                                                    ->label('توضیح')
                                                    ->rows(3)
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Toggle::make('is_active')
                                                    ->label('فعال')
                                                    ->default(true),
                                            ])
                                            ->action(function (array $data, array $arguments, Repeater $component): void {
                                                $items = $component->getRawState() ?? [];
                                                $itemKey = $arguments['item'] ?? null;

                                                if ($itemKey === null || ! array_key_exists($itemKey, $items)) {
                                                    return;
                                                }

                                                $items[$itemKey] = [
                                                    ...$items[$itemKey],
                                                    'title' => trim((string) ($data['title'] ?? '')),
                                                    'description' => trim((string) ($data['description'] ?? '')),
                                                    'category' => trim((string) ($data['category'] ?? '')),
                                                    'icon' => $data['icon'] ?? null,
                                                    'is_active' => (bool) ($data['is_active'] ?? true),
                                                ];

                                                $component->rawState($items);
                                                $component->callAfterStateUpdated();
                                            }),
                                    ])
                                    ->deleteAction(fn ($action) => $action->label('حذف مهارت'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
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

    public static function getNavigationUrl(): string
    {
        $record = AboutSection::query()->latest('id')->first();

        if ($record) {
            return static::getUrl('edit', ['record' => $record]);
        }

        return static::getUrl('create');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutSections::route('/'),
            'create' => CreateAboutSection::route('/create'),
            'edit' => EditAboutSection::route('/{record}/edit'),
        ];
    }
}
