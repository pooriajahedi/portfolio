export function usePublicContentApi(apiUrls = {}) {
    const unwrapResource = (payload) => {
        if (payload && typeof payload === 'object' && !Array.isArray(payload) && 'data' in payload) {
            return payload.data;
        }

        return payload;
    };

    const fetchJson = async (url) => {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Request failed: ${response.status}`);
        }

        return response.json();
    };

    const postJson = async (url, body, csrfToken) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(body),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            const error = new Error(payload?.message || 'Request failed');
            error.status = response.status;
            error.payload = payload;
            throw error;
        }

        return payload;
    };

    const resolvePortfolioShowUrl = (slug) => {
        const normalized = String(slug ?? '').trim();
        const encodedSlug = encodeURIComponent(normalized);
        const template = String(apiUrls.portfolioShow ?? '').trim();

        if (template.includes('__slug__')) {
            return template.replace('__slug__', encodedSlug);
        }

        return `/api/portfolio/${encodedSlug}`;
    };

    const resolveBlogShowUrl = (slug) => {
        const normalized = String(slug ?? '').trim();
        const encodedSlug = encodeURIComponent(normalized);
        const template = String(apiUrls.blogPostShow ?? '').trim();

        if (template.includes('__slug__')) {
            return template.replace('__slug__', encodedSlug);
        }

        return `/api/blog-posts/${encodedSlug}`;
    };

    return {
        fetchSiteState: async () => unwrapResource(await fetchJson(apiUrls.site)),
        fetchResumeFeed: async () => fetchJson(apiUrls.resume),
        fetchPortfolioFeed: async () => unwrapResource(await fetchJson(apiUrls.portfolio)),
        fetchPortfolioProject: async (slug) => unwrapResource(await fetchJson(resolvePortfolioShowUrl(slug))),
        fetchBlogFeed: async () => fetchJson(apiUrls.blogPosts),
        fetchBlogPost: async (slug) => unwrapResource(await fetchJson(resolveBlogShowUrl(slug))),
        submitContactRequest: async (payload, csrfToken) => postJson(apiUrls.contactStore, payload, csrfToken),
    };
}
