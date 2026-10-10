<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import LangSwitch from './LangSwitch.vue';
import ThemeToggle from './ThemeToggle.vue';
import UiIcon from './UiIcon.vue';
import { useLang } from '../../composables/useLang';

const props = defineProps({
    handle: {
        type: String,
        default: 'pooria.jahedi',
    },
    nav: {
        type: Array,
        default: () => [],
    },
    showContact: {
        type: Boolean,
        default: true,
    },
});

const { t } = useLang();

const menuOpen = ref(false);

// "pooria.jahedi" -> "PJ"
const initials = computed(() =>
    props.handle
        .split(/[.\-_\s]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join(''),
);

const menuItems = computed(() => [
    ...props.nav,
    ...(props.showContact ? [{ href: '#contact', label: { fa: 'تماس', en: 'Contact' } }] : []),
]);

const closeMenu = () => {
    menuOpen.value = false;
};

const onKeydown = (event) => {
    if (event.key === 'Escape') closeMenu();
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <header class="relative z-30 mx-auto w-full max-w-[1240px] px-5 pt-5 sm:px-10">
        <div class="flex items-center justify-between gap-3 rounded-full border border-border bg-card px-3.5 py-2.5">
            <a href="#top" class="flex items-center gap-3" :aria-label="handle">
                <span
                    class="grid size-10 shrink-0 place-items-center rounded-[0.875rem] bg-primary font-mono text-[0.9375rem] font-semibold text-primary-foreground"
                >
                    {{ initials }}
                </span>
                <span dir="ltr" class="hidden font-mono text-[0.9375rem] font-medium sm:inline">
                    {{ handle }}<span class="text-primary">.dev</span>
                </span>
            </a>

            <nav class="hidden items-center gap-0.5 min-[900px]:flex" :aria-label="t({ fa: 'ناوبری اصلی', en: 'Main navigation' })">
                <a
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="rounded-full px-4 py-2 text-base font-medium text-muted-foreground transition-colors hover:bg-background-alt hover:text-foreground"
                >
                    {{ t(item.label) }}
                </a>
            </nav>

            <div class="flex items-center gap-2">
                <LangSwitch />
                <ThemeToggle />

                <a
                    v-if="showContact"
                    href="#contact"
                    class="hidden min-h-11 items-center rounded-full border border-border-strong px-5 text-[0.9375rem] font-semibold transition-colors hover:border-primary hover:text-primary min-[900px]:inline-flex"
                >
                    {{ t({ fa: 'تماس', en: 'Contact' }) }}
                </a>

                <button
                    type="button"
                    class="inline-grid size-11 place-items-center rounded-full border border-border transition-colors hover:border-primary hover:text-primary min-[900px]:hidden"
                    :aria-label="t({ fa: 'منو', en: 'Menu' })"
                    :aria-expanded="menuOpen"
                    aria-controls="mobile-menu"
                    @click="menuOpen = !menuOpen"
                >
                    <UiIcon :name="menuOpen ? 'close' : 'menu'" :size="20" />
                </button>
            </div>
        </div>

        <nav
            v-if="menuOpen"
            id="mobile-menu"
            class="card mt-2.5 flex flex-col p-2.5 min-[900px]:hidden"
            :aria-label="t({ fa: 'ناوبری موبایل', en: 'Mobile navigation' })"
        >
            <a
                v-for="item in menuItems"
                :key="item.href"
                :href="item.href"
                class="flex min-h-12 items-center rounded-xl px-4 font-medium transition-colors hover:bg-background-alt"
                @click="closeMenu"
            >
                {{ t(item.label) }}
            </a>
        </nav>
    </header>
</template>
