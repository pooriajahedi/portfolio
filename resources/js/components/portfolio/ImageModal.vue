<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import IconGlyph from '../IconGlyph.vue';

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    src: {
        type: String,
        default: '',
    },
    alt: {
        type: String,
        default: '',
    },
    images: {
        type: Array,
        default: () => [],
    },
    startIndex: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['close']);

const activeIndex = ref(0);

const galleryImages = computed(() => {
    const images = Array.isArray(props.images) ? props.images.filter((item) => String(item ?? '').trim() !== '') : [];
    if (images.length > 0) return images;

    return props.src ? [props.src] : [];
});

const hasMultiple = computed(() => galleryImages.value.length > 1);
const activeImageSrc = computed(() => galleryImages.value[activeIndex.value] || '');

const clampIndex = (value) => {
    if (galleryImages.value.length === 0) return 0;
    return Math.min(Math.max(value, 0), galleryImages.value.length - 1);
};

const prev = () => {
    if (!hasMultiple.value) return;
    activeIndex.value = activeIndex.value === 0 ? galleryImages.value.length - 1 : activeIndex.value - 1;
};

const next = () => {
    if (!hasMultiple.value) return;
    activeIndex.value = activeIndex.value === galleryImages.value.length - 1 ? 0 : activeIndex.value + 1;
};

const onKeydown = (event) => {
    if (!props.open || !hasMultiple.value) return;

    if (event.key === 'ArrowRight') {
        prev();
    }

    if (event.key === 'ArrowLeft') {
        next();
    }
};

watch(
    () => [props.open, props.startIndex, galleryImages.value.length],
    () => {
        if (!props.open) return;
        activeIndex.value = clampIndex(Number(props.startIndex ?? 0));
    },
    { immediate: true }
);

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div class="image-modal" :class="{ 'is-open': open }" id="imageModal" :aria-hidden="(!open).toString()">
        <div class="image-modal-backdrop" @click="emit('close')"></div>
        <button class="image-modal-close" type="button" aria-label="بستن تصویر" @click="emit('close')">
            <IconGlyph icon="mdi:close" alt="بستن" :size="24" />
        </button>
        <div class="image-modal-dialog" role="dialog" aria-modal="true" aria-label="نمایش تصویر">
            <img id="imageModalImage" :src="activeImageSrc" :alt="alt">

            <template v-if="hasMultiple">
                <button type="button" class="image-modal-nav image-modal-prev" aria-label="تصویر قبلی" @click="prev">‹</button>
                <button type="button" class="image-modal-nav image-modal-next" aria-label="تصویر بعدی" @click="next">›</button>
                <small class="image-modal-counter">{{ activeIndex + 1 }} / {{ galleryImages.length }}</small>
            </template>
        </div>
    </div>
</template>
