<script setup>
import { computed } from 'vue';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadError: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['back', 'open-image', 'open-project-url']);

const galleryImages = computed(() => {
    const items = Array.isArray(props.project?.galleryImages) ? props.project.galleryImages : [];
    const cover = String(props.project?.imageUrl ?? '').trim();

    if (!cover) return items;
    if (items.includes(cover)) return items;

    return [cover, ...items];
});

const activeImage = computed(() => galleryImages.value[0] || '');

const formatPersianDate = (value) => {
    const raw = String(value ?? '').trim();
    if (!raw) return '';

    const date = new Date(raw);
    if (Number.isNaN(date.getTime())) return raw;

    return new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).format(date);
};
</script>

<template>
    <section class="section portfolio-single" id="portfolio-single" tabindex="-1">
        <div class="portfolio-single-head">
            <button type="button" class="portfolio-single-back" @click="emit('back')">
                <span class="portfolio-single-back-icon" aria-hidden="true">→</span>
                بازگشت به نمونه کارها
            </button>
        </div>

        <div v-if="loading" class="portfolio-single-skeleton">
            <div class="portfolio-single-top">
                <div class="portfolio-tags">
                    <span class="portfolio-tag skeleton-line skeleton-w-24"></span>
                    <span class="portfolio-tag skeleton-line skeleton-w-20"></span>
                    <span class="portfolio-tag skeleton-line skeleton-w-16"></span>
                </div>
                <span class="portfolio-meta skeleton-line skeleton-w-30"></span>
            </div>
            <h2 class="portfolio-single-title skeleton-line skeleton-w-70"></h2>
            <div class="portfolio-single-image skeleton-block"></div>
            <div class="portfolio-single-content panel">
                <div class="single-skeleton-copy">
                    <div class="skeleton-line single-skeleton-line is-full"></div>
                    <div class="single-skeleton-row">
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                    </div>
                    <div class="single-skeleton-row">
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                    </div>
                    <div class="single-skeleton-row">
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                        <span class="skeleton-line single-skeleton-segment"></span>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="loadError" class="panel">
            <p class="text-block">{{ loadError }}</p>
        </div>

        <template v-else-if="project">
            <div class="portfolio-single-top">
                <div v-if="(project.tags ?? []).length" class="portfolio-tags">
                    <span v-for="tag in project.tags" :key="`${project.slug || project.id}-${tag}`" class="portfolio-tag">{{ tag }}</span>
                </div>
                <small class="portfolio-meta">
                    <span v-if="project.publishedAt">{{ formatPersianDate(project.publishedAt) }}</span>
                    <span v-if="project.readingMinutes">{{ project.readingMinutes }} دقیقه</span>
                </small>
            </div>

            <h2 class="portfolio-single-title">{{ project.title }}</h2>

            <div class="portfolio-single-image" :class="{ 'has-image': !!activeImage }">
                <template v-if="activeImage">
                    <img :src="activeImage" :alt="project.title">
                    <button
                        type="button"
                        class="portfolio-zoom-trigger"
                        :aria-label="`بزرگ‌نمایی تصویر ${project.title}`"
                        @click.stop="emit('open-image', { src: activeImage, alt: project.title, images: galleryImages, startIndex: 0 })">
                        <span class="zoom-trigger-label">بزرگ‌نمایی</span>
                    </button>
                </template>
            </div>

            <button
                v-if="galleryImages.length > 1"
                type="button"
                class="portfolio-gallery-open"
                @click="emit('open-image', { src: activeImage, alt: project.title, images: galleryImages, startIndex: 0 })">
                گالری تصاویر ({{ galleryImages.length }})
            </button>

            <div class="portfolio-single-content panel">
                <p class="text-block">{{ project.text }}</p>
            </div>

            <button
                v-if="project?.projectUrl"
                type="button"
                class="portfolio-single-visit-btn"
                @click="emit('open-project-url', project.projectUrl)">
                مشاهده پروژه
            </button>

        </template>
    </section>
</template>
