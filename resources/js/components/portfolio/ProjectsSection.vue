<script setup>
import { computed, ref } from 'vue';
import ProjectCard from './ProjectCard.vue';
import SectionHeader from './SectionHeader.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    title: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['open']);

const { t } = useLang();

const ALL = 'all';
const activeCategory = ref(ALL);

// Only categories that actually hold a project make useful filters.
const filters = computed(() =>
    props.categories.filter((category) => props.projects.some((project) => project.category?.slug === category.slug)),
);

const visibleProjects = computed(() =>
    activeCategory.value === ALL
        ? props.projects
        : props.projects.filter((project) => project.category?.slug === activeCategory.value),
);

const heading = computed(() => t(props.title) || t({ fa: 'نمونه‌کارها', en: 'Selected work' }));

// A lead card keeps the two-column grid full: it is only needed when the count is odd.
const hasLeadCard = computed(() => visibleProjects.value.length % 2 === 1);
</script>

<template>
    <section id="projects" v-reveal="'projects'" class="mx-auto w-full max-w-[1240px] px-5 py-16 sm:px-10 sm:py-28">
        <SectionHeader index="04" :label="t({ fa: 'نمونه‌کارها', en: 'Selected work' })" />

        <div class="mt-9 flex flex-wrap items-end justify-between gap-7">
            <h2 class="heading text-[clamp(1.75rem,3.6vw,3rem)]">{{ heading }}</h2>

            <div
                v-if="filters.length > 1"
                role="group"
                :aria-label="t({ fa: 'فیلتر دسته‌بندی', en: 'Filter by category' })"
                class="flex flex-wrap gap-2.5"
            >
                <button
                    type="button"
                    class="min-h-11 rounded-full border px-5 text-[0.9375rem] font-medium transition-colors"
                    :class="
                        activeCategory === ALL
                            ? 'border-primary bg-primary font-semibold text-primary-foreground'
                            : 'border-border-strong hover:border-primary'
                    "
                    :aria-pressed="activeCategory === ALL"
                    @click="activeCategory = ALL"
                >
                    {{ t({ fa: 'همه', en: 'All' }) }}
                </button>

                <button
                    v-for="category in filters"
                    :key="category.slug"
                    type="button"
                    class="min-h-11 rounded-full border px-5 text-[0.9375rem] font-medium transition-colors"
                    :class="
                        activeCategory === category.slug
                            ? 'border-primary bg-primary font-semibold text-primary-foreground'
                            : 'border-border-strong hover:border-primary'
                    "
                    :aria-pressed="activeCategory === category.slug"
                    @click="activeCategory = category.slug"
                >
                    {{ t(category.title) }}
                </button>
            </div>
        </div>

        <div class="mt-11 grid grid-cols-1 gap-6 md:grid-cols-2">
            <ProjectCard
                v-for="(project, index) in visibleProjects"
                :key="project.id"
                :project="project"
                :featured="hasLeadCard && index === 0"
                @open="emit('open', $event)"
            />
        </div>
    </section>
</template>
