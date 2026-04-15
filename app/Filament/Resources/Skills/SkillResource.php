<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Models\Skill;
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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'مهارت ها';
    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'مهارت';

    protected static ?string $pluralModelLabel = 'مهارت ها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('آیتم مهارت')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان مهارت')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->label('دسته بندی')
                            ->options(Skill::categoryOptions())
                            ->default(Skill::CATEGORY_FRONTEND)
                            ->required()
                            ->native(false),
                        Select::make('icon')
                            ->label('آیکن')
                            ->options(Skill::iconOptions())
                            ->searchable()
                            ->placeholder('انتخاب آیکن')
                            ->native(false),
                        Hidden::make('sort_order')
                            ->default(fn () => ((int) Skill::query()->max('sort_order')) + 1),
                        Textarea::make('description')
                            ->label('توضیح')
                            ->rows(4)
                            ->required()
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
                TextColumn::make('category')
                    ->label('دسته')
                    ->formatStateUsing(fn (string $state): string => Skill::categoryOptions()[$state] ?? $state)
                    ->badge(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('icon')
                    ->label('آیکن')
                    ->placeholder('-')
                    ->toggleable(),
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
            'index' => ManageSkills::route('/'),
        ];
    }
}
