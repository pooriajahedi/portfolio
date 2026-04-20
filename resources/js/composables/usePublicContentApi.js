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

    return {
        fetchSiteState: async () => unwrapResource(await fetchJson(apiUrls.site)),
        fetchResumeFeed: async () => fetchJson(apiUrls.resume),
        fetchPortfolioFeed: async () => unwrapResource(await fetchJson(apiUrls.portfolio)),
        fetchBlogFeed: async () => fetchJson(apiUrls.blogPosts),
        submitContactRequest: async (payload, csrfToken) => postJson(apiUrls.contactStore, payload, csrfToken),
    };
}
