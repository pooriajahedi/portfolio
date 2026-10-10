import { onBeforeUnmount, onMounted } from 'vue';
import Lenis from 'lenis';

const HEADER_OFFSET = -80;

/** Inertial smooth scrolling for the whole page (respects reduced motion). */
export function useSmoothScroll() {
    let lenis = null;
    let raf = 0;

    const scrollToElement = (target) => {
        if (!target) return;

        if (lenis) {
            lenis.scrollTo(target, { offset: HEADER_OFFSET });
            return;
        }

        window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY + HEADER_OFFSET, behavior: 'smooth' });
    };

    const onClick = (event) => {
        const anchor = event.target?.closest?.('a[href^="#"]');
        const href = anchor?.getAttribute('href');
        if (!href || href === '#') return;

        const target = document.querySelector(href);
        if (!target) return;

        event.preventDefault();
        scrollToElement(target);
    };

    onMounted(() => {
        document.addEventListener('click', onClick);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        lenis = new Lenis({ duration: 1.1, smoothWheel: true });

        const loop = (time) => {
            lenis.raf(time);
            raf = requestAnimationFrame(loop);
        };

        raf = requestAnimationFrame(loop);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', onClick);
        cancelAnimationFrame(raf);
        lenis?.destroy();
        lenis = null;
    });

    return { scrollToElement };
}
