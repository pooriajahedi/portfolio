<script setup>
import IconGlyph from '../../IconGlyph.vue';

const props = defineProps({
    posts: {
        type: Array,
        default: () => [],
    },
    activeBlogIndex: {
        type: Number,
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

const emit = defineEmits(['open-blog', 'close-blog', 'open-image']);

const getUniformExcerpt = (value, maxLength = 150) => {
    const normalized = String(value ?? '').replace(/\s+/g, ' ').trim();
    if (normalized.length <= maxLength) return normalized;

    return `${normalized.slice(0, maxLength).trim()}...`;
};
</script>

<template>
    <section class="section" id="blog">
        <template v-if="activeBlogIndex === null">
            <h2>وبلاگ</h2>
            <div class="underline"></div>
        </template>

        <div v-if="loading" class="panel">
            <p class="text-block">در حال دریافت مقالات...</p>
        </div>

        <div v-else-if="loadError" class="panel">
            <p class="text-block">{{ loadError }}</p>
        </div>

        <p v-else-if="!posts.length" class="blog-empty">هنوز مقاله‌ای منتشر نشده است.</p>

        <template v-else>
            <div v-if="activeBlogIndex === null" class="blog-grid" id="blogList">
                <article
                    v-for="(item, index) in posts"
                    :key="`${item.title}-${index}`"
                    class="blog-card portfolio-card glass-panel"
                    @click="emit('open-blog', index)">
                    <div class="portfolio-thumb blog-card-image" :class="{ 'has-image': !!item.imageUrl }">
                        <template v-if="item.imageUrl">
                            <img :src="item.imageUrl" :alt="item.title">
                        </template>
                        <template v-else>
                            {{ item.title }}
                        </template>
                    </div>
                    <div class="portfolio-card-body">
                        <small class="blog-date">{{ item.date ?? '' }}</small>
                        <h4>{{ item.title }}</h4>
                        <p>{{ getUniformExcerpt(item.excerpt) }}</p>
                        <div class="portfolio-card-foot">
                            <button class="portfolio-more-btn blog-open" type="button" @click.stop="emit('open-blog', index)">مطالعه مقاله</button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="blog-detail is-open portfolio-single" id="blogDetail">
                <div class="portfolio-single-head">
                    <button class="portfolio-single-back" type="button" @click="emit('close-blog')">
                        <span class="portfolio-single-back-icon" aria-hidden="true">→</span>
                        بازگشت به لیست مقالات
                    </button>
                    <small class="portfolio-meta blog-single-date-inline">
                        <span>{{ posts[activeBlogIndex]?.date ?? '' }}</span>
                    </small>
                </div>

                <article class="blog-detail-item">
                    <h3 class="portfolio-single-title">{{ posts[activeBlogIndex]?.title }}</h3>
                    <div v-if="posts[activeBlogIndex]?.imageUrl" class="portfolio-single-image">
                        <img :src="posts[activeBlogIndex]?.imageUrl" :alt="posts[activeBlogIndex]?.title">
                        <button
                            type="button"
                            class="portfolio-zoom-trigger"
                            :aria-label="`بزرگ‌نمایی تصویر ${posts[activeBlogIndex]?.title}`"
                            @click.stop="emit('open-image', { src: posts[activeBlogIndex]?.imageUrl, alt: posts[activeBlogIndex]?.title })">
                            <IconGlyph icon="mdi:magnify-plus" :size="34" />
                        </button>
                    </div>
                    <div class="blog-detail-content portfolio-single-content panel glass-panel" v-html="posts[activeBlogIndex]?.content"></div>
                </article>
            </div>
        </template>
    </section>
</template>
