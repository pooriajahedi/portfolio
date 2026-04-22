<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Select;
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
            SiteSetting::KEY_MATRIX_ENABLED => filter_var($settings[SiteSetting::KEY_MATRIX_ENABLED] ?? '1', FILTER_VALIDATE_BOOL),
            SiteSetting::KEY_MATRIX_OPACITY => (int) ($settings[SiteSetting::KEY_MATRIX_OPACITY] ?? 56),
            SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY => (int) ($settings[SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY] ?? 100),
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
                                        Toggle::make(SiteSetting::KEY_MATRIX_ENABLED)
                                            ->label('نمایه ماتریکس فعال باشد')
                                            ->helperText('در صورت غیرفعال کردن، می‌توانید بک‌گراند جایگزین (عکس/رنگ/گرادیانت) انتخاب کنید.')
                                            ->default(true)
                                            ->live()
                                            ->inline(false)
                                            ->columnSpanFull(),
                                        Slider::make(SiteSetting::KEY_MATRIX_OPACITY)
                                            ->label('پررنگی نوشته‌های ماتریکس')
                                            ->minValue(5)
                                            ->maxValue(100)
                                            ->step(1)
                                            ->decimalPlaces(0)
                                            ->tooltips(true)
                                            ->required(false)
                                            ->helperText('عدد بالاتر یعنی نوشته‌های ماتریکس پررنگ‌تر دیده می‌شوند.')
                                            ->visible(fn (Get $get): bool => (bool) $get(SiteSetting::KEY_MATRIX_ENABLED))
                                            ->columnSpanFull(),
                                        Select::make(SiteSetting::KEY_BACKGROUND_MODE)
                                            ->label('بک‌گراند جایگزین (وقتی ماتریکس خاموش است)')
                                            ->options([
                                                'solid' => 'رنگ یک‌دست',
                                                'gradient' => 'گرادیانت',
                                                'image' => 'عکس',
                                            ])
                                            ->default('gradient')
                                            ->live()
                                            ->native(false)
                                            ->required(false)
                                            ->visible(fn (Get $get): bool => ! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED))
                                            ->columnSpanFull(),
                                        FileUpload::make(SiteSetting::KEY_BACKGROUND_IMAGE)
                                            ->label('عکس بک‌گراند')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site/background')
                                            ->visibility('public')
                                            ->helperText('برای بهترین نتیجه، تصویر با عرض حداقل 1920 پیشنهاد می‌شود.')
                                            ->visible(fn (Get $get): bool => (! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED)) && ((string) $get(SiteSetting::KEY_BACKGROUND_MODE) === 'image'))
                                            ->columnSpanFull(),
                                        Slider::make(SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY)
                                            ->label('شفافیت عکس بک‌گراند')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(1)
                                            ->decimalPlaces(0)
                                            ->tooltips(true)
                                            ->live()
                                            ->required(false)
                                            ->helperText('۰ یعنی کاملاً شفاف، ۱۰۰ یعنی کاملاً واضح.')
                                            ->visible(fn (Get $get): bool => (! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED)) && ((string) $get(SiteSetting::KEY_BACKGROUND_MODE) === 'image'))
                                            ->columnSpanFull(),
                                        ColorPicker::make(SiteSetting::KEY_BACKGROUND_SOLID_COLOR)
                                            ->label('رنگ بک‌گراند')
                                            ->hex()
                                            ->default('#0a0b0f')
                                            ->visible(fn (Get $get): bool => (! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED)) && ((string) $get(SiteSetting::KEY_BACKGROUND_MODE) === 'solid'))
                                            ->columnSpanFull(),
                                        ColorPicker::make(SiteSetting::KEY_BACKGROUND_GRADIENT_FROM)
                                            ->label('رنگ اول گرادیانت')
                                            ->hex()
                                            ->default('#0a0b0f')
                                            ->visible(fn (Get $get): bool => (! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED)) && ((string) $get(SiteSetting::KEY_BACKGROUND_MODE) === 'gradient')),
                                        ColorPicker::make(SiteSetting::KEY_BACKGROUND_GRADIENT_TO)
                                            ->label('رنگ دوم گرادیانت')
                                            ->hex()
                                            ->default('#101827')
                                            ->visible(fn (Get $get): bool => (! (bool) $get(SiteSetting::KEY_MATRIX_ENABLED)) && ((string) $get(SiteSetting::KEY_BACKGROUND_MODE) === 'gradient')),
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

        $matrixEnabled = (bool) ($state[SiteSetting::KEY_MATRIX_ENABLED] ?? true);
        $matrixOpacity = (int) ($state[SiteSetting::KEY_MATRIX_OPACITY] ?? 56);
        $matrixOpacity = max(0, min(100, $matrixOpacity));

        $bgMode = (string) ($state[SiteSetting::KEY_BACKGROUND_MODE] ?? 'gradient');
        if (! in_array($bgMode, ['solid', 'gradient', 'image'], true)) {
            $bgMode = 'gradient';
        }
        $bgImageOpacity = (int) ($state[SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY] ?? 100);
        $bgImageOpacity = max(0, min(100, $bgImageOpacity));

        SiteSetting::setMany([
            SiteSetting::KEY_THEME_STYLE => in_array(($state[SiteSetting::KEY_THEME_STYLE] ?? 'gold'), ['gold', 'green'], true)
                ? $state[SiteSetting::KEY_THEME_STYLE]
                : 'gold',
            SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE => (string) ($state[SiteSetting::KEY_SOCIAL_PREVIEW_IMAGE] ?? ''),
            SiteSetting::KEY_MATRIX_ENABLED => $matrixEnabled ? '1' : '0',
            SiteSetting::KEY_MATRIX_OPACITY => (string) $matrixOpacity,
            SiteSetting::KEY_BACKGROUND_MODE => $bgMode,
            SiteSetting::KEY_BACKGROUND_IMAGE => (string) ($state[SiteSetting::KEY_BACKGROUND_IMAGE] ?? ''),
            SiteSetting::KEY_BACKGROUND_IMAGE_OPACITY => (string) $bgImageOpacity,
            SiteSetting::KEY_BACKGROUND_SOLID_COLOR => (string) ($state[SiteSetting::KEY_BACKGROUND_SOLID_COLOR] ?? '#0a0b0f'),
            SiteSetting::KEY_BACKGROUND_GRADIENT_FROM => (string) ($state[SiteSetting::KEY_BACKGROUND_GRADIENT_FROM] ?? '#0a0b0f'),
            SiteSetting::KEY_BACKGROUND_GRADIENT_TO => (string) ($state[SiteSetting::KEY_BACKGROUND_GRADIENT_TO] ?? '#101827'),
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
