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
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <aside class="sidebar glass-panel">
        <template v-if="loading">
            <div class="avatar-wrap skeleton-circle"></div>
            <div class="profile-name skeleton-line skeleton-w-70"></div>
            <div class="profile-role skeleton-line skeleton-w-55"></div>
            <div class="status-label">
                <span class="skeleton-line skeleton-w-40"></span>
                <span class="skeleton-line skeleton-w-60"></span>
            </div>

            <div class="resume-download-btn skeleton-line skeleton-w-100 skeleton-h-56"></div>

            <div class="contact-list">
                <div v-for="index in 4" :key="`sidebar-skeleton-${index}`" class="contact-item glass-panel">
                    <div class="contact-icon skeleton-circle skeleton-icon-30"></div>
                    <div class="contact-meta">
                        <span class="skeleton-line skeleton-w-30"></span>
                        <span class="skeleton-line skeleton-w-70"></span>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
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
        </template>
    </aside>
</template>
