<script setup>
import { computed } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: '',
    },
    size: {
        type: [Number, String],
        default: 20,
    },
    className: {
        type: String,
        default: '',
    },
});

const iconUrl = computed(() => {
    const normalized = String(props.icon ?? '').trim();
    if (normalized === '') return '';

    return `/icons/${normalized.replace(':', '--')}.svg`;
});

const isMaskIcon = computed(() => String(props.icon ?? '').startsWith('mdi:'));

const maskStyle = computed(() => {
    const size = typeof props.size === 'number' ? `${props.size}px` : String(props.size);

    return {
        '--icon-url': `url(${iconUrl.value})`,
        '--icon-size': size,
    };
});
</script>

<template>
    <span
        v-if="isMaskIcon"
        class="inline-icon icon-glyph icon-glyph-mask"
        :class="className"
        :style="maskStyle"
        :role="alt ? 'img' : undefined"
        :aria-label="alt || undefined"
        :aria-hidden="alt ? undefined : 'true'"
    ></span>

    <img
        v-else
        class="inline-icon"
        :class="className"
        :src="iconUrl"
        :alt="alt"
        :width="size"
        :height="size"
    >
</template>

<style scoped>
.icon-glyph {
    display: inline-block;
    width: var(--icon-size);
    height: var(--icon-size);
    flex-shrink: 0;
    vertical-align: middle;
}

.icon-glyph-mask {
    background-color: currentColor;
    -webkit-mask-image: var(--icon-url);
    mask-image: var(--icon-url);
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
    -webkit-mask-size: contain;
    mask-size: contain;
}
</style>
