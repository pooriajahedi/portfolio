<script setup>
import { ref } from 'vue';

const props = defineProps({
    activeTab: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['change']);
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
            class="tabs-mobile-toggle"
            type="button"
            aria-label="باز کردن منو"
            :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
            @click="mobileMenuOpen = true">
            <span></span>
            <span></span>
            <span></span>
        </button>

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
    </div>
</template>
