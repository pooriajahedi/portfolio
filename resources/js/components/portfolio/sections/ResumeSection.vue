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
});
</script>

<template>
    <section class="section" id="resume">
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
    </section>
</template>
