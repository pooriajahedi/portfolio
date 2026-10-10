// Fades a block in once as it scrolls into view. Blocks that already revealed
// (for example after returning from a project page) stay visible, so nothing
// animates a second time.
const revealed = new Set();

const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const reveal = {
    mounted(el, { value: key }) {
        if (reduceMotion() || (key && revealed.has(key)) || typeof IntersectionObserver === 'undefined') {
            return;
        }

        el.classList.add('reveal');

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;

                el.classList.add('is-visible');
                if (key) revealed.add(key);
                observer.disconnect();
            },
            { threshold: 0.12 },
        );

        observer.observe(el);
        el._revealObserver = observer;
    },
    unmounted(el) {
        el._revealObserver?.disconnect();
    },
};
