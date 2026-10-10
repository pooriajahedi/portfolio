<script setup>
import SectionHeader from './SectionHeader.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    about: {
        type: Object,
        required: true,
    },
});

const { t, n } = useLang();

/** 1 -> "01" / "۰۱" */
const order = (position) => n(position).padStart(2, n(0));
</script>

<template>
    <section id="about" v-reveal="'about'" class="mx-auto w-full max-w-[1240px] px-5 py-16 sm:px-10 sm:py-28">
        <SectionHeader index="01" :label="t({ fa: 'درباره من', en: 'About me' })" />

        <div class="mt-9 flex flex-wrap items-start gap-x-20 gap-y-10">
            <h2 v-if="t(about.headline)" class="heading min-w-0 flex-[1_1_28rem] text-[clamp(1.75rem,3.6vw,3rem)]">
                {{ t(about.headline) }}
            </h2>

            <div class="flex min-w-0 flex-[1_1_24rem] flex-col gap-5">
                <p v-for="(paragraph, index) in about.paragraphs" :key="index" class="whitespace-pre-line text-lg text-muted-foreground">
                    {{ t(paragraph) }}
                </p>
            </div>
        </div>

        <ul v-if="about.services?.length" class="mt-16 grid grid-cols-[repeat(auto-fit,minmax(min(100%,15.5rem),1fr))] gap-5">
            <li
                v-for="(service, index) in about.services"
                :key="index"
                class="card flex flex-col gap-3.5 p-7 transition-[transform,border-color] duration-200 hover:-translate-y-1 hover:border-primary"
            >
                <span dir="ltr" class="label-mono !text-primary font-semibold">{{ order(index + 1) }}</span>
                <h3 class="heading text-xl">{{ t(service.title) }}</h3>
                <p class="text-base text-muted-foreground">{{ t(service.text) }}</p>
            </li>
        </ul>
    </section>
</template>
