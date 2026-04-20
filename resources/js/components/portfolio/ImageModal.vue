<script setup>
import IconGlyph from '../IconGlyph.vue';

defineProps({
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
});

const emit = defineEmits(['close']);
</script>

<template>
    <div class="image-modal" :class="{ 'is-open': open }" id="imageModal" :aria-hidden="(!open).toString()">
        <div class="image-modal-backdrop" @click="emit('close')"></div>
        <button class="image-modal-close" type="button" aria-label="بستن تصویر" @click="emit('close')">
            <IconGlyph icon="mdi:close" alt="بستن" :size="24" />
        </button>
        <div class="image-modal-dialog" role="dialog" aria-modal="true" aria-label="نمایش تصویر">
            <img id="imageModalImage" :src="src" :alt="alt">
        </div>
    </div>
</template>
