<script setup>
import { computed } from 'vue';
import IconGlyph from '../IconGlyph.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';
import { resolveStorageUrl, withVersion } from '../../support/urls';

const props = defineProps({
    profile: {
        type: Object,
        default: () => ({}),
    },
    socials: {
        type: Array,
        default: () => [],
    },
});

const { t } = useLang();

const avatarUrl = computed(() => resolveStorageUrl(props.profile?.avatarImage) || '/images/hero/pooria-hero.jpeg');

const resumeUrl = computed(() =>
    withVersion(resolveStorageUrl(props.profile?.resumeFile), props.profile?.resumeFileVersion),
);

const avatarAlt = computed(() => t(props.profile?.name ?? { fa: 'پوریا جاهدی', en: 'Pooria Jahedi' }));
</script>

<template>
    <section class="py-16 sm:py-24">
        <div class="flex flex-col-reverse items-center gap-10 sm:flex-row sm:items-center sm:gap-12">
            <div class="w-full sm:flex-1">
                <span
                    v-if="profile?.status"
                    class="inline-flex items-center gap-2.5 rounded-full border border-primary/35 bg-primary/10 px-4 py-2 text-sm font-medium text-primary shadow-[0_0_24px_-8px_var(--color-primary)] backdrop-blur"
                >
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-primary opacity-70"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-primary"></span>
                    </span>
                    {{ t(profile.status) }}
                </span>

                <h1
                    class="mt-6 text-4xl font-bold tracking-tight text-foreground drop-shadow-[0_2px_24px_color-mix(in_oklab,var(--color-primary)_30%,transparent)] sm:text-6xl"
                >
                    {{ t(profile?.name) }}
                </h1>

                <p dir="auto" class="mt-4 text-lg font-medium text-primary sm:text-xl">{{ t(profile?.role) }}</p>

                <p v-if="t(profile?.tagline)" dir="auto" class="mt-5 max-w-xl text-lg leading-relaxed text-muted-foreground">
                    {{ t(profile?.tagline) }}
                </p>
            </div>

            <div class="relative shrink-0">
                <div aria-hidden="true" class="absolute -inset-3 rounded-full bg-primary/10 blur-2xl"></div>
                <div
                    class="relative size-40 overflow-hidden rounded-full border border-primary/25 bg-secondary shadow-[0_0_35px_-20px_var(--color-primary)] sm:size-52"
                >
                    <img :src="avatarUrl" :alt="avatarAlt" width="512" height="512" class="size-full object-cover" />
                </div>
            </div>
        </div>

        <div class="mt-9 flex flex-wrap items-center gap-3">
            <a
                href="#projects"
                class="rounded-md bg-primary px-5 py-2.5 text-base font-medium text-primary-foreground transition-opacity hover:opacity-90"
            >
                {{ t({ fa: 'دیدن پروژه‌ها', en: 'View projects' }) }}
            </a>

            <a
                v-if="resumeUrl"
                :href="resumeUrl"
                download
                class="flex items-center gap-2 rounded-md border border-border-strong px-5 py-2.5 text-base font-medium text-foreground transition-colors hover:border-primary hover:text-primary"
            >
                <UiIcon name="download" :size="18" />
                {{ t({ fa: 'رزومه', en: 'Resume' }) }}
            </a>

            <a
                v-for="social in socials"
                :key="social.label"
                :href="social.href"
                :aria-label="social.label"
                :target="social.href.startsWith('http') ? '_blank' : undefined"
                rel="noreferrer noopener"
                class="rounded-md border border-border-strong p-3 text-muted-foreground transition-colors hover:border-primary hover:text-primary"
            >
                <IconGlyph :icon="social.icon" :size="20" />
            </a>
        </div>
    </section>
</template>
