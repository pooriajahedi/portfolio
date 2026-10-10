/** Turn an admin-stored path into something the browser can request. */
export const resolveStorageUrl = (value) => {
    const raw = String(value ?? '').trim();
    if (raw === '') return '';
    if (/^(https?:)?\/\//i.test(raw) || raw.startsWith('/')) return raw;

    return `/storage/${raw.replace(/^\/+/, '')}`;
};

/** Cache-bust a file whose path stays the same across uploads. */
export const withVersion = (url, version) => {
    const normalizedVersion = String(version ?? '').trim();
    if (url === '' || normalizedVersion === '') return url;

    return `${url}${url.includes('?') ? '&' : '?'}v=${encodeURIComponent(normalizedVersion)}`;
};

export const normalizeExternalUrl = (value) => {
    const raw = String(value ?? '').trim();
    if (raw === '') return '';
    if (/^(https?:\/\/|mailto:)/i.test(raw)) return raw;

    return `https://${raw.replace(/^\/+/, '')}`;
};

/** Strip protocol and trailing slash so a URL reads as a label. */
export const compactUrl = (value) =>
    String(value ?? '')
        .trim()
        .replace(/^https?:\/\//i, '')
        .replace(/^www\./i, '')
        .replace(/\/+$/g, '');

export const telegramUrl = (value) => {
    const handle = String(value ?? '')
        .trim()
        .replace(/^@+/, '');

    if (handle === '') return '';
    if (/^https?:\/\//i.test(handle)) return handle;

    return `https://t.me/${handle}`;
};
