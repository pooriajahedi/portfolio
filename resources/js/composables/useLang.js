import { computed, ref, watch } from 'vue';

const STORAGE_KEY = 'portfolio-lang';
const SUPPORTED = ['fa', 'en'];

const readStoredLang = () => {
    try {
        const saved = window.localStorage.getItem(STORAGE_KEY);
        return SUPPORTED.includes(saved) ? saved : 'fa';
    } catch {
        return 'fa';
    }
};

// One shared language for the whole page, so every component stays in sync.
const lang = ref(readStoredLang());

watch(
    lang,
    (value) => {
        const dir = value === 'fa' ? 'rtl' : 'ltr';
        document.documentElement.lang = value;
        document.documentElement.dir = dir;

        try {
            window.localStorage.setItem(STORAGE_KEY, value);
        } catch {
            // Private browsing: the language simply resets on the next visit.
        }
    },
    { immediate: true },
);

/** Pick the active language out of a `{ fa, en }` payload, falling back to Persian. */
export const translate = (value) => {
    if (value == null) return '';
    if (typeof value === 'string') return value;

    return String(value[lang.value] ?? value.fa ?? value.en ?? '');
};

export function useLang() {
    return {
        lang,
        dir: computed(() => (lang.value === 'fa' ? 'rtl' : 'ltr')),
        isRtl: computed(() => lang.value === 'fa'),
        t: translate,
        toggle: () => {
            lang.value = lang.value === 'fa' ? 'en' : 'fa';
        },
    };
}
