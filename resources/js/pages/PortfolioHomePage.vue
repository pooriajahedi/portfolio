<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watchEffect } from 'vue';
import AboutSection from '../components/portfolio/AboutSection.vue';
import CodeStreamBackground from '../components/portfolio/CodeStreamBackground.vue';
import ContactSection from '../components/portfolio/ContactSection.vue';
import ExperienceSection from '../components/portfolio/ExperienceSection.vue';
import HeroSection from '../components/portfolio/HeroSection.vue';
import ImageModal from '../components/portfolio/ImageModal.vue';
import ProjectDetail from '../components/portfolio/ProjectDetail.vue';
import ProjectsSection from '../components/portfolio/ProjectsSection.vue';
import SiteFooter from '../components/portfolio/SiteFooter.vue';
import SiteHeader from '../components/portfolio/SiteHeader.vue';
import SkillsSection from '../components/portfolio/SkillsSection.vue';
import TechMarquee from '../components/portfolio/TechMarquee.vue';
import { useLang } from '../composables/useLang';
import { useSmoothScroll } from '../composables/useSmoothScroll';
import { usePublicContentApi } from '../composables/usePublicContentApi';
import { compactUrl, normalizeExternalUrl, resolveStorageUrl, telegramUrl, withVersion } from '../support/urls';

const props = defineProps({
    apiUrls: {
        type: Object,
        required: true,
    },
});

const PRIMARY_STACK_GROUPS = ['backend', 'database'];
const PRIMARY_STACK_SIZE = 5;

const { t } = useLang();
const { scrollToElement } = useSmoothScroll();
const { fetchSiteState, fetchPortfolioFeed, fetchPortfolioProject } = usePublicContentApi(props.apiUrls);

const siteState = ref({});
const feed = ref({ title: null, categories: [], projects: [] });
const loadingSite = ref(true);
const loadingProjects = ref(true);

const selectedSlug = ref('');
const selectedProject = ref(null);
const loadingDetail = ref(false);
const detailError = ref('');
let detailRequestToken = 0;

const modalOpen = ref(false);
const modalImages = ref([]);
const modalIndex = ref(0);

const profile = computed(() => siteState.value.profile ?? {});
const about = computed(() => siteState.value.about ?? null);
const contact = computed(() => siteState.value.contact ?? null);
const skillGroups = computed(() => siteState.value.skillGroups ?? []);
const experience = computed(() => siteState.value.experience ?? []);
const links = computed(() => siteState.value.links ?? {});
const projects = computed(() => feed.value.projects ?? []);

const handle = computed(() => profile.value.handle || 'pooria.jahedi');
const resumeUrl = computed(() => withVersion(resolveStorageUrl(profile.value?.resumeFile), profile.value?.resumeFileVersion));

const socialLinks = computed(() =>
    [
        { key: 'github', label: 'GitHub', value: compactUrl(links.value.github), href: normalizeExternalUrl(links.value.github), icon: 'github' },
        { key: 'linkedin', label: 'LinkedIn', value: compactUrl(links.value.linkedin), href: normalizeExternalUrl(links.value.linkedin), icon: 'linkedin' },
        { key: 'telegram', label: 'Telegram', value: String(links.value.telegram ?? '').trim(), href: telegramUrl(links.value.telegram), icon: 'send' },
    ].filter((item) => item.href !== ''),
);

const contactLinks = computed(() => {
    const email = String(links.value.email ?? '').trim();
    const emailLink = email
        ? [{ key: 'email', label: t({ fa: 'ایمیل', en: 'Email' }), value: email, href: `mailto:${email}`, icon: 'mail' }]
        : [];

    return [...emailLink, ...socialLinks.value];
});

const allSkills = computed(() => skillGroups.value.flatMap((group) => group.items.map((skill) => t(skill.title))));

// The headline technologies on the portrait card come from the backend and database groups.
const primaryStack = computed(() =>
    PRIMARY_STACK_GROUPS.flatMap((key) => skillGroups.value.find((group) => group.key === key)?.items ?? [])
        .slice(0, PRIMARY_STACK_SIZE)
        .map((skill) => t(skill.title)),
);

// Whole years since the earliest experience entry started.
const yearsOfExperience = computed(() => {
    const starts = experience.value.map((item) => item.period?.start?.en).filter(Boolean);
    if (starts.length === 0) return null;

    const years = new Date().getFullYear() - Math.min(...starts);

    return years > 0 ? years : null;
});

// The tab title and description follow the language: "name | role", or the
// project's own title on its page.
const pageTitle = computed(() => {
    const site = [t(profile.value.name), t(profile.value.role)].filter(Boolean).join(' | ');
    const project = selectedProject.value ? t(selectedProject.value.title) : '';

    return project ? [project, t(profile.value.name)].filter(Boolean).join(' | ') : site;
});

watchEffect(() => {
    if (pageTitle.value) document.title = pageTitle.value;

    const tagline = t(profile.value.tagline);
    if (tagline) document.querySelector('meta[name="description"]')?.setAttribute('content', tagline);
});

