<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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
                Section::make('تم سایت')
                    ->schema([
                        Select::make(SiteSetting::KEY_THEME_STYLE)
                            ->label('تم رنگی سایت')
                            ->options([
                                'gold' => 'طلایی',
                                'green' => 'سبز',
                            ])
                            ->default('gold')
                            ->native(false)
                            ->required()
                            ->helperText('تنها همین گزینه روی ظاهر سایت اعمال می‌شود.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('پیش‌نمایش لینک در شبکه‌های اجتماعی')
                    ->schema([
                        FileUpload::make(SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE)
                            ->label('تصویر پیش‌نمایش (OG Image)')
                            ->image()
                            ->disk('public')
                            ->directory('site/social-preview')
                            ->visibility('public')
                            ->helperText('پیشنهاد: 1200x630 پیکسل برای نمایش بهتر در تلگرام، واتساپ، لینکدین و ...')
                            ->columnSpanFull(),
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
            SiteSetting::KEY_THEME_STYLE => in_array(($state[SiteSetting::KEY_THEME_STYLE] ?? 'gold'), ['gold', 'green'], true)
                ? $state[SiteSetting::KEY_THEME_STYLE]
                : 'gold',
            SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE => (string) ($state[SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE] ?? ''),
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
