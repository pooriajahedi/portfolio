<script setup>
import { computed } from 'vue';

// Lucide-style stroke icons, inlined so the page ships no icon dependency.
const PATHS = {
    download: ['M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4', 'm7 10 5 5 5-5', 'M12 15V3'],
    arrowUpRight: ['M7 7h10v10', 'M7 17 17 7'],
    arrowRight: ['M5 12h14', 'm12 5 7 7-7 7'],
    arrowLeft: ['M19 12H5', 'm12 19-7-7 7-7'],
    arrowUp: ['m5 12 7-7 7 7', 'M12 19V5'],
    externalLink: ['M15 3h6v6', 'M10 14 21 3', 'M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'],
    close: ['M18 6 6 18', 'm6 6 12 12'],
    menu: ['M4 7h16', 'M4 12h16', 'M4 17h16'],
    check: ['M20 6 9 17l-5-5'],
    zoom: ['M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16Z', 'm21 21-4.3-4.3', 'M11 8v6', 'M8 11h6'],
    sun: [
        'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z',
        'M12 2v2',
        'M12 20v2',
        'm4.93 4.93 1.41 1.41',
        'm17.66 17.66 1.41 1.41',
        'M2 12h2',
        'M20 12h2',
        'm6.34 17.66-1.41 1.41',
        'm19.07 4.93-1.41 1.41',
    ],
    moon: ['M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z'],
    mail: ['M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z', 'm22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7'],
    github: [
        'M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4',
        'M9 18c-4.51 2-5-2-7-2',
    ],
    linkedin: [
        'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z',
        'M2 9h4v12H2z',
        'M4 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4z',
    ],
    send: ['m22 2-7 20-4-9-9-4Z', 'M22 2 11 13'],
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
    strokeWidth: {
        type: [Number, String],
        default: 1.75,
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
        :stroke-width="strokeWidth"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        focusable="false"
        class="shrink-0"
    >
        <path v-for="d in paths" :key="d" :d="d" />
    </svg>
</template>
