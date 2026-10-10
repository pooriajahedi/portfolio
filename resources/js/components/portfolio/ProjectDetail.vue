<script setup>
import { computed } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';
import { normalizeExternalUrl } from '../../support/urls';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['back', 'preview']);

const { isRtl, t } = useLang();

const projectUrl = computed(() => normalizeExternalUrl(props.project?.projectUrl));
const gallery = computed(() => props.project?.galleryImages ?? []);
</script>

<template>
    <article id="project-detail" class="scroll-mt-24 py-12 sm:py-16">
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-border-strong px-4 py-2 text-sm text-muted-foreground transition-colors hover:border-primary hover:text-primary"
            @click="emit('back')"
        >
            <UiIcon :name="isRtl ? 'arrowRight' : 'arrowLeft'" :size="16" />
            {{ t({ fa: 'بازگشت به پروژه‌ها', en: 'Back to projects' }) }}
        </button>

        <p v-if="loading" class="mt-10 text-base text-muted-foreground">
            {{ t({ fa: 'در حال بارگذاری…', en: 'Loading…' }) }}
        </p>

        <p v-else-if="error" class="panel mt-10 p-6 text-base text-destructive">{{ error }}</p>

        <template v-else-if="project">
            <header class="mt-8">
                <span
                    v-if="project.category"
                    dir="auto"
                    class="text-sm font-medium text-primary"
                >{{ t(project.category.title) }}</span>
                <h1 dir="auto" class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ t(project.title) }}</h1>

                <ul v-if="project.tags?.length" dir="ltr" class="mt-5 flex flex-wrap gap-2" :class="isRtl ? 'justify-end' : ''">
                    <li
                        v-for="tag in project.tags"
                        :key="tag"
                        class="rounded-md border border-border px-2.5 py-1 text-sm text-muted-foreground"
                    >
                        {{ tag }}
                    </li>
                </ul>
            </header>

            <img
                v-if="project.imageUrl"
                :src="project.imageUrl"
                :alt="t(project.title)"
                class="mt-8 w-full rounded-lg border border-border object-cover"
                loading="lazy"
            />

            <p dir="auto" class="mt-8 whitespace-pre-line text-base leading-relaxed text-muted-foreground">
                {{ t(project.text) }}
            </p>

            <a
                v-if="projectUrl"
                :href="projectUrl"
                target="_blank"
                rel="noreferrer noopener"
                class="mt-8 inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-base font-medium text-primary-foreground transition-opacity hover:opacity-90"
            >
                <UiIcon name="externalLink" :size="18" />
                {{ t({ fa: 'مشاهده پروژه', en: 'Visit project' }) }}
            </a>

            <section v-if="gallery.length" class="mt-12">
                <h2 class="mono-label">{{ t({ fa: 'گالری تصاویر', en: 'Gallery' }) }}</h2>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <button
                        v-for="(image, index) in gallery"
                        :key="image"
                        type="button"
                        class="panel overflow-hidden transition-colors hover:border-primary/40"
                        @click="emit('preview', { images: gallery, index })"
                    >
                        <img
                            :src="image"
                            :alt="`${t(project.title)} — ${index + 1}`"
                            loading="lazy"
                            class="aspect-16/10 w-full object-cover opacity-85 transition-opacity hover:opacity-100"
                        />
                    </button>
                </div>
            </section>
        </template>
    </article>
</template>
