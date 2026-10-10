<script setup>
import { computed } from 'vue';

// Lucide-style stroke icons, inlined so the page ships no icon dependency.
const PATHS = {
    download: ['M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4', 'm7 10 5 5 5-5', 'M12 15V3'],
    languages: ['m5 8 6 6', 'm4 14 6-6 2-3', 'M2 5h12', 'M7 2h1', 'm22 22-5-10-5 10', 'M14 18h6'],
    arrowUpRight: ['M7 7h10v10', 'M7 17 17 7'],
    arrowRight: ['M5 12h14', 'm12 5 7 7-7 7'],
    arrowLeft: ['M19 12H5', 'm12 19-7-7 7-7'],
    externalLink: ['M15 3h6v6', 'M10 14 21 3', 'M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'],
    close: ['M18 6 6 18', 'm6 6 12 12'],
};

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    size: {
        type: [Number, String],
        default: 18,
    },
});

const paths = computed(() => PATHS[props.name] ?? []);
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        focusable="false"
        class="shrink-0"
    >
        <path v-for="d in paths" :key="d" :d="d" />
    </svg>
</template>
