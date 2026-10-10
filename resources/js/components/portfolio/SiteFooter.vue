<script setup>
import { computed } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    handle: {
        type: String,
        default: 'pooria.jahedi',
    },
});

const { lang, t } = useLang();

// Persian visitors see the Jalali year (۱۴۰۵), English visitors the Gregorian one.
const year = computed(() =>
    lang.value === 'fa'
        ? new Intl.DateTimeFormat('fa-IR-u-ca-persian', { year: 'numeric' }).format(new Date())
        : String(new Date().getFullYear()),
);
const initials = computed(() =>
    props.handle
        .split(/[.\-_\s]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join(''),
);
</script>

<template>
    <footer class="border-t border-border">
        <div class="mx-auto flex w-full max-w-[1240px] flex-wrap items-center justify-between gap-4 px-5 py-7 sm:px-10">
            <div class="flex flex-wrap items-center gap-3">
                <span
                    class="grid size-8 place-items-center rounded-[0.625rem] bg-primary font-mono text-xs font-semibold text-primary-foreground"
                >
                    {{ initials }}
                </span>
                <p class="text-[0.9375rem] text-muted-foreground">
                    <span dir="ltr" class="font-mono">© {{ year }}</span>
                    {{ t({ fa: 'پوریا جاهدی. تمامی حقوق محفوظ است.', en: 'Pooria Jahedi. All rights reserved.' }) }}
                </p>
            </div>

            <a
                href="#top"
                class="inline-flex min-h-11 items-center gap-2.5 rounded-full border border-border-strong px-5 text-sm font-medium transition-colors hover:border-primary hover:text-primary"
            >
                {{ t({ fa: 'بازگشت به بالا', en: 'Back to top' }) }}
                <UiIcon name="arrowUp" :size="16" :stroke-width="2" />
            </a>
        </div>
    </footer>
</template>
