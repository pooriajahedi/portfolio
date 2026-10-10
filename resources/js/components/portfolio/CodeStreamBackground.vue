<script setup>
import { CODE_COLUMNS } from '../../support/codeSnippets';
import { highlightPhp } from '../../support/highlightPhp';

// Highlighted once at load; the markup is built from our own static snippets.
const columns = CODE_COLUMNS.map((snippets) =>
    snippets.map((snippet) => `<div class="code-block">${highlightPhp(snippet)}</div>`).join(''),
);
</script>

<template>
    <div class="code-stream pointer-events-none fixed inset-0 -z-10 select-none overflow-hidden" aria-hidden="true">
        <div class="code-stream__columns">
            <div v-for="(html, index) in columns" :key="index" class="code-stream__column">
                <!-- The block is shown twice so translating by -50% loops without a seam. -->
                <div class="code-stream__track" dir="ltr">
                    <div v-html="html"></div>
                    <div v-html="html"></div>
                </div>
            </div>
        </div>

        <div class="code-stream__shade"></div>
    </div>
</template>

<style scoped>
.code-stream {
    --shade-x: 30%;
}

/* Hero copy sits at the start edge, so the quiet zone follows the reading direction. */
:global(:root[dir='rtl']) .code-stream {
    --shade-x: 70%;
}

.code-stream__columns {
    display: flex;
    justify-content: space-around;
    height: 100%;
    padding-inline: 0.75rem;
    /* A touch of blur keeps it a texture rather than something to read. */
    filter: blur(1.2px);
    -webkit-mask-image: linear-gradient(180deg, transparent 0, #000 16%, #000 84%, transparent 100%);
    mask-image: linear-gradient(180deg, transparent 0, #000 16%, #000 84%, transparent 100%);
}

.code-stream__column {
    width: 18.75rem;
    height: 100%;
    overflow: hidden;
}

.code-stream__column:nth-child(2n) .code-stream__track {
    animation-direction: reverse;
}

.code-stream__column:nth-child(1) .code-stream__track {
    animation-duration: 130s;
}

.code-stream__column:nth-child(2) .code-stream__track {
    animation-duration: 160s;
}

.code-stream__column:nth-child(3) .code-stream__track {
    animation-duration: 110s;
}

.code-stream__column:nth-child(4) .code-stream__track {
    animation-duration: 145s;
}

.code-stream__track {
    font-family: var(--font-mono);
    font-size: 0.78125rem;
    line-height: 1.1875rem;
    white-space: pre;
    color: var(--muted);
    opacity: var(--code-opacity);
    font-variant-ligatures: none;
    will-change: transform;
    animation: code-scroll linear infinite;
}

/* Quiet the stream behind the hero copy so body text stays easy to read. */
.code-stream__shade {
    position: absolute;
    inset: 0;
    background: radial-gradient(
        ellipse 36% 56% at var(--shade-x) 52%,
        color-mix(in srgb, var(--bg) 92%, transparent) 22%,
        transparent 100%
    );
}

@keyframes code-scroll {
    from {
        transform: translateY(0);
    }

    to {
        transform: translateY(-50%);
    }
}

/* Fewer columns on narrower screens; a single one fits a phone. */
@media (max-width: 1279px) {
    .code-stream__column:nth-child(n + 4) {
        display: none;
    }
}

@media (max-width: 1023px) {
    .code-stream__column:nth-child(n + 3) {
        display: none;
    }
}

/* A phone shows a single column, quieter still: the copy spans the whole width. */
@media (max-width: 639px) {
    .code-stream__column:nth-child(n + 2) {
        display: none;
    }

    .code-stream__track {
        opacity: calc(var(--code-opacity) * 0.6);
    }
}

@media (prefers-reduced-motion: reduce) {
    .code-stream__track {
        animation: none;
    }
}

/* Rendered through v-html, so the generated nodes need :deep(). */
.code-stream :deep(.code-block) {
    counter-reset: line;
    margin-bottom: 2.375rem;
}

.code-stream :deep(.code-line) {
    display: block;
    height: 1.1875rem;
}

.code-stream :deep(.code-line)::before {
    counter-increment: line;
    content: counter(line);
    display: inline-block;
    width: 2.125rem;
    padding-right: 0.75rem;
    text-align: right;
    opacity: 0.55;
}

.code-stream :deep(.tok-keyword) {
    color: var(--code-keyword);
    font-weight: 500;
}

.code-stream :deep(.tok-string) {
    color: var(--code-string);
}

.code-stream :deep(.tok-function) {
    color: var(--code-function);
}

.code-stream :deep(.tok-type) {
    color: var(--code-type);
}

.code-stream :deep(.tok-number) {
    color: var(--code-number);
}

.code-stream :deep(.tok-variable) {
    color: var(--text);
}

.code-stream :deep(.tok-comment) {
    font-style: italic;
}
</style>
