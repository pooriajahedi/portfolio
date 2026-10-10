<script setup>
import IconGlyph from '../IconGlyph.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
});

const { t } = useLang();
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <div v-for="group in groups" :key="group.key" class="panel p-6">
            <h3 class="mono-label">{{ t(group.title) }}</h3>

            <ul class="mt-5 space-y-3">
                <li
                    v-for="skill in group.items"
                    :key="skill.title.fa + skill.icon"
                    class="flex items-center gap-4 rounded-lg border border-border bg-secondary/60 px-4 py-3 transition-colors hover:border-primary/40"
                >
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-foreground/5">
                        <IconGlyph :icon="skill.icon" :size="22" />
                    </span>
                    <span dir="auto" class="text-base font-medium text-secondary-foreground">{{ t(skill.title) }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
