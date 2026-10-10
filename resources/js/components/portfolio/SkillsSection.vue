<script setup>
import IconGlyph from '../IconGlyph.vue';
import SectionHeader from './SectionHeader.vue';
import { useLang } from '../../composables/useLang';
import { brandColor, isOutlined } from '../../support/skillIcons';

defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
});

const { t, n } = useLang();

const tileStyle = (icon) => {
    const color = brandColor(icon);

    return color ? { '--bc': color } : undefined;
};
</script>

<template>
    <section id="skills" v-reveal="'skills'" class="mx-auto w-full max-w-[1240px] px-5 py-16 sm:px-10 sm:py-28">
        <SectionHeader index="02" :label="t({ fa: 'مهارت‌ها', en: 'Skills' })" />
        <h2 class="heading mt-9 max-w-3xl text-[clamp(1.75rem,3.6vw,3rem)]">
            {{ t({ fa: 'مهارت‌ها و تکنولوژی‌ها', en: 'Skills & technologies' }) }}
        </h2>

        <!-- Two balanced columns, so a group with few skills does not leave an empty card. -->
        <div class="mt-12 gap-6 min-[900px]:columns-2">
            <article v-for="group in groups" :key="group.key" class="card mb-6 break-inside-avoid p-5 sm:p-7">
                <div class="flex items-center justify-between gap-3 border-b border-border pb-5">
                    <h3 class="heading text-[1.375rem]">{{ t(group.title) }}</h3>
                    <span class="label-mono" dir="ltr">{{ n(group.items.length).padStart(2, n(0)) }}</span>
                </div>

                <ul class="mt-5 grid grid-cols-[repeat(auto-fill,minmax(5.25rem,1fr))] gap-3 sm:grid-cols-[repeat(auto-fill,minmax(6.25rem,1fr))]">
                    <li
                        v-for="skill in group.items"
                        :key="skill.title.fa + skill.icon"
                        class="skill-tile"
                        :style="tileStyle(skill.icon)"
                    >
                        <IconGlyph :icon="skill.icon" :size="42" :class-name="isOutlined(skill.icon) ? 'icon-outline' : ''" />
                        <span dir="auto" class="text-center text-[0.8125rem] font-medium leading-snug text-muted-foreground">
                            {{ t(skill.title) }}
                        </span>
                    </li>
                </ul>
            </article>
        </div>
    </section>
</template>

<style scoped>
/* Each tile is lit from the top by its own brand colour. */
.skill-tile {
    --bc: var(--accent);

    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.875rem;
    padding: 1.25rem 0.5rem 0.875rem;
    border: 1px solid color-mix(in srgb, var(--bc) 26%, var(--line));
    border-radius: 1.125rem;
    background:
        radial-gradient(120% 100% at 50% 0%, color-mix(in srgb, var(--bc) var(--brand-tint), transparent), transparent 72%),
        var(--bg);
    transition:
        transform 200ms ease,
        border-color 200ms ease;
}

.skill-tile:hover {
    transform: translateY(-3px);
    border-color: var(--bc);
}
</style>
