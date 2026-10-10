<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Forms\ProjectMediaTabs;
use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'پروژه ها';
    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'پروژه';

    protected static ?string $pluralModelLabel = 'پروژه ها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('آیتم پروژه')
                    ->schema([
                        TextInput::make('title')
                            ->label('نام پروژه (فارسی)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                if (filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug((string) $state));
                            })
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('title_en')
                            ->label('نام پروژه (انگلیسی)')
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('slug')
                            ->label('اسلاگ (اختیاری)')
                            ->maxLength(255)
                            ->helperText('اگر خالی باشد، از عنوان به‌صورت خودکار ساخته می‌شود.')
                            ->dehydrateStateUsing(fn ($state): ?string => filled($state) ? Str::slug((string) $state) : null)
                            ->columnSpan(1),
                        TextInput::make('project_url')
                            ->label('لینک پروژه')
                            ->url()
                            ->maxLength(255),
                        Hidden::make('sort_order')
                            ->default(fn () => ((int) Project::query()->max('sort_order')) + 1),
                        TagsInput::make('tags')
                            ->label('تگ ها'),
                        ColorPicker::make('brand_color')
                            ->label('رنگ برند پروژه')
                            ->regex('/^#[0-9a-fA-F]{6}$/')
                            ->helperText('پس‌زمینه‌ی کاور و رنگ‌های داخل قاب‌ها از این رنگ ساخته می‌شود. اگر خالی بماند، رنگ تاکیدی سایت استفاده می‌شود.'),
                        Select::make('cover_type')
                            ->label('نوع کاور')
                            ->options(Project::coverTypeOptions())
                            ->default(Project::COVER_WEB)
                            ->required()
                            ->native(false)
                            ->helperText('تصویر کاور داخل قاب مرورگر، موبایل یا هر دو نمایش داده می‌شود.'),
                        ProjectMediaTabs::make(),
                        Textarea::make('description')
                            ->label('توضیح پروژه (فارسی)')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description_en')
                            ->label('توضیح پروژه (انگلیسی)')
                            ->rows(4)
                            ->helperText('اگر خالی بماند، در حالت انگلیسی همان متن فارسی نمایش داده می‌شود.')
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
                    ->label('نام پروژه')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('اسلاگ')
                    ->limit(28),
                TextColumn::make('project_url')
                    ->label('لینک')
                    ->url(fn ($record) => $record->project_url, true)
                    ->openUrlInNewTab()
                    ->limit(30),
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
            'index' => ManageProjects::route('/'),
        ];
    }
}
