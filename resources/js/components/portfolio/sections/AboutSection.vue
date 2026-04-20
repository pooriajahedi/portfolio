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
});
</script>

<template>
    <section class="section" id="about">
        <h2>{{ about.title ?? 'درباره من' }}</h2>
        <div class="underline"></div>
        <p class="text-block">{{ about.paragraphOne }}</p>
        <p v-if="about.paragraphTwo" class="text-block">{{ about.paragraphTwo }}</p>

        <h2 style="font-size: clamp(26px, 2.2vw, 32px); margin-top: 22px;">بیشتر روی چه چیزهایی تمرکز دارم</h2>
        <div class="service-grid">
            <article v-for="item in serviceCards" :key="item.title" class="service-card glass-panel hover-light">
                <h3 class="service-title">{{ item.title }}</h3>
                <p class="service-desc">{{ item.description }}</p>
            </article>
        </div>

        <h2 style="font-size: clamp(26px, 2.2vw, 32px); margin-top: 30px;">مهارت‌ها</h2>
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
    </section>
</template>
