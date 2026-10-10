<script setup>
import { computed } from 'vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    // How the screenshot is framed: a browser window, phones, or both.
    type: {
        type: String,
        default: 'web',
        validator: (value) => ['web', 'app', 'both'].includes(value),
    },
    // The project's own brand colour; the cover is tinted with it.
    color: {
        type: String,
        default: null,
    },
    image: {
        type: String,
        default: null,
    },
    gallery: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: '',
    },
});

const { t } = useLang();

const KIND_LABELS = {
    web: { fa: 'وب‌سایت', en: 'Website' },
    app: { fa: 'اپلیکیشن', en: 'App' },
    both: { fa: 'وب + اپلیکیشن', en: 'Web + App' },
};

const kindLabel = computed(() => t(KIND_LABELS[props.type] ?? KIND_LABELS.web));
const tint = computed(() => ({ '--pc': props.color || 'var(--accent)' }));
const initial = computed(() => Array.from(props.title.trim())[0] ?? '');

const showBrowser = computed(() => props.type === 'web' || props.type === 'both');
const showSinglePhone = computed(() => props.type === 'both');
const showPhonePair = computed(() => props.type === 'app');

// Phones prefer a second screenshot from the gallery, falling back to the cover image.
const secondShot = computed(() => props.gallery[0] ?? props.image);
</script>

<template>
    <div class="cover" :style="tint">
        <span class="cover__tag"><i></i>{{ kindLabel }}</span>

        <template v-if="image">
            <div v-if="showBrowser" class="browser" :class="{ 'browser--paired': showSinglePhone }">
                <div class="browser__bar"><i></i><i></i><i></i><span></span></div>
                <img :src="image" :alt="title" loading="lazy" class="browser__shot" />
            </div>

            <div v-if="showSinglePhone" class="phone phone--end">
                <span class="phone__notch"></span>
                <img :src="secondShot" alt="" loading="lazy" class="phone__shot" />
            </div>

            <template v-if="showPhonePair">
                <div class="phone phone--a">
                    <span class="phone__notch"></span>
                    <img :src="image" :alt="title" loading="lazy" class="phone__shot" />
                </div>
                <div class="phone phone--b">
                    <span class="phone__notch"></span>
                    <img :src="secondShot" alt="" loading="lazy" class="phone__shot" />
                </div>
            </template>
        </template>

        <div v-else class="cover__fallback" aria-hidden="true">
            <span dir="auto">{{ initial }}</span>
        </div>
    </div>
</template>

<style scoped>
.cover {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: color-mix(in srgb, var(--pc) 15%, var(--surface-2));
    background-image: radial-gradient(color-mix(in srgb, var(--pc) 30%, transparent) 1px, transparent 1.5px);
    background-size: 22px 22px;
}

.cover__tag {
    position: absolute;
    inset-block-start: 1rem;
    inset-inline-start: 1rem;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 1.875rem;
    padding-inline: 0.75rem;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1;
}

.cover__tag i {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: var(--pc);
}

.cover__fallback {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
}

.cover__fallback span {
    display: grid;
    place-items: center;
    width: 5.25rem;
    height: 5.25rem;
    border-radius: 1.625rem;
    background: var(--pc);
    color: #fff;
    font-size: 2.75rem;
    font-weight: 800;
    line-height: 1;
}

/* Devices bleed off the bottom edge so the cover reads as a crop of a larger screen. */
.browser {
    position: absolute;
    inset-inline: 12%;
    top: 24%;
    bottom: -12%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid var(--line-strong);
    border-bottom: 0;
    border-radius: 0.875rem 0.875rem 0 0;
    background: var(--surface);
    box-shadow: var(--shadow);
}

.browser--paired {
    inset-inline-start: 7%;
    inset-inline-end: 27%;
}

.browser__bar {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5625rem 0.75rem;
    border-bottom: 1px solid var(--line);
    background: var(--surface-2);
}

.browser__bar i {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: var(--line-strong);
}

.browser__bar span {
    flex: 1;
    max-width: 60%;
    height: 0.875rem;
    margin-inline-start: 0.625rem;
    border-radius: 0.4375rem;
    background: var(--bg);
}

.browser__shot {
    flex: 1;
    width: 100%;
    min-height: 0;
    object-fit: cover;
    object-position: top;
}

.phone {
    position: absolute;
    top: 30%;
    bottom: -16%;
    width: 25%;
    min-width: 5.5rem;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 5px solid #0b0c10;
    border-bottom: 0;
    border-radius: 1.625rem 1.625rem 0 0;
    outline: 1px solid var(--line-strong);
    background: var(--surface);
    box-shadow: var(--shadow);
}

.phone--end {
    inset-inline-end: 7%;
}

.phone--a {
    inset-inline-start: 19%;
}

.phone--b {
    inset-inline-end: 19%;
    top: 20%;
}

.phone__notch {
    position: absolute;
    inset-block-start: 0.4rem;
    inset-inline: 33%;
    z-index: 1;
    height: 0.375rem;
    border-radius: 0.1875rem;
    background: #0b0c10;
}

.phone__shot {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top;
}
</style>
