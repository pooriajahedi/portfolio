<?php

namespace App\Filament\Pages;

use App\Models\HeroSection;
use App\Models\SiteSetting;
use App\Services\Cv\CvPdfGenerator;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ResumeFileManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'مدیریت رزومه';

    protected static ?int $navigationSort = 13;

    protected static ?string $title = 'مدیریت فایل‌های رزومه';

    protected string $view = 'filament.pages.resume-file-manager';

    protected Width | string | null $maxContentWidth = Width::Full;

    public ?array $templateData = [];
    public ?array $uploadData = [];

    /**
     * @var array<string, string>
     */
    public array $replacementSelections = [];
    /** @var array<int, string> */
    public array $selectedForBulkDelete = [];
    public ?string $bulkReplacementPath = null;
    public ?string $selectedPreviewPath = null;

    public function mount(): void
    {
        $settings = SiteSetting::getMany(SiteSetting::defaults());

        $this->templateForm->fill([
            SiteSetting::KEY_CV_SIDE_IMAGE => $settings[SiteSetting::KEY_CV_SIDE_IMAGE] ?? '',
            SiteSetting::KEY_CV_SIDE_IMAGE_POSITION => $settings[SiteSetting::KEY_CV_SIDE_IMAGE_POSITION] ?? 'left',
        ]);

        $this->uploadForm->fill([
            'manual_resume_upload' => null,
        ]);

        $activePath = $this->getActiveResumePath();
        $this->selectedPreviewPath = $this->resumeFileExists($activePath) ? $activePath : null;
    }

    protected function getForms(): array
    {
        return [
            'templateForm',
            'uploadForm',
        ];
    }

    public function templateForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تنظیمات قالب رزومه')
                    ->schema([
                        FileUpload::make(SiteSetting::KEY_CV_SIDE_IMAGE)
                            ->label('تصویر نوار کناری رزومه')
                            ->image()
                            ->disk('public')
                            ->directory('site/cv')
                            ->visibility('public')
                            ->helperText('این تصویر در پیش‌نمایش و PDF استفاده می‌شود.')
                            ->columnSpanFull(),
                        Select::make(SiteSetting::KEY_CV_SIDE_IMAGE_POSITION)
                            ->label('موقعیت تصویر در نوار کناری')
                            ->options([
                                'left' => 'چپ',
                                'center' => 'وسط',
                                'right' => 'راست',
                            ])
                            ->default('left')
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->statePath('templateData');
    }

    public function uploadForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بارگذاری دستی رزومه')
                    ->schema([
                        FileUpload::make('manual_resume_upload')
                            ->label('فایل رزومه (PDF)')
                            ->disk('public')
                            ->directory('resume-files')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'application/pdf',
                            ])
                            ->maxSize(20480)
                            ->downloadable()
                            ->openable()
                            ->helperText('حداکثر حجم: 20 مگابایت')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('uploadData');
    }

    public function saveTemplateSettings(): void
    {
        /** @var array<string, mixed> $state */
        $state = $this->templateForm->getState();

        SiteSetting::setMany([
            SiteSetting::KEY_CV_SIDE_IMAGE => (string) ($state[SiteSetting::KEY_CV_SIDE_IMAGE] ?? ''),
            SiteSetting::KEY_CV_SIDE_IMAGE_POSITION => in_array(($state[SiteSetting::KEY_CV_SIDE_IMAGE_POSITION] ?? 'left'), ['left', 'center', 'right'], true)
                ? $state[SiteSetting::KEY_CV_SIDE_IMAGE_POSITION]
                : 'left',
        ]);

        Notification::make()
            ->title('تنظیمات قالب رزومه ذخیره شد.')
            ->success()
            ->send();
    }

    public function uploadAndSetActiveResume(): void
    {
        /** @var array<string, mixed> $state */
        $state = $this->uploadForm->getState();
        $path = (string) ($state['manual_resume_upload'] ?? '');

        if ($path === '') {
            Notification::make()
                ->title('ابتدا فایل رزومه را بارگذاری کنید.')
                ->warning()
                ->send();

            return;
        }

        $this->setActiveResume($path, sendNotification: false);
        $this->uploadData['manual_resume_upload'] = null;

        Notification::make()
            ->title('فایل رزومه بارگذاری و به‌عنوان فایل فعال ثبت شد.')
            ->success()
            ->send();
    }

    public function generateCv(CvPdfGenerator $generator): void
    {
        try {
            $generatedPath = $generator->generate();
            $this->setActiveResume($generatedPath, sendNotification: false);

            Notification::make()
                ->title('نسخه جدید رزومه ساخته شد و به‌عنوان فایل فعال قرار گرفت.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('ساخت رزومه ناموفق بود. لطفاً لاگ برنامه را بررسی کنید.')
                ->danger()
                ->send();
        }
    }

    public function setActiveResume(string $path, bool $sendNotification = true): void
    {
        $normalizedPath = $this->normalizeResumePath($path);

        if (! $this->resumeFileExists($normalizedPath)) {
            Notification::make()
                ->title('فایل رزومه پیدا نشد.')
                ->danger()
                ->send();

            return;
        }

        $hero = $this->getOrCreateHero();
        $hero->update([
            'resume_file' => $normalizedPath,
        ]);
        $this->selectedPreviewPath = $normalizedPath;

        if ($sendNotification) {
            Notification::make()
                ->title('فایل فعال رزومه با موفقیت تغییر کرد.')
                ->success()
                ->send();
        }
    }

    public function deleteResumeFile(string $path): void
    {
        $normalizedPath = $this->normalizeResumePath($path);

        if (! $this->resumeFileExists($normalizedPath)) {
            Notification::make()
                ->title('فایل موردنظر یافت نشد.')
                ->danger()
                ->send();

            return;
        }

        $activePath = $this->getActiveResumePath();

        if ($activePath === $normalizedPath) {
            $replacementPath = $this->normalizeResumePath((string) ($this->replacementSelections[$this->selectionKey($normalizedPath)] ?? ''));

            if ($replacementPath === '') {
                Notification::make()
                    ->title('برای حذف فایل فعال، انتخاب فایل جایگزین الزامی است.')
                    ->warning()
                    ->send();

                return;
            }

            if ($replacementPath === $normalizedPath) {
                Notification::make()
                    ->title('فایل جایگزین باید با فایل فعال فعلی متفاوت باشد.')
                    ->warning()
                    ->send();

                return;
            }

            if (! $this->resumeFileExists($replacementPath)) {
                Notification::make()
                    ->title('فایل جایگزین انتخاب‌شده وجود ندارد.')
                    ->danger()
                    ->send();

                return;
            }

            $this->setActiveResume($replacementPath, sendNotification: false);
        }

        Storage::disk('public')->delete($normalizedPath);
        unset($this->replacementSelections[$this->selectionKey($normalizedPath)]);
        $this->selectedForBulkDelete = array_values(array_filter(
            $this->selectedForBulkDelete,
            fn (string $selected): bool => $this->normalizeResumePath($selected) !== $normalizedPath
        ));

        if ($this->selectedPreviewPath === $normalizedPath) {
            $this->selectedPreviewPath = $this->resumeFileExists($this->getActiveResumePath())
                ? $this->getActiveResumePath()
                : null;
        }

        Notification::make()
            ->title('فایل رزومه حذف شد.')
            ->success()
            ->send();
    }

    /**
     * @return array<int, array{path: string, name: string, size: string, sizeBytes: int, createdAt: string, modifiedAt: int, isActive: bool}>
     */
    public function getResumeFilesProperty(): array
    {
        $activePath = $this->getActiveResumePath();

        $files = collect(Storage::disk('public')->files('resume-files'))
            ->filter(fn (string $file): bool => str_ends_with(strtolower($file), '.pdf'))
            ->map(function (string $file) use ($activePath): array {
                $sizeBytes = (int) Storage::disk('public')->size($file);
                $modifiedAt = (int) Storage::disk('public')->lastModified($file);

                return [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => $this->formatBytes($sizeBytes),
                    'sizeBytes' => $sizeBytes,
                    'createdAt' => date('Y/m/d H:i', $modifiedAt),
                    'modifiedAt' => $modifiedAt,
                    'isActive' => $file === $activePath,
                ];
            })
            ->sortByDesc('modifiedAt')
            ->values()
            ->all();

        return $files;
    }

    public function bulkDeleteResumeFiles(): void
    {
        $selectedPaths = collect($this->selectedForBulkDelete)
            ->map(fn (string $path): string => $this->normalizeResumePath($path))
            ->filter(fn (string $path): bool => $this->resumeFileExists($path))
            ->unique()
            ->values();

        if ($selectedPaths->isEmpty()) {
            Notification::make()
                ->title('ابتدا فایل‌های موردنظر را از چک‌لیست انتخاب کنید.')
                ->warning()
                ->send();

            return;
        }

        $activePath = $this->getActiveResumePath();

        if ($activePath !== '' && $selectedPaths->contains($activePath)) {
            $replacementPath = $this->normalizeResumePath((string) $this->bulkReplacementPath);

            if ($replacementPath === '') {
                Notification::make()
                    ->title('برای حذف گروهی فایل فعال، انتخاب فایل جایگزین الزامی است.')
                    ->warning()
                    ->send();

                return;
            }

            if ($replacementPath === $activePath) {
                Notification::make()
                    ->title('فایل جایگزین نمی‌تواند همان فایل فعال فعلی باشد.')
                    ->warning()
                    ->send();

                return;
            }

            if ($selectedPaths->contains($replacementPath)) {
                Notification::make()
                    ->title('فایل جایگزین نباید داخل لیست حذف گروهی باشد.')
                    ->danger()
                    ->send();

                return;
            }

            if (! $this->resumeFileExists($replacementPath)) {
                Notification::make()
                    ->title('فایل جایگزین انتخاب‌شده معتبر نیست.')
                    ->danger()
                    ->send();

                return;
            }

            $this->setActiveResume($replacementPath, sendNotification: false);
        }

        foreach ($selectedPaths as $path) {
            Storage::disk('public')->delete($path);
            unset($this->replacementSelections[$this->selectionKey($path)]);
        }

        $this->selectedForBulkDelete = [];
        $this->bulkReplacementPath = null;

        if ($this->selectedPreviewPath && ! $this->resumeFileExists($this->selectedPreviewPath)) {
            $this->selectedPreviewPath = $this->resumeFileExists($this->getActiveResumePath())
                ? $this->getActiveResumePath()
                : null;
        }

        Notification::make()
            ->title('حذف گروهی با موفقیت انجام شد.')
            ->success()
            ->send();
    }

    public function previewResumeFile(string $path): void
    {
        $normalizedPath = $this->normalizeResumePath($path);

        if (! $this->resumeFileExists($normalizedPath)) {
            Notification::make()
                ->title('فایل موردنظر برای پیش‌نمایش یافت نشد.')
                ->danger()
                ->send();

            return;
        }

        $this->selectedPreviewPath = $normalizedPath;
    }

    public function getSelectedPreviewUrlProperty(): ?string
    {
        if (! $this->selectedPreviewPath || ! $this->resumeFileExists($this->selectedPreviewPath)) {
            return null;
        }

        return Storage::disk('public')->url($this->selectedPreviewPath);
    }

    public function getIsActiveSelectedForBulkDeleteProperty(): bool
    {
        $activePath = $this->getActiveResumePath();

        if ($activePath === '') {
            return false;
        }

        return collect($this->selectedForBulkDelete)
            ->map(fn (string $path): string => $this->normalizeResumePath($path))
            ->contains($activePath);
    }

    /**
     * @return array<string, string>
     */
    public function getBulkDeleteReplacementOptionsProperty(): array
    {
        $activePath = $this->getActiveResumePath();

        if ($activePath === '') {
            return [];
        }

        $selected = collect($this->selectedForBulkDelete)
            ->map(fn (string $path): string => $this->normalizeResumePath($path))
            ->all();

        return collect($this->resumeFiles)
            ->filter(function (array $file) use ($activePath, $selected): bool {
                if (($file['path'] ?? '') === $activePath) {
                    return false;
                }

                return ! in_array((string) ($file['path'] ?? ''), $selected, true);
            })
            ->pluck('name', 'path')
            ->all();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    private function getOrCreateHero(): HeroSection
    {
        return HeroSection::query()->latest('id')->first()
            ?? HeroSection::query()->create([
                'name' => 'پوریا جاهدی',
                'role' => 'Senior Backend & Full-Stack Developer',
                'current_status' => HeroSection::STATUS_LOOKING_FOR_JOB,
            ]);
    }

    private function getActiveResumePath(): string
    {
        return (string) (HeroSection::query()->latest('id')->value('resume_file') ?? '');
    }

    private function normalizeResumePath(string $path): string
    {
        $normalized = ltrim(trim($path), '/');

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, 'storage/')) {
            return substr($normalized, strlen('storage/'));
        }

        return $normalized;
    }

    private function resumeFileExists(string $path): bool
    {
        return $path !== '' && str_starts_with($path, 'resume-files/') && Storage::disk('public')->exists($path);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }

    public function selectionKey(string $path): string
    {
        return 'resume_' . md5($path);
    }
}
