<script setup>
import ProjectCover from './ProjectCover.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    project: {
        type: Object,
        required: true,
    },
    // The lead project spans the full grid width with its cover beside the text.
    featured: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['open']);

const { t, pick } = useLang();
</script>

<template>
    <a
        :href="`/portfolio/${project.slug}`"
        class="card group flex flex-col overflow-hidden transition-[transform,border-color] duration-200 hover:-translate-y-1 hover:border-primary"
        :class="featured ? 'md:col-span-2 md:flex-row' : ''"
        @click.prevent="emit('open', project.slug)"
    >
        <div
            class="aspect-[16/10] border-b border-border"
            :class="featured ? 'md:aspect-auto md:min-h-[24rem] md:flex-1 md:basis-1/2 md:border-b-0 md:border-e' : ''"
        >
            <ProjectCover
                :type="project.coverType"
                :color="project.brandColor"
                :image="pick(project.image)"
                :gallery="pick(project.gallery) ?? []"
                :title="t(project.title)"
            />
        </div>

        <div
            class="flex flex-1 flex-col gap-3.5 p-6 sm:p-7"
            :class="featured ? 'md:basis-1/2 md:justify-center md:p-10' : ''"
        >
            <div class="flex items-center justify-between gap-3">
                <span v-if="project.category" class="label-mono !text-primary font-semibold">
                    {{ t(project.category.title) }}
                </span>
                <span
                    class="ms-auto inline-grid size-10 place-items-center rounded-full border border-border-strong text-muted-foreground transition-colors group-hover:border-primary group-hover:bg-primary group-hover:text-primary-foreground"
                >
                    <UiIcon name="arrowUpRight" :size="18" :stroke-width="2" class="rtl:-scale-x-100" />
                </span>
            </div>

            <h3 class="heading text-[clamp(1.375rem,2.2vw,1.75rem)]">{{ t(project.title) }}</h3>

            <p class="line-clamp-4 text-base text-muted-foreground">{{ t(project.text) }}</p>

            <ul v-if="project.tags?.length" class="mt-auto flex flex-wrap gap-2 pt-1.5">
                <li
                    v-for="tag in project.tags"
                    :key="tag"
                    dir="ltr"
                    class="inline-flex min-h-8 items-center rounded-[0.625rem] border border-border bg-background px-3 font-mono text-[0.8125rem] text-muted-foreground"
                >
                    {{ tag }}
                </li>
            </ul>
        </div>
    </a>
</template>
