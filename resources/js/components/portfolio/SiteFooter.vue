<script setup>
import { computed } from 'vue';
import IconGlyph from '../IconGlyph.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    handle: {
        type: String,
        default: 'pooria.jahedi',
    },
    socials: {
        type: Array,
        default: () => [],
    },
});

const { t } = useLang();

const year = computed(() => new Date().getFullYear());
</script>

<template>
    <footer class="border-t border-border">
        <div class="mx-auto flex max-w-4xl flex-col gap-4 px-5 py-9 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-muted-foreground">
                <span dir="ltr" class="font-mono">© {{ year }} {{ handle }}</span>
                — {{ t({ fa: 'تمامی حقوق محفوظ است', en: 'All rights reserved' }) }}
            </p>

            <nav id="contact-links" class="flex flex-wrap items-center gap-2">
                <a
                    v-for="social in props.socials"
                    :key="social.label"
                    :href="social.href"
                    :target="social.href.startsWith('http') ? '_blank' : undefined"
                    rel="noreferrer noopener"
                    class="flex items-center gap-2 rounded-md border border-border px-3.5 py-2 text-base text-muted-foreground transition-colors hover:border-primary hover:text-primary"
                >
                    <IconGlyph :icon="social.icon" :size="17" />
                    {{ social.label }}
                </a>
            </nav>
        </div>
    </footer>
</template>
