import { computed, ref, watch } from 'vue';

const STORAGE_KEY = 'portfolio-theme';
const THEMES = ['dark', 'light'];

const systemTheme = () =>
    window.matchMedia?.('(prefers-color-scheme: light)').matches ? 'light' : 'dark';

const readStoredTheme = () => {
    try {
        const saved = window.localStorage.getItem(STORAGE_KEY);
        return THEMES.includes(saved) ? saved : systemTheme();
    } catch {
        return systemTheme();
    }
};

/** Point the tab icon at the file for the active theme (see the <link> in the layout). */
const syncFavicon = (value) => {
    const icon = document.querySelector('link[rel="icon"][data-themed-href]');

    if (icon) icon.href = icon.dataset.themedHref.replace('{theme}', value);
};

// One shared theme for the whole page. The layout applies it before first paint;
// this keeps the reactive state and the <html data-theme> attribute in step.
const theme = ref(readStoredTheme());

watch(
    theme,
    (value) => {
        document.documentElement.dataset.theme = value;
        syncFavicon(value);

        try {
            window.localStorage.setItem(STORAGE_KEY, value);
        } catch {
            // Private browsing: the theme simply resets on the next visit.
        }
    },
    { immediate: true },
);

export function useTheme() {
    return {
        theme,
        isDark: computed(() => theme.value === 'dark'),
        toggle: () => {
            theme.value = theme.value === 'dark' ? 'light' : 'dark';
        },
    };
}
