<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    enabled: {
        type: Boolean,
        default: true,
    },
    // 0–100, how bright the stars burn.
    opacity: {
        type: Number,
        default: 80,
    },
});

/** Stellar colour temperatures (O/B blue → M red), weighted towards white. */
const STAR_COLORS = [
    '170,200,255',
    '202,220,255',
    '230,240,255',
    '255,255,255',
    '255,250,240',
    '255,238,205',
    '255,214,170',
    '255,190,160',
];

const canvasRef = ref(null);
const glowRef = ref(null);

let stopAnimation = null;

const brightness = () => Math.max(0.05, Math.min(1, props.opacity / 100));

const start = () => {
    const canvas = canvasRef.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let stars = [];
    let shooting = [];
    let raf = 0;
    let frame = 0;
    let scrollY = window.scrollY;
    let renderedScroll = scrollY;
    let w = window.innerWidth;
    let h = window.innerHeight;
    // Pointer position, normalized to -1..1 around the viewport centre.
    let mx = 0;
    let my = 0;
    let rmx = 0;
    let rmy = 0;

    const build = () => {
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = `${w}px`;
        canvas.style.height = `${h}px`;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const area = w * h;
        const dust = Math.round(area / 3600);
        const mid = Math.round(area / 26000);
        const hero = Math.max(5, Math.round(area / 190000));

        const make = (tier) => ({
            x: Math.random() * w,
            y: Math.random() * (h * 1.6),
            z: tier === 0 ? Math.random() * 0.4 : tier === 1 ? 0.4 + Math.random() * 0.4 : 0.82 + Math.random() * 0.18,
            r: tier === 0 ? 0.35 + Math.random() * 0.3 : tier === 1 ? 0.85 + Math.random() * 0.55 : 1.5 + Math.random() * 0.9,
            a: tier === 0 ? 0.22 + Math.random() * 0.18 : tier === 1 ? 0.5 + Math.random() * 0.22 : 0.82 + Math.random() * 0.18,
            s: Math.random() * 0.03 + 0.004,
            p: Math.random() * Math.PI * 2,
            c: STAR_COLORS[Math.floor(Math.pow(Math.random(), 1.6) * STAR_COLORS.length)],
            tier,
        });

        stars = [
            ...Array.from({ length: dust }, () => make(0)),
            ...Array.from({ length: mid }, () => make(1)),
            ...Array.from({ length: hero }, () => make(2)),
        ];
    };

    const spawnShooting = () => {
        const fromLeft = Math.random() > 0.5;
        const speed = 5 + Math.random() * 4;

        shooting.push({
            x: fromLeft ? -60 : w + 60,
            y: Math.random() * h * 0.6,
            vx: fromLeft ? speed : -speed,
            vy: speed * 0.45,
            life: 1,
            len: 90 + Math.random() * 70,
        });
    };

    const onScroll = () => {
        scrollY = window.scrollY;
    };

    const onPointer = (event) => {
        mx = (event.clientX / w) * 2 - 1;
        my = (event.clientY / h) * 2 - 1;

        if (glowRef.value) {
            glowRef.value.style.setProperty('--mx', `${event.clientX}px`);
            glowRef.value.style.setProperty('--my', `${event.clientY}px`);
        }
    };

    const draw = () => {
        const gain = brightness();

        renderedScroll += (scrollY - renderedScroll) * 0.08;
        rmx += (mx - rmx) * 0.05;
        rmy += (my - rmy) * 0.05;
        ctx.clearRect(0, 0, w, h);
        ctx.globalCompositeOperation = 'lighter';

        for (const star of stars) {
            const depth = 0.08 + star.z * 0.55;
            const drift = frame * (0.02 + star.z * 0.05);
            const px = star.x - rmx * (10 + star.z * 55);
            let y = star.y - renderedScroll * depth + drift - rmy * (6 + star.z * 32);
            const span = h * 1.6;
            y = ((y % span) + span) % span;
            if (y > h + 6) continue;

            // Scintillation: distant dust shimmers most, hero stars stay steady.
            const flicker =
                Math.sin(frame * star.s + star.p) * 0.5 + Math.sin(frame * star.s * 2.7 + star.p * 1.9) * 0.22;
            const amp = star.tier === 0 ? 0.16 : star.tier === 1 ? 0.1 : 0.06;
            const alpha = Math.max(0.03, Math.min(1, (star.a + flicker * amp) * gain));

            if (star.tier > 0) {
                // A tight glow keeps mid and hero stars reading as points of light.
                const halo = star.r * (star.tier === 2 ? 6.5 : 4);
                const gradient = ctx.createRadialGradient(px, y, 0, px, y, halo);
                gradient.addColorStop(0, `rgba(${star.c},${alpha * (star.tier === 2 ? 0.4 : 0.24)})`);
                gradient.addColorStop(0.4, `rgba(${star.c},${alpha * 0.08})`);
                gradient.addColorStop(1, `rgba(${star.c},0)`);
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(px, y, halo, 0, Math.PI * 2);
                ctx.fill();
            }

            // Crisp core — this is what makes it read as a star, not a smudge.
            ctx.fillStyle = `rgba(${star.c},${alpha})`;
            ctx.beginPath();
            ctx.arc(px, y, star.r, 0, Math.PI * 2);
            ctx.fill();

            // Diffraction spikes on hero stars only.
            if (star.tier === 2) {
                const len = star.r * 9;
                const spike = ctx.createLinearGradient(px - len, y, px + len, y);
                spike.addColorStop(0, `rgba(${star.c},0)`);
                spike.addColorStop(0.5, `rgba(${star.c},${alpha * 0.45})`);
                spike.addColorStop(1, `rgba(${star.c},0)`);
                ctx.strokeStyle = spike;
                ctx.lineWidth = 0.7;
                ctx.beginPath();
                ctx.moveTo(px - len, y);
                ctx.lineTo(px + len, y);
                ctx.moveTo(px, y - len);
                ctx.lineTo(px, y + len);
                ctx.stroke();
            }
        }

        ctx.globalCompositeOperation = 'source-over';

        // Rare, quiet shooting stars.
        if (Math.random() < 0.002 && shooting.length < 1) spawnShooting();
        shooting = shooting.filter((item) => item.life > 0 && item.x > -200 && item.x < w + 200 && item.y < h + 200);

        for (const item of shooting) {
            item.x += item.vx;
            item.y += item.vy;
            item.life -= 0.006;

            const trail = ctx.createLinearGradient(
                item.x,
                item.y,
                item.x - item.vx * (item.len / 8),
                item.y - item.vy * (item.len / 8),
            );
            trail.addColorStop(0, `rgba(214,255,236,${0.45 * item.life * gain})`);
            trail.addColorStop(1, 'rgba(214,255,236,0)');
            ctx.strokeStyle = trail;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(item.x, item.y);
            ctx.lineTo(item.x - item.vx * (item.len / 8), item.y - item.vy * (item.len / 8));
            ctx.stroke();
        }

        ctx.globalAlpha = 1;
        frame += 1;

        if (!reducedMotion && !document.hidden) {
            raf = requestAnimationFrame(draw);
        }
    };

    // A hidden tab does not need its sky animated.
    const onVisibilityChange = () => {
        cancelAnimationFrame(raf);

        if (!document.hidden && !reducedMotion) {
            raf = requestAnimationFrame(draw);
        }
    };

    build();
    draw();

    document.addEventListener('visibilitychange', onVisibilityChange);
    window.addEventListener('resize', build, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('pointermove', onPointer, { passive: true });

    stopAnimation = () => {
        cancelAnimationFrame(raf);
        document.removeEventListener('visibilitychange', onVisibilityChange);
        window.removeEventListener('resize', build);
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('pointermove', onPointer);
        stopAnimation = null;
    };
};

const stop = () => {
    stopAnimation?.();
};

onMounted(() => {
    if (props.enabled) start();
});

watch(
    () => props.enabled,
    async (enabled) => {
        stop();
        if (!enabled) return;

        // The canvas is only in the DOM while the starfield is on.
        await nextTick();
        start();
    },
);

onBeforeUnmount(stop);
</script>

<template>
    <div class="pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
        <div class="nebula absolute inset-0"></div>
        <canvas v-if="enabled" ref="canvasRef" class="absolute inset-0"></canvas>
        <div ref="glowRef" class="cursor-glow absolute inset-0"></div>
        <div class="space-vignette absolute inset-0"></div>
    </div>
</template>
