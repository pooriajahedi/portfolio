<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'تنظیمات سایت';

    protected static ?int $navigationSort = 12;

    protected static ?string $title = 'تنظیمات ظاهری سایت';

    protected string $view = 'filament.pages.site-settings';

    protected Width | string | null $maxContentWidth = Width::Full;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            SiteSetting::getMany(SiteSetting::defaults())
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make(SiteSetting::KEY_BACKGROUND_MODE)
                                    ->label('نوع پس‌زمینه')
                                    ->options([
                                        'gradient' => 'گرادیانت رنگی',
                                        'image' => 'تصویر',
                                    ])
                                    ->default('gradient')
                                    ->native(false)
                                    ->live(),
                                Slider::make(SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY)
                                    ->label('شفافیت تصویر پس‌زمینه (%)')
                                    ->range(0, 100)
                                    ->step(1)
                                    ->default(55)
                                    ->visible(fn (Get $get): bool => $get(SiteSetting::KEY_BACKGROUND_MODE) === 'image'),
                                ColorPicker::make(SiteSetting::KEY_PRIMARY_COLOR)
                                    ->label('رنگ پرایمری سایت')
                                    ->default('#F4C64F')
                                    ->required(),
                            ]),
                        FileUpload::make(SiteSetting::KEY_BACKGROUND_IMAGE)
                            ->label('تصویر پس‌زمینه')
                            ->image()
                            ->disk('public')
                            ->directory('site/backgrounds')
                            ->visibility('public')
                            ->imagePreviewHeight('220')
                            ->panelLayout('compact')
                            ->downloadable()
                            ->openable()
                            ->visible(fn (Get $get): bool => $get(SiteSetting::KEY_BACKGROUND_MODE) === 'image')
                            ->helperText('تصویر پیشنهاد شده: عرض حداقل 1920 پیکسل.'),
                        Grid::make(2)
                            ->schema([
                                ColorPicker::make(SiteSetting::KEY_BACKGROUND_COLOR_FROM)
                                    ->label('رنگ اول گرادیانت')
                                    ->default('#101829')
                                    ->required(),
                                ColorPicker::make(SiteSetting::KEY_BACKGROUND_COLOR_TO)
                                    ->label('رنگ دوم گرادیانت')
                                    ->default('#0B0F19')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        /** @var array<string, mixed> $state */
        $state = $this->form->getState();

        SiteSetting::setMany([
            SiteSetting::KEY_BACKGROUND_MODE => in_array(($state[SiteSetting::KEY_BACKGROUND_MODE] ?? 'gradient'), ['gradient', 'image'], true)
                ? $state[SiteSetting::KEY_BACKGROUND_MODE]
                : 'gradient',
            SiteSetting::KEY_BACKGROUND_IMAGE => $state[SiteSetting::KEY_BACKGROUND_IMAGE] ?? null,
            SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY => max(0, min(100, (int) ($state[SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY] ?? 55))),
            SiteSetting::KEY_BACKGROUND_COLOR_FROM => $state[SiteSetting::KEY_BACKGROUND_COLOR_FROM] ?? '#101829',
            SiteSetting::KEY_BACKGROUND_COLOR_TO => $state[SiteSetting::KEY_BACKGROUND_COLOR_TO] ?? '#0B0F19',
            SiteSetting::KEY_PRIMARY_COLOR => $state[SiteSetting::KEY_PRIMARY_COLOR] ?? '#F4C64F',
        ]);

        Notification::make()
            ->title('تنظیمات با موفقیت ذخیره شد.')
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        $defaults = SiteSetting::defaults();

        SiteSetting::setMany($defaults);
        $this->form->fill($defaults);

        Notification::make()
            ->title('تنظیمات به حالت پیش‌فرض بازگردانی شد.')
            ->success()
            ->send();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }
}
