<script setup>
import IconGlyph from '../IconGlyph.vue';
import SectionHeader from './SectionHeader.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
});

const { t, n } = useLang();
</script>

<template>
    <section id="skills" v-reveal="'skills'" class="mx-auto w-full max-w-[1240px] px-5 py-16 sm:px-10 sm:py-28">
        <SectionHeader index="02" :label="t({ fa: 'مهارت‌ها', en: 'Skills' })" />
        <h2 class="heading mt-9 max-w-3xl text-[clamp(1.75rem,3.6vw,3rem)]">
            {{ t({ fa: 'مهارت‌ها و تکنولوژی‌ها', en: 'Skills & technologies' }) }}
        </h2>

        <div class="mt-12 grid grid-cols-[repeat(auto-fit,minmax(min(100%,30rem),1fr))] gap-6">
            <article v-for="group in groups" :key="group.key" class="card p-7">
                <div class="flex items-center justify-between gap-3 border-b border-border pb-5">
                    <h3 class="heading text-[1.375rem]">{{ t(group.title) }}</h3>
                    <span class="label-mono" dir="ltr">{{ n(group.items.length).padStart(2, n(0)) }}</span>
                </div>

                <ul class="mt-5 flex flex-wrap gap-2.5">
                    <li
                        v-for="skill in group.items"
                        :key="skill.title.fa + skill.icon"
                        dir="ltr"
                        class="inline-flex min-h-12 items-center gap-2.5 rounded-[0.875rem] border border-border bg-background py-1.5 pe-4 ps-1.5 text-[0.9375rem] font-medium transition-colors hover:border-primary"
                    >
                        <span class="grid size-9 shrink-0 place-items-center rounded-[0.625rem] border border-border bg-white">
                            <IconGlyph :icon="skill.icon" :size="22" />
                        </span>
                        {{ t(skill.title) }}
                    </li>
                </ul>
            </article>
        </div>
    </section>
</template>
