<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
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

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }
}
