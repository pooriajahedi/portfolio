<script setup>
import { computed } from 'vue';
import hljs from 'highlight.js';
import IconGlyph from '../../IconGlyph.vue';

const props = defineProps({
    posts: {
        type: Array,
        default: () => [],
    },
    activeBlogSlug: {
        type: String,
        default: '',
    },
    selectedPost: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadingDetail: {
        type: Boolean,
        default: false,
    },
    loadError: {
        type: String,
        default: '',
    },
    loadDetailError: {
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

const highlightedPostContent = computed(() => {
    const rawContent = String(props.selectedPost?.content ?? '').trim();

    if (!rawContent) {
        return '';
    }

    if (typeof document === 'undefined') {
        return rawContent;
    }

    const template = document.createElement('template');
    template.innerHTML = rawContent;

    template.content.querySelectorAll('pre code').forEach((block) => {
        const source = block.textContent ?? '';

        if (!source.trim()) {
            return;
        }

        const languageClass = Array.from(block.classList).find((item) => item.startsWith('language-'));
        const language = languageClass?.replace('language-', '').trim();

        const highlighted = language && hljs.getLanguage(language)
            ? hljs.highlight(source, { language, ignoreIllegals: true })
            : hljs.highlightAuto(source, ['php', 'javascript', 'typescript', 'bash', 'sql', 'json', 'html', 'css']);

        const hasColoredTokens = highlighted.value.includes('hljs-');

        const finalHighlighted = hasColoredTokens
            ? highlighted
            : hljs.highlight(source, { language: 'php', ignoreIllegals: true });

        block.innerHTML = finalHighlighted.value;
        block.classList.add('hljs');
    });

    return template.innerHTML;
});
</script>

<template>
    <section class="section" :class="{ 'blog-section-single': activeBlogSlug !== '' }" id="blog">
        <template v-if="activeBlogSlug === ''">
            <h2>وبلاگ</h2>
            <div class="underline"></div>
        </template>

        <div v-if="loading" class="blog-grid" id="blogList">
            <article
                v-for="index in 6"
                :key="`blog-skeleton-${index}`"
                class="blog-card portfolio-card glass-panel">
                <div class="portfolio-thumb blog-card-image skeleton-block"></div>
                <div class="portfolio-card-body">
                    <small class="blog-date skeleton-line skeleton-w-30"></small>
                    <h4 class="skeleton-line skeleton-w-68"></h4>
                    <p class="skeleton-line skeleton-w-100"></p>
                    <p class="skeleton-line skeleton-w-86"></p>
                    <div class="portfolio-card-foot">
                        <span class="portfolio-more-btn blog-open skeleton-line skeleton-w-36"></span>
                    </div>
                </div>
            </article>
        </div>

        <div v-else-if="loadError" class="panel">
            <p class="text-block">{{ loadError }}</p>
        </div>

        <p v-else-if="!posts.length" class="blog-empty">هنوز مقاله‌ای منتشر نشده است.</p>

        <template v-else>
            <div v-if="activeBlogSlug === ''" class="blog-grid" id="blogList">
                <article
                    v-for="(item, index) in posts"
                    :key="item.slug || `${item.title}-${index}`"
                    class="blog-card portfolio-card glass-panel">
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
                            <button class="portfolio-more-btn blog-open" type="button" @click.stop="item.slug && emit('open-blog', item.slug)">مطالعه مقاله</button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="blog-detail is-open portfolio-single" id="blogDetail" tabindex="-1">
                <div class="portfolio-single-head">
                    <button class="portfolio-single-back" type="button" @click="emit('close-blog')">
                        <span class="portfolio-single-back-icon" aria-hidden="true">→</span>
                        بازگشت به لیست مقالات
                    </button>
                    <small class="portfolio-meta blog-single-date-inline">
                        <span>{{ selectedPost?.date ?? '' }}</span>
                    </small>
                </div>

                <div v-if="loadingDetail" class="blog-detail-item">
                    <div class="portfolio-single-top">
                        <div class="portfolio-tags">
                            <span class="portfolio-tag skeleton-line skeleton-w-20"></span>
                            <span class="portfolio-tag skeleton-line skeleton-w-16"></span>
                        </div>
                        <small class="portfolio-meta skeleton-line skeleton-w-30"></small>
                    </div>
                    <h3 class="portfolio-single-title skeleton-line skeleton-w-72"></h3>
                    <div class="portfolio-single-image skeleton-block"></div>
                    <div class="blog-detail-content portfolio-single-content panel glass-panel">
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

                <div v-else-if="loadDetailError" class="panel">
                    <p class="text-block">{{ loadDetailError }}</p>
                </div>

                <article v-else-if="selectedPost" class="blog-detail-item">
                    <h3 class="portfolio-single-title">{{ selectedPost?.title }}</h3>
                    <div v-if="selectedPost?.imageUrl" class="portfolio-single-image">
                        <img :src="selectedPost?.imageUrl" :alt="selectedPost?.title">
                        <button
                            type="button"
                            class="portfolio-zoom-trigger"
                            :aria-label="`بزرگ‌نمایی تصویر ${selectedPost?.title}`"
                            @click.stop="emit('open-image', { src: selectedPost?.imageUrl, alt: selectedPost?.title })">
                            <IconGlyph icon="mdi:magnify-plus" :size="34" />
                        </button>
                    </div>
                    <div class="blog-detail-content portfolio-single-content panel glass-panel" v-html="highlightedPostContent"></div>
                </article>
            </div>
        </template>
    </section>
</template>
