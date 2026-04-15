<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="mt-8 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="flex flex-wrap items-center justify-start [column-gap:12px] [row-gap:12px]" style="margin-top: 15px;">
                <x-filament::button
                    type="button"
                    color="gray"
                    class="ms-0"
                    icon="heroicon-o-arrow-path"
                    x-on:click="if (confirm('تنظیمات به حالت پیش‌فرض بازگردانی شود؟')) $wire.resetToDefaults()"
                >
                    بازگردانی به پیش‌فرض
                </x-filament::button>

                <x-filament::button type="submit" class="ms-0" icon="heroicon-o-check-circle">
                    ذخیره تنظیمات
                </x-filament::button>
            </div>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
