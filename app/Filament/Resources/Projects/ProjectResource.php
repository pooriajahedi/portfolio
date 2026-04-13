<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

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
                            ->label('نام پروژه')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('project_url')
                            ->label('لینک پروژه')
                            ->url()
                            ->maxLength(255),
                        Hidden::make('sort_order')
                            ->default(fn () => ((int) Project::query()->max('sort_order')) + 1),
                        TagsInput::make('tags')
                            ->label('تگ ها'),
                        Textarea::make('description')
                            ->label('توضیح پروژه')
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
                TextColumn::make('title')
                    ->label('نام پروژه')
                    ->searchable(),
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
