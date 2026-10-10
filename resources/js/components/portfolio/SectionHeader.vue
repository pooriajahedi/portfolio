<script setup>
defineProps({
    index: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    // Used on the accent contact block, where the neutral tokens would disappear.
    inverted: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="flex items-center gap-4">
        <span
            class="rounded-lg px-2.5 py-1 font-mono text-[0.8125rem] font-semibold leading-snug"
            :class="inverted ? 'bg-primary-foreground/15' : 'bg-primary-soft text-primary'"
            dir="ltr"
        >
            {{ index }}
        </span>
        <span class="h-px w-8 sm:w-14" :class="inverted ? 'bg-primary-foreground/40' : 'bg-border-strong'"></span>
        <span class="label-mono" :style="inverted ? { color: 'inherit', opacity: 0.85 } : undefined">{{ label }}</span>
    </div>
</template>
