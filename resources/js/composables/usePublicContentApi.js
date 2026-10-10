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

    const resolvePortfolioShowUrl = (slug) => {
        const encodedSlug = encodeURIComponent(String(slug ?? '').trim());
        const template = String(apiUrls.portfolioShow ?? '').trim();

        if (template.includes('__slug__')) {
            return template.replace('__slug__', encodedSlug);
        }

        return `/api/portfolio/${encodedSlug}`;
    };

    return {
        fetchSiteState: async () => unwrapResource(await fetchJson(apiUrls.site)),
        fetchPortfolioFeed: async () => unwrapResource(await fetchJson(apiUrls.portfolio)),
        fetchPortfolioProject: async (slug) => unwrapResource(await fetchJson(resolvePortfolioShowUrl(slug))),
    };
}
