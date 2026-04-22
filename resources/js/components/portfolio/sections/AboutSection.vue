<script setup>
import IconGlyph from '../../IconGlyph.vue';

defineProps({
    about: {
        type: Object,
        required: true,
    },
    serviceCards: {
        type: Array,
        default: () => [],
    },
    skillCategoryLabels: {
        type: Object,
        default: () => ({}),
    },
    groupedSkills: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <section class="section" id="about">
        <template v-if="loading">
            <div class="skeleton-line skeleton-title"></div>
            <div class="underline"></div>
            <div class="about-skeleton-copy">
                <div class="skeleton-line about-skeleton-line is-full"></div>
                <div class="about-skeleton-segment-row">
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                </div>
                <div class="about-skeleton-segment-row">
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                </div>
                <div class="about-skeleton-segment-row">
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                    <span class="skeleton-line about-skeleton-segment"></span>
                </div>
            </div>

            <div class="skeleton-line skeleton-subtitle mt-24"></div>
            <div class="service-grid">
                <article v-for="index in 3" :key="`about-service-skeleton-${index}`" class="service-card glass-panel">
                    <div class="service-title skeleton-line skeleton-w-60"></div>
                    <p class="service-desc skeleton-line skeleton-w-100"></p>
                    <p class="service-desc skeleton-line skeleton-w-90"></p>
                </article>
            </div>

            <div class="skeleton-line skeleton-subtitle mt-30"></div>
            <div class="skills-categories">
                <div class="skill-category-row">
                    <div class="skill-category-head">
                        <div class="skill-category-label skeleton-line skeleton-w-40"></div>
                    </div>
                    <div class="skill-items">
                        <article v-for="index in 6" :key="`about-skill-skeleton-${index}`" class="skill-item">
                            <span class="skeleton-line skeleton-w-70"></span>
                            <span class="skeleton-circle skeleton-icon-28"></span>
                        </article>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <h2>{{ about.title ?? 'درباره من' }}</h2>
            <div class="underline"></div>
            <p class="text-block">{{ about.paragraphOne }}</p>
            <p v-if="about.paragraphTwo" class="text-block">{{ about.paragraphTwo }}</p>

            <h2 style="font-size: clamp(21px, 1.6vw, 25px); margin-top: 18px;">بیشتر روی چه چیزهایی تمرکز دارم</h2>
            <div class="service-grid">
                <article v-for="item in serviceCards" :key="item.title" class="service-card glass-panel hover-light">
                    <h3 class="service-title">{{ item.title }}</h3>
                    <p class="service-desc">{{ item.description }}</p>
                </article>
            </div>

            <h2 style="font-size: clamp(21px, 1.6vw, 25px); margin-top: 24px;">مهارت‌ها</h2>
            <div class="skills-categories">
                <template v-for="(categoryLabel, categoryKey) in skillCategoryLabels" :key="categoryKey">
                    <div v-if="(groupedSkills[categoryKey] ?? []).length" class="skill-category-row">
                        <div class="skill-category-head">
                            <h3 class="skill-category-label">{{ categoryLabel }}</h3>
                        </div>
                        <div class="skill-items">
                            <article v-for="item in groupedSkills[categoryKey]" :key="`${categoryKey}-${item.title}`" class="skill-item hover-light">
                                <span>{{ item.title }}</span>
                                <IconGlyph :icon="item.icon || 'mdi:star-four-points-circle'" :alt="item.title" :size="28" />
                            </article>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </section>
</template>
