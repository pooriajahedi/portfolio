<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    images: {
        type: Array,
        default: () => [],
    },
    alt: {
        type: String,
        default: '',
    },
    startIndex: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['close']);

const { t } = useLang();

const activeIndex = ref(0);

const galleryImages = computed(() =>
    (Array.isArray(props.images) ? props.images : []).filter((item) => String(item ?? '').trim() !== ''),
);

const hasMultiple = computed(() => galleryImages.value.length > 1);
const activeImageSrc = computed(() => galleryImages.value[activeIndex.value] ?? '');

const step = (delta) => {
    if (!hasMultiple.value) return;
    const total = galleryImages.value.length;
    activeIndex.value = (activeIndex.value + delta + total) % total;
};

const onKeydown = (event) => {
    if (!props.open) return;

    if (event.key === 'Escape') {
        emit('close');
        return;
    }

    if (event.key === 'ArrowRight') step(1);
    if (event.key === 'ArrowLeft') step(-1);
};

watch(
    () => [props.open, props.startIndex],
    () => {
        if (!props.open) return;
        const total = galleryImages.value.length;
        activeIndex.value = total === 0 ? 0 : Math.min(Math.max(Number(props.startIndex ?? 0), 0), total - 1);
    },
    { immediate: true },
);

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        :aria-label="t({ fa: 'نمایش تصویر', en: 'Image viewer' })"
    >
        <div class="absolute inset-0 bg-background/90 backdrop-blur-sm" @click="emit('close')"></div>

        <button
            type="button"
            class="absolute end-4 top-4 z-10 rounded-md border border-border-strong bg-background/70 p-2.5 text-foreground transition-colors hover:border-primary hover:text-primary"
            :aria-label="t({ fa: 'بستن', en: 'Close' })"
            @click="emit('close')"
        >
            <UiIcon name="close" :size="20" />
        </button>

        <div class="relative z-10 flex max-h-full w-full max-w-4xl flex-col items-center gap-4">
            <img :src="activeImageSrc" :alt="alt" class="max-h-[75vh] w-auto rounded-lg border border-border object-contain" />

            <div v-if="hasMultiple" class="flex items-center gap-4">
                <button
                    type="button"
                    class="rounded-md border border-border-strong p-2.5 text-muted-foreground transition-colors hover:border-primary hover:text-primary"
                    :aria-label="t({ fa: 'تصویر قبلی', en: 'Previous image' })"
                    @click="step(-1)"
                >
                    <UiIcon name="arrowLeft" :size="18" />
                </button>

                <span class="font-mono text-sm text-muted-foreground">
                    {{ activeIndex + 1 }} / {{ galleryImages.length }}
                </span>

                <button
                    type="button"
                    class="rounded-md border border-border-strong p-2.5 text-muted-foreground transition-colors hover:border-primary hover:text-primary"
                    :aria-label="t({ fa: 'تصویر بعدی', en: 'Next image' })"
                    @click="step(1)"
                >
                    <UiIcon name="arrowRight" :size="18" />
                </button>
            </div>
        </div>
    </div>
</template>
