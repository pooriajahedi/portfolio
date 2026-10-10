<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'تنظیمات سایت';

    protected static ?int $navigationSort = 12;

    protected static ?string $title = 'تنظیمات سایت';

    protected string $view = 'filament.pages.site-settings';

    protected Width | string | null $maxContentWidth = Width::Full;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::getMany(SiteSetting::defaults());

        $this->form->fill([
            ...$settings,
            SiteSetting::KEY_STARFIELD_ENABLED => filter_var($settings[SiteSetting::KEY_STARFIELD_ENABLED] ?? '1', FILTER_VALIDATE_BOOL),
            SiteSetting::KEY_STARFIELD_OPACITY => (int) ($settings[SiteSetting::KEY_STARFIELD_OPACITY] ?? 80),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Tabs::make('site-settings-tabs')
                            ->persistTabInQueryString('settings-tab')
                            ->tabs([
                                Tab::make('تنظیمات ظاهری')
                                    ->schema([
                                        Toggle::make(SiteSetting::KEY_STARFIELD_ENABLED)
                                            ->label('پس‌زمینه آسمان پرستاره فعال باشد')
                                            ->helperText('در صورت غیرفعال کردن، فقط پس‌زمینه تیره ساده نمایش داده می‌شود.')
                                            ->default(true)
                                            ->live()
                                            ->inline(false)
                                            ->columnSpanFull(),
                                        Slider::make(SiteSetting::KEY_STARFIELD_OPACITY)
                                            ->label('پررنگی ستاره‌ها')
                                            ->minValue(5)
                                            ->maxValue(100)
                                            ->step(1)
                                            ->decimalPlaces(0)
                                            ->tooltips(true)
                                            ->required(false)
                                            ->helperText('عدد بالاتر یعنی ستاره‌ها پررنگ‌تر دیده می‌شوند.')
                                            ->visible(fn (Get $get): bool => (bool) $get(SiteSetting::KEY_STARFIELD_ENABLED))
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('پیش‌نمایش شبکه‌های اجتماعی')
                                    ->schema([
                                        FileUpload::make(SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE)
                                            ->label('تصویر پیش‌نمایش (OG Image)')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site/social-preview')
                                            ->visibility('public')
                                            ->helperText('پیشنهاد: 1200x630 پیکسل برای نمایش بهتر در تلگرام، واتساپ، لینکدین و ...')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->footerActions([
                        Action::make('save')
                            ->label('ذخیره تنظیمات')
                            ->icon('heroicon-o-check-circle')
                            ->submit('save'),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        /** @var array<string, mixed> $state */
        $state = $this->form->getState();

        $starfieldOpacity = max(0, min(100, (int) ($state[SiteSetting::KEY_STARFIELD_OPACITY] ?? 80)));

        SiteSetting::setMany([
            SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE => (string) ($state[SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE] ?? ''),
            SiteSetting::KEY_STARFIELD_ENABLED => ((bool) ($state[SiteSetting::KEY_STARFIELD_ENABLED] ?? true)) ? '1' : '0',
            SiteSetting::KEY_STARFIELD_OPACITY => (string) $starfieldOpacity,
        ]);

        Notification::make()
            ->title('تنظیمات با موفقیت ذخیره شد.')
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
