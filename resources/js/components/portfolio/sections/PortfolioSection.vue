<script setup>
import IconGlyph from '../../IconGlyph.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'نمونه کارها',
    },
    categories: {
        type: Array,
        default: () => [],
    },
    projects: {
        type: Array,
        default: () => [],
    },
    activeCategory: {
        type: String,
        default: 'all',
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

const emit = defineEmits(['change-category', 'open-project', 'open-image']);
</script>

<template>
    <section class="section" id="portfolio">
        <h2>{{ title }}</h2>
        <div class="underline"></div>

        <div class="portfolio-filter">
            <span
                :class="{ active: activeCategory === 'all' }"
                data-category="all"
                @click="emit('change-category', 'all')">
                همه
            </span>
            <span
                v-for="category in categories.filter((item) => item.slug !== 'all')"
                :key="category.slug"
                :data-category="category.slug"
                :class="{ active: activeCategory === category.slug }"
                @click="emit('change-category', category.slug)">
                {{ category.title }}
            </span>
        </div>

        <div v-if="loading" class="panel">
            <p class="text-block">در حال دریافت نمونه‌کارها...</p>
        </div>

        <div v-else-if="loadError" class="panel">
            <p class="text-block">{{ loadError }}</p>
        </div>

        <div v-else class="portfolio-grid">
            <article
                v-for="item in projects"
                :key="item.title"
                class="portfolio-card glass-panel"
                :class="{ 'is-link': !!item.projectUrl }"
                :data-category="item.category?.slug ?? 'uncategorized'"
                role="link"
                tabindex="0"
                :aria-label="`مشاهده پروژه ${item.title}`"
                @click="emit('open-project', item.projectUrl)">
                <div class="portfolio-thumb" :class="{ 'has-image': !!item.imageUrl }">
                    <template v-if="item.imageUrl">
                        <img :src="item.imageUrl" :alt="item.title">
                        <button
                            type="button"
                            class="portfolio-zoom-trigger"
                            :aria-label="`بزرگ‌نمایی تصویر ${item.title}`"
                            @click.stop="emit('open-image', { src: item.imageUrl, alt: item.title })">
                            <IconGlyph icon="mdi:magnify-plus" :size="34" />
                        </button>
                    </template>
                    <template v-else>
                        {{ item.title }}
                    </template>
                </div>
                <h4>{{ item.title }}</h4>
                <p>{{ item.text }}</p>
                <div v-if="(item.tags ?? []).length" class="portfolio-tags">
                    <span v-for="tag in item.tags" :key="`${item.title}-${tag}`" class="portfolio-tag">{{ tag }}</span>
                </div>
            </article>
        </div>
    </section>
</template>
