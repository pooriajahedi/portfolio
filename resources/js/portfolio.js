import { createApp } from 'vue';
import PortfolioHomePage from './pages/PortfolioHomePage.vue';
import { reveal } from './directives/reveal';

const container = document.getElementById('portfolioApp');

if (container) {
    const apiUrls = window.__PORTFOLIO_API_URLS__ ?? {
        site: '/api/site',
        portfolio: '/api/portfolio',
        portfolioShow: '/api/portfolio/__slug__',
    };

    createApp(PortfolioHomePage, { apiUrls }).directive('reveal', reveal).mount(container);
}
