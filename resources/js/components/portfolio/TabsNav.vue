<script setup>
const props = defineProps({
    activeTab: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['change']);

const tabs = [
    { key: 'about', label: 'درباره من' },
    { key: 'resume', label: 'رزومه' },
    { key: 'portfolio', label: 'نمونه کارها' },
    { key: 'blog', label: 'وبلاگ' },
    { key: 'contact', label: 'تماس با من' },
];
</script>

<template>
    <nav class="tabs" id="tabs">
        <a
            v-for="tab in tabs"
            :key="tab.key"
            :href="`#${tab.key}`"
            :class="{ active: activeTab === tab.key }"
            @click.prevent="emit('change', tab.key)">
            {{ tab.label }}
        </a>
    </nav>
</template>
