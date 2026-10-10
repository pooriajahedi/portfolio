<script setup>
defineProps({
    id: {
        type: String,
        required: true,
    },
    index: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <section :id="id" class="scroll-mt-24 border-t border-border py-16 sm:py-24">
        <div class="mb-9 flex items-baseline gap-3">
            <span class="font-mono text-sm text-primary">{{ index }}</span>
            <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">{{ title }}</h2>
        </div>
        <slot />
    </section>
</template>
