<script setup>
import { computed } from 'vue';
import SectionHeader from './SectionHeader.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    resumeUrl: {
        type: String,
        default: '',
    },
});

const { lang, t, n } = useLang();

/** "۱۳۹۴ – اکنون" / "2015 – Present", from the Jalali + Gregorian years the API sends. */
const formatPeriod = (period) => {
    const start = period?.start?.[lang.value];
    if (!start) return '';

    const end = period.current
        ? t({ fa: 'اکنون', en: 'Present' })
        : period.end?.[lang.value]
          ? n(period.end[lang.value])
          : '';

    return end ? `${n(start)} – ${end}` : n(start);
};

const timeline = computed(() =>
    props.items.map((item, index) => ({
        ...item,
        periodLabel: formatPeriod(item.period),
        isLast: index === props.items.length - 1,
    })),
);
</script>

<template>
    <section id="experience" v-reveal="'experience'" class="mx-auto w-full max-w-[1240px] px-5 py-16 sm:px-10 sm:py-28">
        <SectionHeader index="03" :label="t({ fa: 'مسیر حرفه‌ای', en: 'Experience' })" />

        <div class="mt-9 flex flex-wrap items-start gap-x-[4.5rem] gap-y-12">
            <div class="min-w-0 flex-[1_1_18rem] lg:max-w-[25rem]">
                <h2 class="heading text-[clamp(1.75rem,3.6vw,3rem)]">{{ t({ fa: 'مسیر حرفه‌ای', en: 'Experience' }) }}</h2>

                <template v-if="resumeUrl">
                    <p class="mt-5 text-muted-foreground">
                        {{
                            t({
                                fa: 'یک فایل PDF شامل سوابق کامل کاری، تکنولوژی‌ها و راه‌های ارتباطی.',
                                en: 'A PDF with my full work history, stack and contact details.',
                            })
                        }}
                    </p>

                    <a :href="resumeUrl" download class="btn btn-primary mt-7">
                        <UiIcon name="download" :size="20" :stroke-width="2" />
                        {{ t({ fa: 'دانلود رزومه (PDF)', en: 'Download resume (PDF)' }) }}
                    </a>
                </template>
            </div>

            <ol class="min-w-0 flex-[2_1_34rem]">
                <li
                    v-for="item in timeline"
                    :key="item.id"
                    class="grid grid-cols-[1.25rem_minmax(0,1fr)] gap-x-3.5 sm:grid-cols-[9.5rem_1.75rem_minmax(0,1fr)] sm:gap-x-5"
                >
                    <div class="col-start-2 row-start-1 pb-2.5 sm:col-start-1 sm:row-start-1 sm:pb-0 sm:pt-[1.375rem]">
                        <div dir="auto" class="label-mono !text-foreground font-semibold">{{ item.periodLabel }}</div>
                        <span
                            v-if="item.period.current"
                            class="mt-2 inline-flex items-center gap-2 text-[0.8125rem] font-semibold text-success"
                        >
                            <span class="size-[7px] rounded-full bg-success"></span>
                            {{ t({ fa: 'در جریان', en: 'Ongoing' }) }}
                        </span>
                    </div>

                    <div aria-hidden="true" class="relative col-start-1 row-span-2 row-start-1 sm:col-start-2 sm:row-span-1">
                        <span
                            class="absolute start-1/2 top-0 w-px bg-border-strong"
                            :class="item.isLast ? 'h-9' : 'bottom-0'"
                        ></span>
                        <span
                            class="absolute start-1/2 top-1.5 size-3.5 -translate-x-1/2 rounded-full border-2 sm:top-[1.625rem] rtl:translate-x-1/2"
                            :class="item.period.current ? 'border-primary bg-primary' : 'border-border-strong bg-background'"
                        ></span>
                    </div>

                    <div
                        class="card col-start-2 row-start-2 mb-5 p-5 transition-[transform,border-color] duration-200 hover:-translate-y-1 hover:border-primary sm:col-start-3 sm:row-start-1 sm:px-7 sm:py-6"
                    >
                        <h3 class="heading text-xl">{{ t(item.title) }}</h3>
                        <p class="mt-2 whitespace-pre-line text-base text-muted-foreground">{{ t(item.text) }}</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>
</template>
