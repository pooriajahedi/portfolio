<script setup>
import { computed } from 'vue';
import ProjectCover from './ProjectCover.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';
import { normalizeExternalUrl } from '../../support/urls';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    // The project listed after this one, offered as the next stop.
    next: {
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

const emit = defineEmits(['back', 'preview', 'open']);

const { t, pick } = useLang();

const projectUrl = computed(() => normalizeExternalUrl(props.project?.projectUrl));
const gallery = computed(() => pick(props.project?.gallery) ?? []);
</script>

<template>
    <article id="project-detail" class="mx-auto w-full max-w-[1240px] px-5 py-10 sm:px-10 sm:py-14">
        <button
            type="button"
            class="inline-flex min-h-11 items-center gap-2.5 rounded-full border border-border-strong px-5 text-[0.9375rem] font-medium transition-colors hover:border-primary hover:text-primary"
            @click="emit('back')"
        >
            <UiIcon name="arrowLeft" :size="16" :stroke-width="2" class="rtl:-scale-x-100" />
            {{ t({ fa: 'بازگشت به نمونه‌کارها', en: 'Back to projects' }) }}
        </button>

        <p v-if="loading" class="mt-12 text-base text-muted-foreground">
            {{ t({ fa: 'در حال بارگذاری…', en: 'Loading…' }) }}
        </p>

        <p v-else-if="error" class="card mt-12 p-6 text-base">{{ error }}</p>

        <template v-else-if="project">
            <header class="mt-10 flex flex-wrap items-end justify-between gap-x-12 gap-y-7">
                <div class="min-w-0 flex-[1_1_32rem]">
                    <span v-if="project.category" class="label-mono !text-primary font-semibold">
                        {{ t(project.category.title) }}
                    </span>
                    <h1 class="font-display mt-3.5 text-[clamp(2.125rem,5vw,4.25rem)]">{{ t(project.title) }}</h1>
                </div>

                <ul v-if="project.tags?.length" class="flex flex-[0_1_24rem] flex-wrap gap-2">
                    <li
                        v-for="tag in project.tags"
                        :key="tag"
                        dir="ltr"
                        class="inline-flex min-h-[2.125rem] items-center rounded-[0.625rem] border border-border bg-card px-3.5 font-mono text-[0.8125rem] text-muted-foreground"
                    >
                        {{ tag }}
                    </li>
                </ul>
            </header>

            <div class="mt-10 h-[clamp(18rem,38vw,35rem)] overflow-hidden rounded-[1.75rem] border border-border">
                <ProjectCover
                    :type="project.coverType"
                    :color="project.brandColor"
                    :image="pick(project.image)"
                    :gallery="gallery"
                    :title="t(project.title)"
                />
            </div>

            <div class="mt-14 flex flex-wrap items-start gap-x-[4.5rem] gap-y-12">
                <p class="min-w-0 flex-[2_1_34rem] whitespace-pre-line text-lg text-muted-foreground">
                    {{ t(project.text) }}
                </p>

                <aside class="card flex flex-[1_1_18rem] flex-col gap-5 p-7 lg:max-w-[25rem]">
                    <div v-if="project.category">
                        <div class="label-mono">{{ t({ fa: 'دسته‌بندی', en: 'Category' }) }}</div>
                        <div class="mt-1.5 font-semibold">{{ t(project.category.title) }}</div>
                    </div>

                    <div v-if="project.tags?.length" class="border-t border-border pt-5">
                        <div class="label-mono">{{ t({ fa: 'تکنولوژی‌ها', en: 'Stack' }) }}</div>
                        <ul class="mt-2.5 flex flex-wrap gap-2">
                            <li
                                v-for="tag in project.tags"
                                :key="tag"
                                dir="ltr"
                                class="inline-flex min-h-[1.875rem] items-center rounded-[0.625rem] border border-border bg-background px-2.5 font-mono text-xs text-muted-foreground"
                            >
                                {{ tag }}
                            </li>
                        </ul>
                    </div>

                    <a
                        v-if="projectUrl"
                        :href="projectUrl"
                        target="_blank"
                        rel="noreferrer noopener"
                        class="btn btn-primary mt-1"
                    >
                        <UiIcon name="externalLink" :size="18" :stroke-width="2" />
                        {{ t({ fa: 'مشاهده پروژه', en: 'Visit project' }) }}
                    </a>
                </aside>
            </div>

            <section v-if="gallery.length" class="mt-20">
                <h2 class="label-mono">{{ t({ fa: 'گالری تصاویر', en: 'Gallery' }) }}</h2>

                <div class="mt-6 grid grid-cols-[repeat(auto-fit,minmax(min(100%,28rem),1fr))] gap-5">
                    <button
                        v-for="(image, index) in gallery"
                        :key="image"
                        type="button"
                        class="card group relative overflow-hidden rounded-[1.25rem] transition-colors hover:border-primary"
                        :aria-label="t({ fa: 'بزرگ‌نمایی تصویر', en: 'Zoom image' })"
                        @click="emit('preview', { images: gallery, index })"
                    >
                        <img
                            :src="image"
                            :alt="`${t(project.title)} — ${index + 1}`"
                            loading="lazy"
                            class="aspect-[16/10] w-full object-cover"
                        />
                        <span
                            class="absolute bottom-3.5 end-3.5 inline-flex min-h-10 items-center gap-2 rounded-xl border border-border-strong bg-card px-3.5 text-sm font-medium transition-colors group-hover:border-primary group-hover:text-primary"
                        >
                            <UiIcon name="zoom" :size="16" :stroke-width="2" />
                            {{ t({ fa: 'بزرگ‌نمایی', en: 'Zoom' }) }}
                        </span>
                    </button>
                </div>
            </section>

            <a
                v-if="next"
                :href="`/portfolio/${next.slug}`"
                class="card mt-20 flex flex-wrap items-center justify-between gap-5 px-7 py-8 transition-[transform,border-color] duration-200 hover:-translate-y-1 hover:border-primary sm:px-9"
                @click.prevent="emit('open', next.slug)"
            >
                <div class="min-w-0">
                    <span class="label-mono !text-primary font-semibold">{{ t({ fa: 'پروژه‌ی بعدی', en: 'Next project' }) }}</span>
                    <div class="heading mt-1.5 text-[clamp(1.375rem,2.6vw,2rem)]">{{ t(next.title) }}</div>
                </div>
                <span class="inline-grid size-14 shrink-0 place-items-center rounded-full bg-primary text-primary-foreground">
                    <UiIcon name="arrowRight" :size="22" :stroke-width="2" class="rtl:-scale-x-100" />
                </span>
            </a>
        </template>
    </article>
</template>
