<script setup>
import { useLang } from '../../composables/useLang';

const LANGUAGES = [
    { code: 'fa', label: 'FA', name: 'فارسی' },
    { code: 'en', label: 'EN', name: 'English' },
];

const { lang, setLang, t } = useLang();
</script>

<template>
    <div
        dir="ltr"
        role="group"
        :aria-label="t({ fa: 'زبان', en: 'Language' })"
        class="inline-flex rounded-full border border-border bg-background-alt p-[3px]"
    >
        <button
            v-for="item in LANGUAGES"
            :key="item.code"
            type="button"
            class="min-h-10 min-w-11 rounded-full px-3 font-mono text-[0.8125rem] font-semibold leading-none tracking-wide transition-colors"
            :class="lang === item.code ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
            :aria-pressed="lang === item.code"
            :aria-label="item.name"
            :lang="item.code"
            @click="setLang(item.code)"
        >
            {{ item.label }}
        </button>
    </div>
</template>
