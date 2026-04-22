<script setup>
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

const emit = defineEmits(['change-category', 'open-detail']);

const getUniformSummary = (value, maxLength = 150) => {
    const normalized = String(value ?? '').replace(/\s+/g, ' ').trim();
    if (normalized.length <= maxLength) return normalized;

    return `${normalized.slice(0, maxLength).trim()}...`;
};

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

        <div v-if="loading" class="portfolio-grid">
            <article v-for="index in 6" :key="`portfolio-skeleton-${index}`" class="portfolio-card glass-panel">
                <div class="portfolio-thumb skeleton-block"></div>
                <div class="portfolio-card-body">
                    <h4 class="skeleton-line skeleton-w-62"></h4>
                    <p class="skeleton-line skeleton-w-100"></p>
                    <p class="skeleton-line skeleton-w-82"></p>
                    <div class="portfolio-tags">
                        <span class="portfolio-tag skeleton-line skeleton-w-24"></span>
                        <span class="portfolio-tag skeleton-line skeleton-w-20"></span>
                        <span class="portfolio-tag skeleton-line skeleton-w-16"></span>
                    </div>
                    <div class="portfolio-card-foot">
                        <span class="portfolio-more-btn skeleton-line skeleton-w-36"></span>
                    </div>
                </div>
            </article>
        </div>

        <div v-else-if="loadError" class="panel">
            <p class="text-block">{{ loadError }}</p>
        </div>

        <div v-else class="portfolio-grid">
            <article
                v-for="item in projects"
                :key="item.slug || item.id || item.title"
                class="portfolio-card glass-panel"
                :data-category="item.category?.slug ?? 'uncategorized'">
                <div class="portfolio-thumb" :class="{ 'has-image': !!item.imageUrl }">
                    <template v-if="item.imageUrl">
                        <img :src="item.imageUrl" :alt="item.title">
                    </template>
                    <template v-else>
                        {{ item.title }}
                    </template>
                </div>
                <div class="portfolio-card-body">
                    <h4>{{ item.title }}</h4>
                    <p>{{ getUniformSummary(item.text) }}</p>

                    <div v-if="(item.tags ?? []).length" class="portfolio-tags">
                        <span v-for="tag in item.tags" :key="`${item.title}-${tag}`" class="portfolio-tag">{{ tag }}</span>
                    </div>

                    <div class="portfolio-card-foot">
                        <button
                            type="button"
                            class="portfolio-more-btn"
                            @click.stop="item.slug && emit('open-detail', item.slug)">
                            جزئیات پروژه
                        </button>
                        <small v-if="item.readingMinutes" class="portfolio-meta">
                            <span>{{ item.readingMinutes }} دقیقه</span>
                        </small>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
