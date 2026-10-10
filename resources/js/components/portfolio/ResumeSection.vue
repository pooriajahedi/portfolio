<script setup>
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    resumeUrl: {
        type: String,
        default: '',
    },
});

const { t } = useLang();
</script>

<template>
    <div class="panel flex flex-col items-start gap-5 p-7 sm:flex-row sm:items-center sm:justify-between">
        <p class="max-w-md text-base leading-relaxed text-muted-foreground">
            {{
                t({
                    fa: 'یک فایل PDF شامل سوابق کامل کاری، تکنولوژی‌ها و راه‌های ارتباطی.',
                    en: 'A PDF with my full work history, stack and contact details.',
                })
            }}
        </p>

        <a
            v-if="resumeUrl"
            :href="resumeUrl"
            download
            class="flex shrink-0 items-center gap-2 rounded-md bg-primary px-5 py-3 text-base font-medium text-primary-foreground transition-opacity hover:opacity-90"
        >
            <UiIcon name="download" :size="18" />
            {{ t({ fa: 'دانلود رزومه (PDF)', en: 'Download resume (PDF)' }) }}
        </a>
    </div>
</template>
