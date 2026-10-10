<script setup>
defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div
        v-if="items.length"
        aria-hidden="true"
        class="group overflow-hidden whitespace-nowrap border-y border-border bg-card"
    >
        <div dir="ltr" class="marquee-track flex w-max items-center gap-9 py-4 ps-9 font-mono text-[0.9375rem] font-medium text-muted-foreground">
            <template v-for="copy in 2" :key="copy">
                <span v-for="item in items" :key="`${copy}-${item}`" class="flex items-center gap-9">
                    {{ item }}
                    <span class="size-2 rotate-45 bg-primary"></span>
                </span>
            </template>
        </div>
    </div>
</template>

<style scoped>
.marquee-track {
    animation: marquee 45s linear infinite;
}

.group:hover .marquee-track {
    animation-play-state: paused;
}

/* Two identical copies sit side by side, so sliding by half a track loops seamlessly. */
@keyframes marquee {
    to {
        transform: translateX(-50%);
    }
}
</style>
