<x-filament-panels::page>
    <div class="resume-manager-page" x-data="{ activeResumeTab: 'files' }">
        <div class="resume-tabs">
            <button type="button" class="resume-tab-btn" :class="{ 'is-active': activeResumeTab === 'files' }" x-on:click="activeResumeTab = 'files'">
                فهرست فایل‌ها
            </button>
            <button type="button" class="resume-tab-btn" :class="{ 'is-active': activeResumeTab === 'template' }" x-on:click="activeResumeTab = 'template'">
                تنظیمات قالب
            </button>
            <button type="button" class="resume-tab-btn" :class="{ 'is-active': activeResumeTab === 'upload' }" x-on:click="activeResumeTab = 'upload'">
                بارگذاری دستی
            </button>
        </div>

        <div x-show="activeResumeTab === 'files'" x-cloak>
            <div class="resume-manager-toolbar">
                <div class="resume-manager-toolbar-actions">
                    <x-filament::button
                        type="button"
                        color="primary"
                        icon="heroicon-o-document-arrow-down"
                        wire:click="generateCv"
                        wire:loading.attr="disabled"
                        wire:target="generateCv"
                    >
                        ساخت فایل رزومه PDF
                    </x-filament::button>

                    <x-filament::button
                        tag="a"
                        color="info"
                        icon="heroicon-o-eye"
                        :href="route('admin.cv-preview')"
                        target="_blank"
                    >
                        پیش‌نمایش رزومه
                    </x-filament::button>
                </div>
            </div>

            <div class="resume-manager-files-card">
                <div class="resume-manager-files-header">
                    <h3>فهرست فایل‌های رزومه</h3>
                </div>

                <div class="resume-bulk-actions">
                    <x-filament::button
                        type="button"
                        color="danger"
                        size="sm"
                        icon="heroicon-o-trash"
                        wire:click="bulkDeleteResumeFiles"
                        x-on:click="if (!confirm('فایل‌های انتخاب‌شده حذف شوند؟')) { $event.stopImmediatePropagation() }"
                    >
                        حذف گروهی
                    </x-filament::button>

                    <span class="resume-bulk-count">تعداد انتخاب‌شده: {{ count($selectedForBulkDelete) }}</span>

                    @if($this->isActiveSelectedForBulkDelete)
                        <div class="resume-bulk-replacement">
                            <label>جایگزین فایل فعال:</label>
                            <select wire:model.live="bulkReplacementPath" class="resume-replacement-select">
                                <option value="">انتخاب فایل جایگزین</option>
                                @foreach($this->bulkDeleteReplacementOptions as $path => $name)
                                    <option value="{{ $path }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="resume-manager-table-wrap">
                    <table class="resume-manager-table">
                        <thead>
                        <tr>
                            <th>چک‌لیست</th>
                            <th>نام فایل</th>
                            <th>تاریخ ساخت</th>
                            <th>حجم</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($this->resumeFiles as $file)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $file['path'] }}"
                                        wire:model.live="selectedForBulkDelete"
                                        class="resume-row-checkbox"
                                    >
                                </td>
                                <td>
                                    <div class="resume-file-name">{{ $file['name'] }}</div>
                                    <div class="resume-file-path">{{ $file['path'] }}</div>
                                </td>
                                <td>{{ $file['createdAt'] }}</td>
                                <td>{{ $file['size'] }}</td>
                                <td>
                                    @if($file['isActive'])
                                        <span class="resume-status resume-status-active">فعال</span>
                                    @else
                                        <span class="resume-status resume-status-inactive">غیرفعال</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="resume-row-actions">
                                        <x-filament::button
                                            size="sm"
                                            color="info"
                                            x-on:click="$wire.previewResumeFile('{{ $file['path'] }}'); $nextTick(() => $dispatch('open-modal', { id: 'resume-preview-modal' }))"
                                        >
                                            پیش‌نمایش
                                        </x-filament::button>

                                        @if(! $file['isActive'])
                                            <x-filament::button
                                                size="sm"
                                                color="gray"
                                                wire:click="setActiveResume('{{ $file['path'] }}')"
                                            >
                                                فعال‌سازی
                                            </x-filament::button>
                                        @endif

                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="deleteResumeFile('{{ $file['path'] }}')"
                                            x-on:click="if (!confirm('از حذف این فایل مطمئن هستید؟')) { $event.stopImmediatePropagation() }"
                                        >
                                            حذف
                                        </x-filament::button>
                                    </div>

                                    @if($file['isActive'])
                                        @php
                                            $selectionKey = $this->selectionKey($file['path']);
                                            $replacementOptions = collect($this->resumeFiles)
                                                ->where('path', '!=', $file['path'])
                                                ->pluck('name', 'path')
                                                ->all();
                                        @endphp

                                        @if(count($replacementOptions) > 0)
                                            <div class="resume-replacement-box">
                                                <label>جایگزین برای حذف فایل فعال</label>
                                                <select
                                                    class="resume-replacement-select"
                                                    wire:model.live="replacementSelections.{{ $selectionKey }}"
                                                >
                                                    <option value="">انتخاب فایل جایگزین</option>
                                                    @foreach($replacementOptions as $path => $name)
                                                        <option value="{{ $path }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <p>برای حذف فایل فعال، انتخاب جایگزین الزامی است.</p>
                                            </div>
                                        @else
                                            <p class="resume-delete-warning">
                                                این فایل فعال است و تا زمانی که فایل دیگری تولید یا بارگذاری نشود قابل حذف نیست.
                                            </p>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="resume-empty">
                                    هنوز فایلی در پوشه رزومه وجود ندارد.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="activeResumeTab === 'template'" x-cloak>
            <form wire:submit="saveTemplateSettings" class="resume-manager-form">
                {{ $this->templateForm }}

                <div class="resume-manager-toolbar">
                    <div class="resume-manager-toolbar-actions">
                        <x-filament::button type="submit" icon="heroicon-o-check-circle">
                            ذخیره تنظیمات قالب
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </div>

        <div x-show="activeResumeTab === 'upload'" x-cloak>
            <form wire:submit="uploadAndSetActiveResume" class="resume-manager-form">
                {{ $this->uploadForm }}

                <div class="resume-manager-toolbar">
                    <div class="resume-manager-toolbar-actions">
                        <x-filament::button type="submit" color="warning" icon="heroicon-o-document-plus">
                            بارگذاری و فعال‌سازی فایل
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-filament::modal id="resume-preview-modal" width="screen">
        <x-slot name="heading">پیش‌نمایش فایل رزومه</x-slot>

        @if($this->selectedPreviewUrl)
            <div class="resume-preview-frame-wrap">
                <iframe
                    class="resume-preview-frame"
                    src="{{ $this->selectedPreviewUrl }}#zoom=100"
                    title="PDF Resume Preview"
                ></iframe>
            </div>
        @else
            <p class="resume-empty">برای نمایش در پنل، یکی از فایل‌های رزومه را انتخاب کنید.</p>
        @endif
    </x-filament::modal>

    <x-filament-actions::modals />
</x-filament-panels::page>
