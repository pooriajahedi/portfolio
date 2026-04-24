<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { gsap } from 'gsap';
import Lenis from 'lenis';
import IconGlyph from '../components/IconGlyph.vue';
import ImageModal from '../components/portfolio/ImageModal.vue';
import CustomCursor from '../components/portfolio/CustomCursor.vue';
import SidebarProfile from '../components/portfolio/SidebarProfile.vue';
import TabsNav from '../components/portfolio/TabsNav.vue';
import AboutSection from '../components/portfolio/sections/AboutSection.vue';
import ResumeSection from '../components/portfolio/sections/ResumeSection.vue';
import PortfolioSection from '../components/portfolio/sections/PortfolioSection.vue';
import PortfolioSingleSection from '../components/portfolio/sections/PortfolioSingleSection.vue';
import BlogSection from '../components/portfolio/sections/BlogSection.vue';
import ContactSection from '../components/portfolio/sections/ContactSection.vue';
import { usePublicContentApi } from '../composables/usePublicContentApi';

const props = defineProps({
    apiUrls: {
        type: Object,
        required: true,
    },
    csrfToken: {
        type: String,
        default: '',
    },
});

const { fetchSiteState, fetchResumeFeed, fetchPortfolioFeed, fetchPortfolioProject, fetchBlogFeed, fetchBlogPost, submitContactRequest } = usePublicContentApi(props.apiUrls);

const siteState = ref({});
const resumeItems = ref([]);
const portfolioTitle = ref('نمونه کارها');
const projectCategories = ref([]);
const projects = ref([]);
const selectedProjectSlug = ref('');
const selectedProject = ref(null);
const blogPosts = ref([]);
const selectedBlogSlug = ref('');
const selectedBlogPost = ref(null);

const loadingSite = ref(true);
const loadingResume = ref(true);
const loadingPortfolio = ref(true);
const loadingBlog = ref(true);
const loadingProjectDetail = ref(false);
const loadingBlogDetail = ref(false);
const portfolioDetailRequestToken = ref(0);
const blogDetailRequestToken = ref(0);

const siteError = ref('');
const resumeError = ref('');
const portfolioError = ref('');
const blogError = ref('');
const projectDetailError = ref('');
const blogDetailError = ref('');

const activeCategory = ref('all');
const activeTab = ref('about');
const themeMode = ref('dark');
const modalOpen = ref(false);
const modalImage = ref('');
const modalAlt = ref('');
const modalImages = ref([]);
const modalIndex = ref(0);

const profile = computed(() => siteState.value.profile ?? {});
const about = computed(() => siteState.value.about ?? {});
const serviceCards = computed(() => siteState.value.services ?? []);
const skillCategoryLabels = computed(() => siteState.value.skillCategoryLabels ?? {});
const skills = computed(() => siteState.value.skills ?? []);
const contacts = computed(() => siteState.value.contacts ?? {});
const appVersion = computed(() => siteState.value.appVersion ?? '1.0.3');

const currentStatus = computed(() => profile.value?.currentStatus?.key ?? 'looking_for_job');

const avatarImage = computed(() => {
    const raw = String(profile.value?.avatarImage ?? '/images/hero/pooria-hero.jpeg').trim();

    if (raw === '') return '/images/hero/pooria-hero.jpeg';
    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('/')) return raw;

    return `/storage/${raw.replace(/^\/+/, '')}`;
});

const resumeFileUrl = computed(() => {
    const raw = String(profile.value?.resumeFile ?? '').trim();
    const version = String(profile.value?.resumeFileVersion ?? '').trim();

    if (raw === '') return '';

    const baseUrl = raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('/')
        ? raw
        : `/storage/${raw.replace(/^\/+/, '')}`;

    if (version === '') return baseUrl;

    const separator = baseUrl.includes('?') ? '&' : '?';

    return `${baseUrl}${separator}v=${encodeURIComponent(version)}`;
});

