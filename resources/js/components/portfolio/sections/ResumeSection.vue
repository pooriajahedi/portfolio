<script setup>
import IconGlyph from '../../IconGlyph.vue';

defineProps({
    timeline: {
        type: Array,
        default: () => [],
    },
    resumeFileUrl: {
        type: String,
        default: '',
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <section class="section" id="resume">
        <template v-if="loading">
            <div class="resume-head">
                <div class="skeleton-line skeleton-title"></div>
                <div class="resume-download-btn skeleton-line skeleton-w-38 skeleton-h-56"></div>
            </div>
            <div class="underline"></div>

            <div class="timeline">
                <article v-for="index in 4" :key="`resume-skeleton-${index}`" class="timeline-item">
                    <small class="timeline-period skeleton-line skeleton-w-40"></small>
                    <h4 class="skeleton-line skeleton-w-65"></h4>
                    <p class="skeleton-line skeleton-w-100"></p>
                    <p class="skeleton-line skeleton-w-86"></p>
                </article>
            </div>
        </template>

        <template v-else>
            <div class="resume-head">
                <h2>رزومه</h2>
                <a
                    v-if="resumeFileUrl"
                    class="resume-download-btn"
                    :href="resumeFileUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    download>
                    <IconGlyph icon="mdi:cloud-download-outline" alt="دانلود فایل رزومه" :size="24" />
                    <span>دانلود فایل رزومه</span>
                </a>
            </div>
            <div class="underline"></div>

            <div class="timeline">
                <article v-for="item in timeline" :key="`${item.title}-${item.period}`" class="timeline-item">
                    <small v-if="item.period" class="timeline-period">{{ item.period }}</small>
                    <h4>{{ item.title }}</h4>
                    <p>{{ item.text }}</p>
                </article>
            </div>
        </template>
    </section>
</template>
