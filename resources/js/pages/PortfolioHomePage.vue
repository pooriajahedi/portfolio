<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import StarfieldBackground from '../components/portfolio/StarfieldBackground.vue';
import SiteHeader from '../components/portfolio/SiteHeader.vue';
import SiteFooter from '../components/portfolio/SiteFooter.vue';
import HeroSection from '../components/portfolio/HeroSection.vue';
import PortfolioSection from '../components/portfolio/PortfolioSection.vue';
import SkillsSection from '../components/portfolio/SkillsSection.vue';
import ProjectsSection from '../components/portfolio/ProjectsSection.vue';
import ResumeSection from '../components/portfolio/ResumeSection.vue';
import ProjectDetail from '../components/portfolio/ProjectDetail.vue';
import ImageModal from '../components/portfolio/ImageModal.vue';
import { useLang } from '../composables/useLang';
import { useSmoothScroll } from '../composables/useSmoothScroll';
import { usePublicContentApi } from '../composables/usePublicContentApi';
import { normalizeExternalUrl, resolveStorageUrl, telegramUrl, withVersion } from '../support/urls';

const props = defineProps({
    apiUrls: {
        type: Object,
        required: true,
    },
});

const { t } = useLang();
const { scrollToElement } = useSmoothScroll();
const { fetchSiteState, fetchPortfolioFeed, fetchPortfolioProject } = usePublicContentApi(props.apiUrls);

const siteState = ref({});
const projects = ref([]);
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
const skillGroups = computed(() => siteState.value.skillGroups ?? []);
const links = computed(() => siteState.value.links ?? {});
const starfield = computed(() => siteState.value.appearance?.starfield ?? { enabled: true, opacity: 80 });

const resumeUrl = computed(() => withVersion(resolveStorageUrl(profile.value?.resumeFile), profile.value?.resumeFileVersion));

const socials = computed(() => {
    const items = [
        { label: 'GitHub', href: normalizeExternalUrl(links.value.github), icon: links.value.githubIcon || 'mdi:github' },
        { label: 'LinkedIn', href: normalizeExternalUrl(links.value.linkedin), icon: links.value.linkedinIcon || 'mdi:linkedin' },
        { label: 'Telegram', href: telegramUrl(links.value.telegram), icon: links.value.telegramIcon || 'mdi:telegram' },
        {
            label: t({ fa: 'ایمیل', en: 'Email' }),
            href: links.value.email ? `mailto:${links.value.email}` : '',
            icon: links.value.emailIcon || 'logos:google-gmail',
        },
    ];

    return items.filter((item) => item.href !== '');
});

const nav = computed(() => [
    { label: { fa: 'مهارت‌ها', en: 'Skills' }, href: '#skills' },
    { label: { fa: 'پروژه‌ها', en: 'Projects' }, href: '#projects' },
    { label: { fa: 'رزومه', en: 'Resume' }, href: '#resume' },
]);

const isDetailView = computed(() => selectedSlug.value !== '');

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
        if (token !== detailRequestToken) return;
        loadingDetail.value = false;
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
                projects.value = payload?.projects ?? [];
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
    <div class="relative min-h-screen">
        <StarfieldBackground :enabled="starfield.enabled !== false" :opacity="Number(starfield.opacity ?? 80)" />

        <SiteHeader :handle="profile.handle || 'pooria.jahedi'" :email="links.email || ''" :nav="nav" />

        <main id="top" class="relative">
            <div class="mx-auto max-w-4xl px-5">
                <ProjectDetail
                    v-if="isDetailView"
                    :project="selectedProject"
                    :loading="loadingDetail"
                    :error="detailError"
                    @back="closeProject()"
                    @preview="openPreview"
                />

                <div v-else class="transition-opacity duration-500" :class="loadingSite ? 'opacity-0' : 'opacity-100'">
                    <HeroSection :profile="profile" :socials="socials" />

                    <PortfolioSection
                        v-if="skillGroups.length"
                        id="skills"
                        index="01"
                        :title="t({ fa: 'مهارت‌ها و تکنولوژی‌ها', en: 'Skills & Technologies' })"
                    >
                        <SkillsSection :groups="skillGroups" />
                    </PortfolioSection>

                    <PortfolioSection
                        v-if="!loadingProjects && projects.length"
                        id="projects"
                        index="02"
                        :title="t({ fa: 'پروژه‌ها', en: 'Projects' })"
                    >
                        <ProjectsSection :projects="projects" @open="openProject" />
                    </PortfolioSection>

                    <PortfolioSection
                        v-if="resumeUrl"
                        id="resume"
                        index="03"
                        :title="t({ fa: 'رزومه', en: 'Resume' })"
                    >
                        <ResumeSection :resume-url="resumeUrl" />
                    </PortfolioSection>
                </div>
            </div>
        </main>

        <SiteFooter :handle="profile.handle || 'pooria.jahedi'" :socials="socials" />

        <ImageModal
            :open="modalOpen"
            :images="modalImages"
            :start-index="modalIndex"
            :alt="selectedProject ? t(selectedProject.title) : ''"
            @close="closePreview"
        />
    </div>
</template>