const groupedSkills = computed(() => {
    return skills.value.reduce((acc, item) => {
        const key = item?.category ?? 'frontend';
        if (!acc[key]) acc[key] = [];
        acc[key].push(item);
        return acc;
    }, {});
});

const filteredProjects = computed(() => {
    if (activeCategory.value === 'all') return projects.value;
    return projects.value.filter((item) => (item?.category?.slug ?? 'uncategorized') === activeCategory.value);
});

const isPortfolioSingle = computed(() => selectedProjectSlug.value !== '');
const isBlogSingle = computed(() => selectedBlogSlug.value !== '');

const compactUrl = (value) => {
    const normalized = String(value ?? '').trim();
    if (normalized === '') return '';

    return normalized
        .replace(/^https?:\/\//i, '')
        .replace(/^www\./i, '')
        .replace(/\/+$/g, '');
};

const linkedinDisplayId = (value) => {
    const normalized = compactUrl(value);
    if (normalized === '') return '';

    const match = normalized.match(/linkedin\.com\/(?:in|company)\/([^/?#]+)/i);
    if (match?.[1]) return String(match[1]).trim();

    const parts = normalized.split('/');
    const last = String(parts[parts.length - 1] ?? '').trim();
    return last || normalized;
};

const normalizeExternalUrl = (value) => {
    const normalized = String(value ?? '').trim();
    if (normalized === '') return null;

    if (normalized.startsWith('http://') || normalized.startsWith('https://') || normalized.startsWith('mailto:')) {
        return normalized;
    }

    return `https://${normalized.replace(/^\/+/, '')}`;
};

const contactItems = computed(() => {
    const email = String(contacts.value?.email ?? '').trim();
    const telegramId = String(contacts.value?.telegram ?? '').trim().replace(/^@+/, '');
    const github = String(contacts.value?.github ?? '').trim();
    const linkedin = String(contacts.value?.linkedin ?? '').trim();

    const items = [
        {
            label: 'ایمیل',
            value: email,
            display: email,
            icon: contacts.value?.emailIcon || 'logos:google-gmail',
            href: email !== '' ? `mailto:${email}` : null,
        },
        {
            label: 'تلگرام',
            value: telegramId !== '' ? `@${telegramId}` : '',
            display: telegramId !== '' ? `@${telegramId}` : '',
            icon: contacts.value?.telegramIcon || 'mdi:telegram',
            href: telegramId !== '' ? `https://t.me/${telegramId}` : null,
        },
        {
            label: 'گیت‌هاب',
            value: github,
            display: compactUrl(github),
            icon: contacts.value?.githubIcon || 'mdi:github',
            href: normalizeExternalUrl(github),
        },
        {
            label: 'لینکدین',
            value: linkedin,
            display: linkedinDisplayId(linkedin),
            icon: contacts.value?.linkedinIcon || 'mdi:linkedin',
            href: normalizeExternalUrl(linkedin),
        },
    ];

    return items.filter((item) => String(item.display ?? '').trim() !== '');
});

const selectTab = (tab) => {
    activeTab.value = tab;
    if (tab !== 'portfolio') {
        clearPortfolioSingleRoute(false);
    }
    if (tab !== 'blog') {
        clearBlogSingleRoute(false);
    }

    requestAnimationFrame(() => animateTabEntrance(tab));
    nextTick(() => {
        initHoverLight();

        const section = document.getElementById(tab);
        if (section) {
            if (lenisInstance) {
                lenisInstance.scrollTo(section, {
                    offset: -10,
                    duration: 0.85,
                });
            } else {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        }
    });
};

const openImageModal = ({ src, alt = '', images = [], startIndex = 0 }) => {
    if (!src) return;
    modalImage.value = src;
    modalAlt.value = alt;
    modalImages.value = Array.isArray(images) ? images : [];
    modalIndex.value = Number.isFinite(Number(startIndex)) ? Math.max(0, Number(startIndex)) : 0;
    modalOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closeImageModal = () => {
    modalOpen.value = false;
    modalImage.value = '';
    modalAlt.value = '';
    modalImages.value = [];
    modalIndex.value = 0;
    document.body.style.overflow = '';
};

const extractPortfolioSlugFromPath = () => {
    const match = window.location.pathname.match(/^\/portfolio\/([^/]+)$/i);
    return match?.[1] ? decodeURIComponent(match[1]) : '';
};

const extractBlogSlugFromPath = () => {
    const match = window.location.pathname.match(/^\/blog\/([^/]+)$/i);
    return match?.[1] ? decodeURIComponent(match[1]) : '';
};

const clearPortfolioSingleRoute = (pushState = true) => {
    portfolioDetailRequestToken.value += 1;
    selectedProjectSlug.value = '';
    selectedProject.value = null;
    loadingProjectDetail.value = false;
    projectDetailError.value = '';

    if (pushState && window.location.pathname !== '/') {
        window.history.pushState({}, '', '/');
    }
};

const clearBlogSingleRoute = (pushState = true) => {
    blogDetailRequestToken.value += 1;
    selectedBlogSlug.value = '';
    selectedBlogPost.value = null;
    loadingBlogDetail.value = false;
    blogDetailError.value = '';

    if (pushState && window.location.pathname !== '/') {
        window.history.pushState({}, '', '/');
    }
};

const focusSingleContent = (targetId) => {
    nextTick(() => {
        const section = document.getElementById(targetId);
        if (!section) return;

        section.focus?.({ preventScroll: true });

        if (lenisInstance) {
            lenisInstance.scrollTo(section, {
                offset: -10,
                duration: 0.8,
            });
            return;
        }

        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    });
};

const loadPortfolioProject = async (slug) => {
    const normalizedSlug = String(slug ?? '').trim();
    if (normalizedSlug === '') return;

    if (loadingProjectDetail.value && selectedProjectSlug.value === normalizedSlug) return;
    if (!loadingProjectDetail.value && selectedProjectSlug.value === normalizedSlug && selectedProject.value) return;

    const requestToken = portfolioDetailRequestToken.value + 1;
    portfolioDetailRequestToken.value = requestToken;
    selectedProjectSlug.value = normalizedSlug;
    selectedProject.value = null;
    loadingProjectDetail.value = true;
    projectDetailError.value = '';

    try {
        const payload = await fetchPortfolioProject(normalizedSlug);
        if (portfolioDetailRequestToken.value !== requestToken) return;
        selectedProject.value = payload?.project ?? null;
    } catch {
        if (portfolioDetailRequestToken.value !== requestToken) return;
        selectedProject.value = null;
        projectDetailError.value = 'پروژه موردنظر پیدا نشد یا خطایی در دریافت اطلاعات رخ داد.';
    } finally {
        if (portfolioDetailRequestToken.value !== requestToken) return;
        loadingProjectDetail.value = false;
        nextTick(() => {
            initHoverLight();
            requestAnimationFrame(() => animateTabEntrance('portfolio-single'));
        });
    }
};

const openProjectDetail = (slug) => {
    const normalizedSlug = String(slug ?? '').trim();
    if (normalizedSlug === '') return;
    if (selectedProjectSlug.value === normalizedSlug && (loadingProjectDetail.value || selectedProject.value)) return;

    activeTab.value = 'portfolio';
    clearBlogSingleRoute(false);
    selectedProjectSlug.value = normalizedSlug;
    selectedProject.value = null;
    projectDetailError.value = '';
    if (window.location.pathname !== `/portfolio/${normalizedSlug}`) {
        window.history.pushState({}, '', `/portfolio/${encodeURIComponent(normalizedSlug)}`);
    }
    focusSingleContent('portfolio-single');
    loadPortfolioProject(normalizedSlug);
};

const handlePortfolioBack = () => {
    clearPortfolioSingleRoute(true);
};

const openProjectUrlFromSingle = (url) => {
    const normalized = normalizeExternalUrl(url);
    if (!normalized) return;
    window.open(normalized, '_blank', 'noopener,noreferrer');
};

const loadBlogPost = async (slug) => {
    const normalizedSlug = String(slug ?? '').trim();
    if (normalizedSlug === '') return;

    if (loadingBlogDetail.value && selectedBlogSlug.value === normalizedSlug) return;
    if (!loadingBlogDetail.value && selectedBlogSlug.value === normalizedSlug && selectedBlogPost.value) return;

    const requestToken = blogDetailRequestToken.value + 1;
    blogDetailRequestToken.value = requestToken;
    selectedBlogSlug.value = normalizedSlug;
    selectedBlogPost.value = null;
    loadingBlogDetail.value = true;
    blogDetailError.value = '';

    try {
        const payload = await fetchBlogPost(normalizedSlug);
        if (blogDetailRequestToken.value !== requestToken) return;
        selectedBlogPost.value = payload ?? null;
    } catch {
        if (blogDetailRequestToken.value !== requestToken) return;
        selectedBlogPost.value = null;
        blogDetailError.value = 'مقاله موردنظر پیدا نشد یا خطایی در دریافت اطلاعات رخ داد.';
    } finally {
        if (blogDetailRequestToken.value !== requestToken) return;
        loadingBlogDetail.value = false;
        nextTick(() => {
            initHoverLight();
            requestAnimationFrame(() => animateTabEntrance('blogDetail'));
        });
    }
};

const openBlogDetail = (slug) => {
    const normalizedSlug = String(slug ?? '').trim();
    if (normalizedSlug === '') return;
    if (selectedBlogSlug.value === normalizedSlug && (loadingBlogDetail.value || selectedBlogPost.value)) return;

    activeTab.value = 'blog';
    clearPortfolioSingleRoute(false);
    selectedBlogSlug.value = normalizedSlug;
    selectedBlogPost.value = null;
    blogDetailError.value = '';
    if (window.location.pathname !== `/blog/${normalizedSlug}`) {
        window.history.pushState({}, '', `/blog/${encodeURIComponent(normalizedSlug)}`);
    }
    focusSingleContent('blogDetail');
    loadBlogPost(normalizedSlug);
};

const handleBlogBack = () => {
    clearBlogSingleRoute(true);
};

const handlePopState = () => {
    const portfolioSlug = extractPortfolioSlugFromPath();
    const blogSlug = extractBlogSlugFromPath();

    if (portfolioSlug) {
        activeTab.value = 'portfolio';
        clearBlogSingleRoute(false);
        loadPortfolioProject(portfolioSlug);
        return;
    }

    if (blogSlug) {
        activeTab.value = 'blog';
        clearPortfolioSingleRoute(false);
        loadBlogPost(blogSlug);
        return;
    }

    clearPortfolioSingleRoute(false);
    clearBlogSingleRoute(false);
};

const submitContact = async (payload) => {
    try {
        const response = await submitContactRequest(payload, props.csrfToken);
        return response;
    } catch (error) {
        const firstValidationError = error?.payload?.errors
            ? Object.values(error.payload.errors).flat()[0]
            : null;

        throw new Error(firstValidationError || error?.payload?.message || error?.message || 'ارسال پیام ناموفق بود.');
    }
};

const applyThemeStyle = (themeStyle) => {
    const safeTheme = themeStyle === 'green' ? 'green' : 'gold';
    document.body.setAttribute('data-theme-style', safeTheme);
};

const resolveStorageOrAbsoluteUrl = (value) => {
    const raw = String(value ?? '').trim();
    if (raw === '') return '';
    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('/')) return raw;
    return `/storage/${raw.replace(/^\/+/, '')}`;
};

const applyAppearance = (appearance) => {
    const matrixEnabled = Boolean(appearance?.matrix?.enabled ?? true);
    const matrixOpacity = Number(appearance?.matrix?.opacity ?? 56);
    const safeOpacity = Number.isFinite(matrixOpacity) ? Math.max(0, Math.min(100, matrixOpacity)) : 56;

    // Keep existing defaults as "56" so current look doesn't change unless configured.
    const rainAlpha = Math.max(0, Math.min(1, safeOpacity / 100));
    // Scale fade alpha proportionally to the old default (0.12 when rainAlpha ~= 0.56).
    const fadeAlpha = Math.max(0.02, Math.min(0.25, rainAlpha * (0.12 / 0.56)));

    document.body.style.setProperty('--matrix-rain-alpha', String(rainAlpha));
    document.body.style.setProperty('--matrix-fade-alpha', String(fadeAlpha));
    document.body.setAttribute('data-matrix-enabled', matrixEnabled ? '1' : '0');

    if (matrixEnabled) {
        document.body.removeAttribute('data-bg-mode');
        document.body.style.removeProperty('--site-bg-image');
        return { matrixEnabled };
    }

    const bgModeRaw = String(appearance?.background?.mode ?? 'gradient').trim();
    const bgMode = bgModeRaw === 'solid' || bgModeRaw === 'image' || bgModeRaw === 'gradient' ? bgModeRaw : 'gradient';
    document.body.setAttribute('data-bg-mode', bgMode);

    const solid = String(appearance?.background?.solidColor ?? '#0a0b0f').trim() || '#0a0b0f';
    const from = String(appearance?.background?.gradientFrom ?? '#0a0b0f').trim() || '#0a0b0f';
    const to = String(appearance?.background?.gradientTo ?? '#101827').trim() || '#101827';
    document.body.style.setProperty('--site-bg-solid', solid);
    document.body.style.setProperty('--site-bg-gradient-from', from);
    document.body.style.setProperty('--site-bg-gradient-to', to);

    const imageUrl = resolveStorageOrAbsoluteUrl(appearance?.background?.image ?? '');
    document.body.style.setProperty('--site-bg-image', imageUrl ? `url("${imageUrl}")` : 'none');
    const imageOpacityPercent = Number(appearance?.background?.imageOpacity ?? 100);
    const imageOpacity = Number.isFinite(imageOpacityPercent)
        ? Math.max(0, Math.min(1, imageOpacityPercent / 100))
        : 1;
    document.body.style.setProperty('--site-bg-image-opacity', String(imageOpacity));

    return { matrixEnabled };
};

const applyThemeMode = (mode) => {
    const safeMode = mode === 'light' ? 'light' : 'dark';
    themeMode.value = safeMode;
    document.body.setAttribute('data-theme-mode', safeMode);
};

const initThemeMode = () => {
    const stored = String(window.localStorage.getItem('portfolio-theme-mode') ?? '').trim();

    if (stored === 'light' || stored === 'dark') {
        applyThemeMode(stored);
        return;
    }

    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? true;
    applyThemeMode(prefersDark ? 'dark' : 'light');
};

const toggleThemeMode = () => {
    const nextMode = themeMode.value === 'dark' ? 'light' : 'dark';
    applyThemeMode(nextMode);
    window.localStorage.setItem('portfolio-theme-mode', nextMode);
};

const handleEsc = (event) => {
    if (event.key === 'Escape') {
        if (modalOpen.value) closeImageModal();
    }
};

let matrixCleanup = null;
let tabAnimationFrame = 0;
let hoverLightCleanup = null;
let lenisInstance = null;
let lenisFrameId = 0;

const preventContextMenu = (event) => {
    event.preventDefault();
};

const animateTabEntrance = (tab) => {
    const section = document.getElementById(tab);
    if (!section) return;

    const targets = section.querySelectorAll('h2, .underline, .text-block, .service-card, .skill-item, .timeline-item, .portfolio-card, .portfolio-single-title, .portfolio-single-image, .portfolio-single-content, .portfolio-related-card, .blog-card, .blog-detail-item, .contact-form, .resume-download-btn, .site-credit');

    targets.forEach((element, index) => {
        element.animate(
            [
                { opacity: 0, transform: 'translateY(14px)' },
                { opacity: 1, transform: 'translateY(0)' },
            ],
            {
                duration: 360,
                delay: Math.min(index * 26, 220),
                easing: 'cubic-bezier(.2,.7,.2,1)',
                fill: 'both',
            }
        );
    });
};

const initMatrixRain = () => {
    const canvas = document.getElementById('matrixRain');
    if (!(canvas instanceof HTMLCanvasElement)) return;

    const context = canvas.getContext('2d');
    if (!context) return;

    const letters = 'アァカサタナハマヤャラワガザダバパイィキシチニヒミリヰギジヂビピウゥクスツヌフムユュルグズヅブプエェケセテネヘメレヱゲゼデベペオォコソトノホモヨョロヲゴゾドボポヴッン<>[]{}()$#@*&+=-_0123456789';
    const fontSize = 16;
    let drops = [];
    let frameId = 0;
    let lastTick = 0;
    const frameInterval = 70;

    const setup = () => {
        const dpr = Math.max(1, Math.min(window.devicePixelRatio || 1, 2));
        const width = window.innerWidth;
        const height = window.innerHeight;

        canvas.width = Math.floor(width * dpr);
        canvas.height = Math.floor(height * dpr);
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(dpr, 0, 0, dpr, 0, 0);

        const columns = Math.ceil(width / fontSize);
        drops = Array.from({ length: columns }, () => Math.floor(Math.random() * -60));
    };

    const draw = () => {
        const width = window.innerWidth;
        const height = window.innerHeight;

        const matrixFade = getComputedStyle(document.body).getPropertyValue('--matrix-fade-rgb').trim() || '3, 9, 20';
        const matrixFadeAlpha = parseFloat(getComputedStyle(document.body).getPropertyValue('--matrix-fade-alpha').trim() || '0.12');
        context.fillStyle = `rgba(${matrixFade}, ${Number.isFinite(matrixFadeAlpha) ? matrixFadeAlpha : 0.12})`;
        context.fillRect(0, 0, width, height);

        context.font = `${fontSize}px monospace`;
        const matrixColor = getComputedStyle(document.body).getPropertyValue('--matrix-rain-rgb').trim() || '231, 188, 102';
        const matrixRainAlpha = parseFloat(getComputedStyle(document.body).getPropertyValue('--matrix-rain-alpha').trim() || '0.56');
        context.fillStyle = `rgba(${matrixColor}, ${Number.isFinite(matrixRainAlpha) ? matrixRainAlpha : 0.56})`;

        for (let i = 0; i < drops.length; i += 1) {
            const text = letters[Math.floor(Math.random() * letters.length)];
            const x = i * fontSize;
            const y = drops[i] * fontSize;

            context.fillText(text, x, y);

            if (y > height && Math.random() > 0.978) {
                drops[i] = 0;
            }

            drops[i] += 0.8;
        }
    };

    const animate = (timestamp = 0) => {
        if (timestamp - lastTick >= frameInterval) {
            draw();
            lastTick = timestamp;
        }

        frameId = window.requestAnimationFrame(animate);
    };

    setup();
    animate();

    const onResize = () => setup();
    window.addEventListener('resize', onResize);

    matrixCleanup = () => {
        window.cancelAnimationFrame(frameId);
        window.removeEventListener('resize', onResize);
    };
};

const initHoverLight = () => {
    if (hoverLightCleanup) {
        hoverLightCleanup();
    }

    const targets = Array.from(document.querySelectorAll('.hover-light'));
    if (targets.length === 0) return;

    const cleanups = [];

    targets.forEach((element) => {
        const setX = gsap.quickTo(element, '--mx', { duration: 0.22, ease: 'power2.out' });
        const setY = gsap.quickTo(element, '--my', { duration: 0.22, ease: 'power2.out' });

        const handleMove = (event) => {
            const rect = element.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            setX(`${x}px`);
            setY(`${y}px`);
        };

        const handleEnter = () => {
            element.classList.add('is-hover-lit');
        };

        const handleLeave = () => {
            element.classList.remove('is-hover-lit');
        };

        element.addEventListener('pointermove', handleMove);
        element.addEventListener('pointerenter', handleEnter);
        element.addEventListener('pointerleave', handleLeave);

        cleanups.push(() => {
            element.removeEventListener('pointermove', handleMove);
            element.removeEventListener('pointerenter', handleEnter);
            element.removeEventListener('pointerleave', handleLeave);
            element.classList.remove('is-hover-lit');
        });
    });

    hoverLightCleanup = () => {
        cleanups.forEach((cleanup) => cleanup());
    };
};

const initSmoothScroll = () => {
    const prefersReducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;

    if (prefersReducedMotion) return;

    lenisInstance = new Lenis({
        duration: 0.95,
        smoothWheel: true,
        wheelMultiplier: 1.12,
        touchMultiplier: 1.08,
        easing: (t) => 1 - Math.pow(1 - t, 2.35),
    });

    const onFrame = (time) => {
        lenisInstance?.raf(time);
        lenisFrameId = window.requestAnimationFrame(onFrame);
    };

    lenisFrameId = window.requestAnimationFrame(onFrame);
};

onMounted(async () => {
    initSmoothScroll();
    initThemeMode();
    document.addEventListener('keydown', handleEsc);
    document.addEventListener('contextmenu', preventContextMenu);
    window.addEventListener('popstate', handlePopState);
    // Matrix init is controlled by site settings after we fetch the appearance payload.

    try {
        const site = await fetchSiteState();
        siteState.value = site ?? {};
        applyThemeStyle(site?.appearance?.themeStyle);
        const { matrixEnabled } = applyAppearance(site?.appearance ?? {});
        if (matrixEnabled) {
            initMatrixRain();
        }
        const profileName = String(site?.profile?.name ?? '').trim();
        const profileRole = String(site?.profile?.role ?? '').trim();

        if (profileName && profileRole) {
            document.title = `${profileName} | ${profileRole}`;
        } else if (profileName) {
            document.title = profileName;
        }
        siteError.value = '';
    } catch {
        siteError.value = 'خطا در دریافت اطلاعات سایت.';
    } finally {
        loadingSite.value = false;
    }

    try {
        const resumeFeed = await fetchResumeFeed();
        resumeItems.value = Array.isArray(resumeFeed?.data) ? resumeFeed.data : [];
        resumeError.value = '';
    } catch {
        resumeError.value = 'خطا در دریافت رزومه.';
    } finally {
        loadingResume.value = false;
    }

    try {
        const portfolioFeed = await fetchPortfolioFeed();
        portfolioTitle.value = portfolioFeed?.title ?? 'نمونه کارها';
        projectCategories.value = portfolioFeed?.categories ?? [];
        projects.value = portfolioFeed?.projects ?? [];
        portfolioError.value = '';

        const routeSlug = extractPortfolioSlugFromPath();
        if (routeSlug) {
            activeTab.value = 'portfolio';
            await loadPortfolioProject(routeSlug);
        }
    } catch {
        portfolioError.value = 'خطا در دریافت داده‌های نمونه‌کارها.';
    } finally {
        loadingPortfolio.value = false;
    }

    try {
        const blogFeed = await fetchBlogFeed();
        blogPosts.value = Array.isArray(blogFeed?.data) ? blogFeed.data : [];
        blogError.value = '';

        const blogRouteSlug = extractBlogSlugFromPath();
        if (blogRouteSlug) {
            activeTab.value = 'blog';
            await loadBlogPost(blogRouteSlug);
        }
    } catch {
        blogError.value = 'خطا در دریافت داده‌های وبلاگ.';
    } finally {
        loadingBlog.value = false;
    }

    await nextTick();
    initHoverLight();
});

onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleEsc);
    document.removeEventListener('contextmenu', preventContextMenu);
    window.removeEventListener('popstate', handlePopState);

    if (matrixCleanup) matrixCleanup();
    if (hoverLightCleanup) hoverLightCleanup();
    if (lenisFrameId) {
        window.cancelAnimationFrame(lenisFrameId);
    }
    if (lenisInstance) {
        lenisInstance.destroy();
        lenisInstance = null;
    }
    if (tabAnimationFrame) {
        window.cancelAnimationFrame(tabAnimationFrame);
    }

    document.body.style.overflow = '';
});
</script>

<template>
    <div>
        <div class="site-bg-image-layer" aria-hidden="true"></div>
        <div class="bg-orbs" aria-hidden="true">
            <span class="orb orb-1"></span>
            <span class="orb orb-2"></span>
            <span class="orb orb-3"></span>
            <span class="orb orb-4"></span>
            <span class="orb orb-5"></span>
        </div>
        <canvas class="matrix-rain" id="matrixRain" aria-hidden="true"></canvas>

        <div class="layout">
            <SidebarProfile
                :profile="profile"
                :avatar-image="avatarImage"
                :current-status="currentStatus"
                :resume-file-url="resumeFileUrl"
                :contact-items="contactItems"
                :loading="loadingSite" />

            <main class="content-shell glass-panel" id="app">
                <TabsNav :active-tab="activeTab" :theme-mode="themeMode" @change="selectTab" @toggle-theme="toggleThemeMode" />

                <div v-if="siteError" class="panel section">
                    <p class="text-block">{{ siteError }}</p>
                </div>

                <template v-else>
                    <AboutSection
                        v-show="activeTab === 'about'"
                        :about="about"
                        :service-cards="serviceCards"
                        :skill-category-labels="skillCategoryLabels"
                        :grouped-skills="groupedSkills"
                        :loading="loadingSite" />

                    <ResumeSection
                        v-show="activeTab === 'resume'"
                        :timeline="resumeItems"
                        :resume-file-url="resumeFileUrl"
                        :loading="loadingResume" />

                    <div v-if="resumeError && activeTab === 'resume'" class="panel" style="margin-top:12px;">
                        <p class="text-block">{{ resumeError }}</p>
                    </div>

                    <PortfolioSection
                        v-show="activeTab === 'portfolio' && !isPortfolioSingle"
                        :title="portfolioTitle"
                        :categories="projectCategories"
                        :projects="filteredProjects"
                        :active-category="activeCategory"
                        :loading="loadingPortfolio"
                        :load-error="portfolioError"
                        @change-category="activeCategory = $event"
                        @open-detail="openProjectDetail"
                        @open-image="openImageModal" />

                    <PortfolioSingleSection
                        v-show="activeTab === 'portfolio' && isPortfolioSingle"
                        :project="selectedProject"
                        :loading="loadingProjectDetail"
                        :load-error="projectDetailError"
                        @back="handlePortfolioBack"
                        @open-project-url="openProjectUrlFromSingle"
                        @open-image="openImageModal" />

                    <BlogSection
                        v-show="activeTab === 'blog'"
                        :posts="blogPosts"
                        :active-blog-slug="selectedBlogSlug"
                        :selected-post="selectedBlogPost"
                        :loading="loadingBlog"
                        :loading-detail="loadingBlogDetail"
                        :load-error="blogError"
                        :load-detail-error="blogDetailError"
                        @open-blog="openBlogDetail"
                        @close-blog="handleBlogBack"
                        @open-image="openImageModal" />

                    <ContactSection
                        v-show="activeTab === 'contact'"
                        :contacts="contacts"
                        :loading="loadingSite"
                        :submit-handler="submitContact" />
                </template>

                <footer class="site-credit">
                    <p>طراحی و توسعه این قالب توسط <strong>{{ profile.name || 'پوریا جاهدی' }}</strong> انجام شده است.</p>
                    <span class="site-credit-version">
                        <IconGlyph icon="mdi:tag-outline" :size="16" />
                        <span>نسخه {{ appVersion }}</span>
                    </span>
                </footer>
            </main>
        </div>

        <ImageModal
            :open="modalOpen"
            :src="modalImage"
            :alt="modalAlt"
            :images="modalImages"
            :start-index="modalIndex"
            @close="closeImageModal" />

        <CustomCursor />
    </div>
</template>
