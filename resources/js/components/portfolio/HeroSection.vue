<script setup>
import { computed } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';
import { resolveStorageUrl } from '../../support/urls';

const props = defineProps({
    profile: {
        type: Object,
        default: () => ({}),
    },
    socials: {
        type: Array,
        default: () => [],
    },
    // Short list of headline technologies shown on the portrait card.
    stack: {
        type: Array,
        default: () => [],
    },
    // Full years of experience, or null while the experience timeline is empty.
    years: {
        type: Number,
        default: null,
    },
    resumeUrl: {
        type: String,
        default: '',
    },
});

const { t, n } = useLang();

const avatarUrl = computed(() => resolveStorageUrl(props.profile?.avatarImage) || '/images/hero/pooria-hero.jpeg');
const avatarAlt = computed(() => t(props.profile?.name ?? { fa: 'پوریا جاهدی', en: 'Pooria Jahedi' }));
</script>

<template>
    <section class="relative">
        <div aria-hidden="true" class="dot-grid pointer-events-none absolute inset-0 opacity-70"></div>

        <div class="relative mx-auto flex w-full max-w-[1240px] flex-wrap items-center gap-x-16 gap-y-14 px-5 pb-14 pt-9 sm:px-10 sm:pb-24 sm:pt-20">
            <div class="min-w-0 flex-[1_1_32rem]">
                <span
                    v-if="profile?.status"
                    class="inline-flex min-h-10 items-center gap-2.5 rounded-full border border-border-strong bg-card px-4 text-sm font-medium"
                >
                    <span class="size-2 rounded-full bg-success shadow-[0_0_0_4px_color-mix(in_srgb,var(--ok)_24%,transparent)]"></span>
                    {{ t(profile.status) }}
                </span>

                <h1 class="font-display mt-7 text-[clamp(3.25rem,8.2vw,7rem)]">{{ t(profile?.name) }}</h1>

                <div class="mt-5 flex items-center gap-3.5">
                    <span class="h-0.5 w-10 shrink-0 bg-primary"></span>
                    <p class="text-[clamp(1.0625rem,1.7vw,1.375rem)] font-semibold text-primary">{{ t(profile?.role) }}</p>
                </div>

                <p v-if="t(profile?.tagline)" class="mt-5 max-w-xl text-lg text-muted-foreground sm:text-xl">
                    {{ t(profile.tagline) }}
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a href="#projects" class="btn btn-primary max-sm:w-full">
                        {{ t({ fa: 'دیدن نمونه‌کارها', en: 'View projects' }) }}
                        <UiIcon name="arrowRight" :size="20" :stroke-width="2" class="rtl:-scale-x-100" />
                    </a>

                    <a v-if="resumeUrl" :href="resumeUrl" download class="btn btn-ghost max-sm:w-full">
                        <UiIcon name="download" :size="20" />
                        {{ t({ fa: 'دانلود رزومه', en: 'Download resume' }) }}
                    </a>

                    <a
                        v-for="social in socials"
                        :key="social.label"
                        :href="social.href"
                        :aria-label="social.label"
                        :title="social.label"
                        :target="social.href.startsWith('http') ? '_blank' : undefined"
                        rel="noreferrer noopener"
                        class="inline-grid size-[3.375rem] place-items-center rounded-2xl border border-border-strong text-muted-foreground transition-[transform,color,border-color] hover:-translate-y-0.5 hover:border-primary hover:text-primary"
                    >
                        <UiIcon :name="social.icon" :size="22" />
                    </a>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[25rem] flex-[0_1_25rem]">
                <div
                    aria-hidden="true"
                    class="pointer-events-none absolute -bottom-[18px] start-[18px] -end-[18px] top-[18px] rounded-b-[28px] rounded-t-[200px] border border-primary"
                ></div>

                <div class="relative isolate aspect-[4/5] overflow-hidden rounded-b-[28px] rounded-t-[200px] bg-[var(--portrait-bg)]">
                    <img
                        :src="avatarUrl"
                        :alt="avatarAlt"
                        width="512"
                        height="512"
                        class="absolute -left-[15%] -top-[23%] size-[135%] max-w-none object-cover mix-blend-multiply [filter:var(--portrait-filter)] [object-position:48%_50%]"
                    />
                </div>

                <div
                    v-if="stack.length"
                    class="card absolute bottom-9 w-[min(17.5rem,86%)] p-4 shadow-float start-[-28px] max-sm:start-3 max-sm:bottom-5"
                >
                    <div class="label-mono">{{ t({ fa: 'استک اصلی', en: 'Primary stack' }) }}</div>
                    <ul class="mt-2.5 flex flex-wrap gap-1.5">
                        <li
                            v-for="item in stack"
                            :key="item"
                            dir="ltr"
                            class="inline-flex min-h-7 items-center rounded-[0.625rem] border border-border bg-background px-2.5 font-mono text-xs text-muted-foreground"
                        >
                            {{ item }}
                        </li>
                    </ul>
                </div>

                <div
                    v-if="years"
                    class="card absolute top-14 z-10 flex size-[6.75rem] flex-col items-center justify-center gap-1 rounded-full border-primary text-center shadow-float -end-[26px] max-sm:end-2 max-sm:top-6 max-sm:size-[5.75rem]"
                >
                    <span dir="ltr" class="font-display text-3xl leading-none text-primary">{{ n(years) }}+</span>
                    <span class="px-2.5 text-xs leading-tight text-muted-foreground">
                        {{ t({ fa: 'سال تجربه', en: 'years of experience' }) }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
