<script setup>
import { computed } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';
import { useTheme } from '../../composables/useTheme';

const { t } = useLang();
const { isDark, toggle } = useTheme();

const label = computed(() =>
    isDark.value
        ? t({ fa: 'تغییر به حالت روشن', en: 'Switch to light mode' })
        : t({ fa: 'تغییر به حالت تاریک', en: 'Switch to dark mode' }),
);
</script>

<template>
    <button
        type="button"
        class="inline-grid size-11 place-items-center rounded-full border border-border text-foreground transition-colors hover:border-primary hover:text-primary"
        :aria-label="label"
        :title="label"
        @click="toggle"
    >
        <UiIcon :name="isDark ? 'sun' : 'moon'" :size="20" />
    </button>
</template>
