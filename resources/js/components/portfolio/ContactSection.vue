<script setup>
import SectionHeader from './SectionHeader.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

defineProps({
    contact: {
        type: Object,
        default: null,
    },
    // [{ key, label, value, href, icon }]
    links: {
        type: Array,
        default: () => [],
    },
});

const { t } = useLang();
</script>

<template>
    <section id="contact" v-reveal="'contact'" class="mx-auto w-full max-w-[1240px] px-5 pb-16 sm:px-10 sm:pb-28">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-primary px-6 py-10 text-primary-foreground sm:rounded-[2.25rem] sm:px-9 sm:py-14 lg:px-16 lg:py-[4.5rem]">
            <span
                aria-hidden="true"
                class="pointer-events-none absolute -end-[7.5rem] -top-[7.5rem] size-[22.5rem] rounded-full border border-primary-foreground/20"
            ></span>
            <span
                aria-hidden="true"
                class="pointer-events-none absolute -end-10 -top-10 size-[12.5rem] rounded-full border border-primary-foreground/20"
            ></span>

            <div class="relative">
                <SectionHeader index="05" :label="t({ fa: 'تماس با من', en: 'Get in touch' })" inverted />

                <h2
                    v-if="contact && t(contact.headline)"
                    class="font-display mt-8 max-w-3xl text-[clamp(2rem,5.2vw,4.25rem)]"
                >
                    {{ t(contact.headline) }}
                </h2>

                <p v-if="contact && t(contact.text)" class="mt-6 max-w-2xl text-lg opacity-90 sm:text-xl">
                    {{ t(contact.text) }}
                </p>

                <ul class="mt-12 grid grid-cols-[repeat(auto-fit,minmax(min(100%,26rem),1fr))] gap-3.5">
                    <li v-for="link in links" :key="link.key">
                        <a
                            :href="link.href"
                            :target="link.href.startsWith('http') ? '_blank' : undefined"
                            rel="noreferrer noopener"
                            class="flex items-center gap-4 rounded-[1.25rem] border border-primary-foreground/25 bg-primary-foreground/10 px-5 py-4 transition-[transform,background-color] duration-200 hover:-translate-y-0.5 hover:bg-primary-foreground/20"
                        >
                            <span class="grid size-[2.875rem] shrink-0 place-items-center rounded-[0.875rem] bg-primary-foreground/15">
                                <UiIcon :name="link.icon" :size="22" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="label-mono block !text-inherit opacity-80">{{ link.label }}</span>
                                <span dir="ltr" class="block break-words text-start font-mono text-[0.9375rem] font-medium">
                                    {{ link.value }}
                                </span>
                            </span>
                            <UiIcon name="arrowUpRight" :size="18" :stroke-width="2" class="shrink-0 rtl:-scale-x-100" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