const nav = computed(() =>
    [
        { show: about.value !== null, label: { fa: 'درباره من', en: 'About' }, href: '#about' },
        { show: skillGroups.value.length > 0, label: { fa: 'مهارت‌ها', en: 'Skills' }, href: '#skills' },
        { show: experience.value.length > 0 || resumeUrl.value !== '', label: { fa: 'مسیر حرفه‌ای', en: 'Experience' }, href: '#experience' },
        { show: projects.value.length > 0, label: { fa: 'نمونه‌کارها', en: 'Projects' }, href: '#projects' },
    ].filter((item) => item.show),
);

const isDetailView = computed(() => selectedSlug.value !== '');

const nextProject = computed(() => {
    const index = projects.value.findIndex((project) => project.slug === selectedSlug.value);
    if (index === -1 || projects.value.length < 2) return null;

    return projects.value[(index + 1) % projects.value.length];
});

const slugFromPath = () => {
    const match = window.location.pathname.match(/^\/portfolio\/([^/]+)\/?$/i);
    return match?.[1] ? decodeURIComponent(match[1]) : '';
};

const loadProjectDetail = async (slug) => {
    const token = ++detailRequestToken;

    selectedSlug.value = slug;
    selectedProject.value = null;
    detailError.value = '';
    loadingDetail.value = true;

    try {
        const payload = await fetchPortfolioProject(slug);
        if (token !== detailRequestToken) return;
        selectedProject.value = payload?.project ?? null;
    } catch {
        if (token !== detailRequestToken) return;
        detailError.value = t({
            fa: 'پروژه موردنظر پیدا نشد یا خطایی در دریافت اطلاعات رخ داد.',
            en: 'This project could not be found, or something went wrong while loading it.',
        });
    } finally {
        if (token === detailRequestToken) loadingDetail.value = false;
    }
};

const openProject = (slug) => {
    if (!slug || slug === selectedSlug.value) return;

    window.history.pushState({}, '', `/portfolio/${encodeURIComponent(slug)}`);
    loadProjectDetail(slug);
    nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
};

const closeProject = ({ pushState = true } = {}) => {
    detailRequestToken += 1;
    selectedSlug.value = '';
    selectedProject.value = null;
    detailError.value = '';
    loadingDetail.value = false;

    if (pushState && window.location.pathname !== '/') {
        window.history.pushState({}, '', '/');
    }

    nextTick(() => scrollToElement(document.getElementById('projects')));
};

const openPreview = ({ images, index }) => {
    modalImages.value = images ?? [];
    modalIndex.value = index ?? 0;
    modalOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closePreview = () => {
    modalOpen.value = false;
    modalImages.value = [];
    modalIndex.value = 0;
    document.body.style.overflow = '';
};

const onPopState = () => {
    const slug = slugFromPath();

    if (slug) {
        loadProjectDetail(slug);
        return;
    }

    closeProject({ pushState: false });
};

onMounted(async () => {
    window.addEventListener('popstate', onPopState);

    const initialSlug = slugFromPath();
    if (initialSlug) loadProjectDetail(initialSlug);

    await Promise.allSettled([
        fetchSiteState()
            .then((payload) => {
                siteState.value = payload ?? {};
            })
            .finally(() => {
                loadingSite.value = false;
            }),
        fetchPortfolioFeed()
            .then((payload) => {
                feed.value = { title: payload?.title ?? null, categories: payload?.categories ?? [], projects: payload?.projects ?? [] };
            })
            .finally(() => {
                loadingProjects.value = false;
            }),
    ]);
});

onBeforeUnmount(() => {
    window.removeEventListener('popstate', onPopState);
    document.body.style.overflow = '';
});
</script>

<template>
    <div id="top" class="relative isolate min-h-screen">
        <CodeStreamBackground />

        <SiteHeader :handle="handle" :nav="nav" :show-contact="contactLinks.length > 0" />

        <main class="relative">
            <ProjectDetail
                v-if="isDetailView"
                :project="selectedProject"
                :next="nextProject"
                :loading="loadingDetail"
                :error="detailError"
                @back="closeProject()"
                @open="openProject"
                @preview="openPreview"
            />

            <div v-else class="transition-opacity duration-500" :class="loadingSite ? 'opacity-0' : 'opacity-100'">
                <HeroSection
                    :profile="profile"
                    :socials="socialLinks"
                    :stack="primaryStack"
                    :years="yearsOfExperience"
                    :resume-url="resumeUrl"
                />

                <TechMarquee :items="allSkills" />

                <AboutSection v-if="about" :about="about" />

                <SkillsSection v-if="skillGroups.length" :groups="skillGroups" />

                <ExperienceSection v-if="experience.length || resumeUrl" :items="experience" :resume-url="resumeUrl" />

                <ProjectsSection
                    v-if="!loadingProjects && projects.length"
                    :projects="projects"
                    :categories="feed.categories"
                    :title="feed.title"
                    @open="openProject"
                />

                <ContactSection v-if="contactLinks.length" :contact="contact" :links="contactLinks" />
            </div>
        </main>

        <SiteFooter :handle="handle" />

        <ImageModal
            :open="modalOpen"
            :images="modalImages"
            :start-index="modalIndex"
            :alt="selectedProject ? t(selectedProject.title) : ''"
            @close="closePreview"
        />
    </div>
</template>
