/**
 * A tiny PHP highlighter for the decorative code stream. It is not a parser:
 * one regular expression, one token class per capture group, and everything
 * else is left as plain (escaped) text.
 */

const KEYWORDS = [
    'namespace',
    'use',
    'class',
    'extends',
    'implements',
    'public',
    'protected',
    'private',
    'function',
    'fn',
    'return',
    'void',
    'string',
    'new',
];

// The order matches the capture groups below.
const KINDS = ['comment', 'string', 'variable', 'keyword', 'number', 'type', 'function'];

const TOKEN = new RegExp(
    [
        String.raw`(\/\/.*)`,
        String.raw`('(?:\\.|[^'\\])*')`,
        String.raw`(\$\w+)`,
        String.raw`(<\?php|\b(?:${KEYWORDS.join('|')})\b)`,
        String.raw`\b(\d+)\b`,
        String.raw`\b([A-Z]\w*(?:\\\w+)*)\b`,
        String.raw`\b(\w+)(?=\()`,
    ].join('|'),
    'g',
);

const escapeHtml = (text) => text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

const highlightLine = (line) => {
    let html = '';
    let cursor = 0;

    for (const match of line.matchAll(TOKEN)) {
        const kind = KINDS[match.findIndex((group, index) => index > 0 && group !== undefined) - 1];

        html += escapeHtml(line.slice(cursor, match.index));
        html += `<span class="tok-${kind}">${escapeHtml(match[0])}</span>`;
        cursor = match.index + match[0].length;
    }

    return html + escapeHtml(line.slice(cursor));
};

/** Highlight a snippet into one `.code-line` element per line. */
export const highlightPhp = (code) =>
    code
        .split('\n')
        .map((line) => `<span class="code-line">${highlightLine(line)}</span>`)
        .join('');
