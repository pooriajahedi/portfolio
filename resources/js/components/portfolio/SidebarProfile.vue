<script setup>
import IconGlyph from '../IconGlyph.vue';

defineProps({
    profile: {
        type: Object,
        required: true,
    },
    avatarImage: {
        type: String,
        required: true,
    },
    currentStatus: {
        type: String,
        required: true,
    },
    resumeFileUrl: {
        type: String,
        default: '',
    },
    contactItems: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <aside class="sidebar glass-panel">
        <div class="avatar-wrap">
            <img :src="avatarImage" :alt="profile.name">
            <span class="status-dot" :class="`status-${currentStatus}`"></span>
        </div>

        <h1 class="profile-name">{{ profile.name }}</h1>
        <p class="profile-role">{{ profile.role }}</p>
        <p class="status-label">
            <strong>وضعیت فعلی</strong>
            <span>{{ profile.currentStatus?.label ?? '' }}</span>
        </p>

        <a
            v-if="resumeFileUrl"
            class="resume-download-btn"
            :href="resumeFileUrl"
            target="_blank"
            rel="noopener noreferrer"
            download>
            <IconGlyph icon="mdi:cloud-download-outline" alt="دانلود رزومه" :size="24" />
            <span>دانلود رزومه</span>
        </a>

        <div class="contact-list">
            <div v-for="item in contactItems" :key="`${item.label}-${item.display}`" class="contact-item glass-panel">
                <div class="contact-icon">
                    <IconGlyph :icon="item.icon" :alt="item.label" :size="30" />
                </div>
                <div class="contact-meta">
                    <small>{{ item.label }}</small>
                    <a
                        v-if="item.href"
                        :href="item.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        :title="item.value">
                        {{ item.display }}
                    </a>
                    <span v-else :title="item.value">{{ item.display }}</span>
                </div>
            </div>
        </div>
    </aside>
</template>
