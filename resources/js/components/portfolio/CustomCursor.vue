<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const isEnabled = ref(false);
const isVisible = ref(false);
const isPointerDown = ref(false);
const variant = ref('default');
const selectionActive = ref(false);

const targetX = ref(0);
const targetY = ref(0);
const currentX = ref(0);
const currentY = ref(0);

let rafId = 0;

const style = computed(() => ({
    transform: `translate3d(${currentX.value}px, ${currentY.value}px, 0)`,
}));

const hasFinePointer = () => {
    if (typeof window === 'undefined') return false;

    return window.matchMedia('(pointer: fine)').matches && window.matchMedia('(hover: hover)').matches;
};

const animate = () => {
    const lerp = 0.22;
    currentX.value += (targetX.value - currentX.value) * lerp;
    currentY.value += (targetY.value - currentY.value) * lerp;
    rafId = window.requestAnimationFrame(animate);
};

const isInteractiveElement = (el) => {
    return !!el.closest(
        'a, button, [role="button"], .resume-download-btn, .portfolio-more-btn, .portfolio-single-back, .portfolio-single-visit-btn, .portfolio-gallery-open, .submit, .tabs a, .tabs-mobile-links a, [data-cursor-hover]'
    );
};

const isMediaElement = (el) => {
    return !!el.closest(
        '.portfolio-zoom-trigger, .blog-zoom-trigger, .blog-detail-image, .portfolio-single-image img, .portfolio-thumb img, [data-cursor-media]'
    );
};

const isTextElement = (el) => {
    return !!el.closest(
        'input:not([type="button"]):not([type="submit"]), textarea, [contenteditable="true"], p, h1, h2, h3, h4, h5, h6, small, li, blockquote, code, pre, .text-block, .service-desc, .timeline-item p, .portfolio-card p, .blog-card p'
    );
};

const resolveVariant = (target) => {
    if (!(target instanceof Element)) {
        return selectionActive.value ? 'selection' : 'default';
    }

    if (target.closest('[data-cursor-hidden]')) {
        return 'hidden';
    }

    if (selectionActive.value && isTextElement(target)) {
        return 'selection';
    }

    if (isMediaElement(target)) {
        return 'media';
    }

    if (isInteractiveElement(target)) {
        return 'interactive';
    }

    if (isTextElement(target)) {
        return 'text';
    }

    return 'default';
};

const onPointerMove = (event) => {
    targetX.value = event.clientX;
    targetY.value = event.clientY;
    isVisible.value = true;
    variant.value = resolveVariant(event.target);
};

const onPointerDown = () => {
    isPointerDown.value = true;
};

const onPointerUp = () => {
    isPointerDown.value = false;
};

const onPointerLeaveWindow = () => {
    isVisible.value = false;
};

const onWindowBlur = () => {
    isVisible.value = false;
    isPointerDown.value = false;
};

const onVisibilityChange = () => {
    if (document.visibilityState !== 'visible') {
        isVisible.value = false;
        isPointerDown.value = false;
    }
};

const onWindowMouseOut = (event) => {
    const related = event.relatedTarget || event.toElement;

    if (!related) {
        isVisible.value = false;
        isPointerDown.value = false;
    }
};

const onSelectionChange = () => {
    const selection = window.getSelection();
    selectionActive.value = !!selection && String(selection.toString()).trim() !== '';
};

onMounted(() => {
    if (!hasFinePointer()) return;

    isEnabled.value = true;
    document.body.setAttribute('data-portfolio-cursor', 'on');

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('pointerdown', onPointerDown, { passive: true });
    window.addEventListener('pointerup', onPointerUp, { passive: true });
    window.addEventListener('pointerleave', onPointerLeaveWindow, { passive: true });
    window.addEventListener('blur', onWindowBlur);
    window.addEventListener('mouseout', onWindowMouseOut);
    document.addEventListener('selectionchange', onSelectionChange);
    document.addEventListener('visibilitychange', onVisibilityChange);

    currentX.value = window.innerWidth / 2;
    currentY.value = window.innerHeight / 2;
    targetX.value = currentX.value;
    targetY.value = currentY.value;

    rafId = window.requestAnimationFrame(animate);
});

onBeforeUnmount(() => {
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerdown', onPointerDown);
    window.removeEventListener('pointerup', onPointerUp);
    window.removeEventListener('pointerleave', onPointerLeaveWindow);
    window.removeEventListener('blur', onWindowBlur);
    window.removeEventListener('mouseout', onWindowMouseOut);
    document.removeEventListener('selectionchange', onSelectionChange);
    document.removeEventListener('visibilitychange', onVisibilityChange);
    if (rafId) {
        window.cancelAnimationFrame(rafId);
    }
    document.body.removeAttribute('data-portfolio-cursor');
});
</script>

<template>
    <div
        v-if="isEnabled"
        class="custom-cursor"
        :class="[
            `is-${variant}`,
            { 'is-visible': isVisible, 'is-down': isPointerDown },
        ]"
        :style="style"
        aria-hidden="true">
        <div class="custom-cursor-core">
            <svg class="custom-cursor-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32">
                <path d="M26.44,21.1c-1.51-1.73-3.13-3.44-4.84-5.09,1.15-.71,2.35-1.53,3.56-2.61.53-.48.78-1.18.67-1.87-.11-.66-.54-1.22-1.16-1.49h0c-6.25-2.73-11.96-4.39-17.44-5.06-.63-.08-1.24.14-1.68.6-.44.46-.63,1.1-.52,1.71.94,5.44,2.56,11.07,4.97,17.2.25.63.76,1.07,1.42,1.2.7.14,1.42-.09,1.94-.61,1.11-1.13,1.84-2.36,2.53-3.55,1.68,1.74,3.42,3.39,5.19,4.94.42.37.97.56,1.52.56.38,0,.76-.09,1.11-.28,1.31-.7,2.34-1.72,3.04-3.04.45-.85.34-1.91-.28-2.62Z" />
            </svg>
        </div>
    </div>
</template>
