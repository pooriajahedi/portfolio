<script setup>
import { ref } from 'vue';
import IconGlyph from '../IconGlyph.vue';

const props = defineProps({
    activeTab: {
        type: String,
        required: true,
    },
    themeMode: {
        type: String,
        default: 'dark',
    },
});

const emit = defineEmits(['change', 'toggle-theme']);
const mobileMenuOpen = ref(false);

const tabs = [
    { key: 'about', label: 'درباره من' },
    { key: 'resume', label: 'رزومه' },
    { key: 'portfolio', label: 'نمونه کارها' },
    { key: 'blog', label: 'وبلاگ' },
    { key: 'contact', label: 'تماس با من' },
];

const changeTab = (tabKey) => {
    emit('change', tabKey);
    mobileMenuOpen.value = false;
};

const toggleTheme = () => {
    emit('toggle-theme');
};
</script>

<template>
    <div class="tabs-wrap" id="tabs">
        <nav class="tabs">
            <a
                v-for="tab in tabs"
                :key="tab.key"
                :href="`#${tab.key}`"
                :class="{ active: activeTab === tab.key }"
                @click.prevent="changeTab(tab.key)">
                {{ tab.label }}
            </a>
        </nav>

        <button
            class="theme-toggle"
            type="button"
            :aria-label="themeMode === 'light' ? 'فعال‌سازی تم تیره' : 'فعال‌سازی تم روشن'"
            @click="toggleTheme">
            <span class="theme-toggle-track" :class="{ 'is-light': themeMode === 'light' }">
                <span class="theme-toggle-thumb">
                    <span class="theme-toggle-icon" aria-hidden="true">
                        <IconGlyph :icon="themeMode === 'light' ? 'mdi:white-balance-sunny' : 'mdi:moon-waning-crescent'" :size="14" />
                    </span>
                </span>
            </span>
        </button>

        <button
            class="tabs-mobile-toggle"
            type="button"
            aria-label="باز کردن منو"
            :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
            @click="mobileMenuOpen = true">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <teleport to="body">
            <div
                class="tabs-mobile-backdrop"
                :class="{ 'is-open': mobileMenuOpen }"
                @click="mobileMenuOpen = false"></div>

            <aside class="tabs-mobile-drawer" :class="{ 'is-open': mobileMenuOpen }" aria-label="منوی ناوبری">
                <div class="tabs-mobile-head">
                    <strong>منو</strong>
                    <button type="button" aria-label="بستن منو" @click="mobileMenuOpen = false">✕</button>
                </div>
                <nav class="tabs-mobile-links">
                    <a
                        v-for="tab in tabs"
                        :key="`mobile-${tab.key}`"
                        :href="`#${tab.key}`"
                        :class="{ active: activeTab === tab.key }"
                        @click.prevent="changeTab(tab.key)">
                        {{ tab.label }}
                    </a>
                </nav>
            </aside>
        </teleport>
    </div>
</template>
