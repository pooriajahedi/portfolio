<script setup>
import { computed } from 'vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    handle: {
        type: String,
        default: 'pooria.jahedi',
    },
    email: {
        type: String,
        default: '',
    },
    nav: {
        type: Array,
        default: () => [],
    },
});

const { lang, t, toggle } = useLang();

const contactHref = computed(() => (props.email !== '' ? `mailto:${props.email}` : '#contact-links'));
const toggleLabel = computed(() => (lang.value === 'fa' ? 'Switch to English' : 'تغییر به فارسی'));
</script>

<template>
    <header class="sticky top-0 z-20 border-b border-border bg-background/70 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-5">
            <a href="#top" dir="ltr" class="font-mono text-base text-foreground">
                {{ handle }}<span class="text-primary">.dev</span>
            </a>

            <nav class="flex items-center gap-5">
                <a
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="hidden text-base text-muted-foreground transition-colors hover:text-foreground sm:inline"
                >
                    {{ t(item.label) }}
                </a>

                <button
                    type="button"
                    class="inline-flex h-10 items-center gap-1.5 rounded-md border border-border-strong px-3.5 text-sm leading-none text-foreground transition-colors hover:border-primary hover:text-primary"
                    :aria-label="toggleLabel"
                    @click="toggle"
                >
                    <UiIcon name="languages" :size="17" />
                    <span class="font-mono text-sm">{{ lang === 'fa' ? 'EN' : 'FA' }}</span>
                </button>

                <a
                    :href="contactHref"
                    class="inline-flex h-10 items-center rounded-md border border-border-strong px-3.5 text-sm leading-none text-foreground transition-colors hover:border-primary hover:text-primary"
                >
                    {{ t({ fa: 'تماس', en: 'Contact' }) }}
                </a>
            </nav>
        </div>
    </header>
</template>
