<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * The cover and gallery of a project, once per language.
 *
 * Shared by the standalone project form and the projects repeater so both write
 * the same four fields: image_path, gallery_paths, image_path_en, gallery_paths_en.
 */
final class ProjectMediaTabs
{
    public static function make(): Tabs
    {
        return Tabs::make()
            ->tabs([
                Tab::make('نسخه‌ی فارسی')->schema(self::fields('image_path', 'gallery_paths')),
                Tab::make('نسخه‌ی انگلیسی')->schema(self::fields('image_path_en', 'gallery_paths_en')),
            ])
            ->columnSpanFull();
    }

    /**
     * @return array<int, FileUpload>
     */
    private static function fields(string $imageField, string $galleryField): array
    {
        return [
            FileUpload::make($imageField)
                ->label('تصویر کاور')
                ->image()
                ->disk('public')
                ->directory('projects')
                ->visibility('public')
                ->imagePreviewHeight('220')
                ->panelLayout('compact')
                ->downloadable()
                ->openable()
                ->helperText('اگر تصویر یک زبان بارگذاری نشود، سایت از تصویر زبان دیگر استفاده می‌کند.')
                ->columnSpanFull(),
            FileUpload::make($galleryField)
                ->label('گالری تصاویر')
                ->image()
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->disk('public')
                ->directory('projects/gallery')
                ->visibility('public')
                ->imagePreviewHeight('140')
                ->panelLayout('grid')
                ->downloadable()
                ->openable()
                ->helperText('چند تصویر برای صفحه‌ی جزئیات پروژه؛ ترتیب را می‌توانید با کشیدن عوض کنید.')
                ->columnSpanFull(),
        ];
    }
}
