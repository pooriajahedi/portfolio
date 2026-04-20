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
</script>

<template>
    <section class="section" id="blog">
        <h2>وبلاگ</h2>
        <div class="underline"></div>

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
                    class="blog-card glass-panel"
                    @click="emit('open-blog', index)">
                    <div v-if="item.imageUrl" class="blog-card-image">
                        <img :src="item.imageUrl" :alt="item.title">
                    </div>
                    <small>{{ item.date ?? '' }}</small>
                    <h4>{{ item.title }}</h4>
                    <p>{{ item.excerpt }}</p>
                    <button class="blog-open" type="button" @click.stop="emit('open-blog', index)">مشاهده مقاله</button>
                </article>
            </div>

            <div v-else class="blog-detail is-open" id="blogDetail">
                <button class="blog-back" type="button" @click="emit('close-blog')">بازگشت به لیست مقالات</button>

                <article class="blog-detail-item" style="display:block;">
                    <small style="display:block;color:#9ca5b2;margin-bottom:8px;">{{ posts[activeBlogIndex]?.date ?? '' }}</small>
                    <h3 style="font-size:clamp(28px,2.4vw,36px);line-height:1.3;margin-bottom:12px;">{{ posts[activeBlogIndex]?.title }}</h3>
                    <div v-if="posts[activeBlogIndex]?.imageUrl" class="blog-detail-image">
                        <img :src="posts[activeBlogIndex]?.imageUrl" :alt="posts[activeBlogIndex]?.title">
                        <button
                            type="button"
                            class="blog-zoom-trigger"
                            :aria-label="`بزرگ‌نمایی تصویر ${posts[activeBlogIndex]?.title}`"
                            @click.stop="emit('open-image', { src: posts[activeBlogIndex]?.imageUrl, alt: posts[activeBlogIndex]?.title })">
                            <IconGlyph icon="mdi:magnify-plus" :size="34" class-name="blog-zoom-icon" />
                        </button>
                    </div>
                    <div class="blog-detail-content glass-panel" v-html="posts[activeBlogIndex]?.content"></div>
                </article>
            </div>
        </template>
    </section>
</template>
