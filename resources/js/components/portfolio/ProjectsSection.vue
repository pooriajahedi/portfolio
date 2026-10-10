<script setup>
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['open']);

const { isRtl, t } = useLang();
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <a
            v-for="project in projects"
            :key="project.id"
            :href="`/portfolio/${project.slug}`"
            class="panel group flex flex-col overflow-hidden transition-colors hover:border-border-strong"
            @click.prevent="emit('open', project.slug)"
        >
            <img
                v-if="project.imageUrl"
                :src="project.imageUrl"
                :alt="t(project.title)"
                width="1024"
                height="640"
                loading="lazy"
                class="aspect-16/10 w-full border-b border-border object-cover opacity-85 transition-opacity group-hover:opacity-100"
            />

            <div class="flex flex-1 flex-col p-6">
                <div class="flex items-start justify-between gap-3">
                    <h3 dir="auto" class="text-xl font-semibold transition-colors group-hover:text-primary">
                        {{ t(project.title) }}
                    </h3>
                    <UiIcon
                        name="arrowUpRight"
                        :size="18"
                        class="mt-1 text-muted-foreground transition-colors group-hover:text-primary"
                        :class="isRtl ? '-scale-x-100' : ''"
                    />
                </div>

                <p dir="auto" class="mt-2.5 line-clamp-4 flex-1 text-base leading-relaxed text-muted-foreground">
                    {{ t(project.text) }}
                </p>

                <ul v-if="project.tags?.length" dir="ltr" class="mt-5 flex flex-wrap gap-2" :class="isRtl ? 'justify-end' : ''">
                    <li
                        v-for="tag in project.tags"
                        :key="tag"
                        class="rounded-md border border-border px-2.5 py-1 text-sm text-muted-foreground"
                    >
                        {{ tag }}
                    </li>
                </ul>
            </div>
        </a>
    </div>
</template>
