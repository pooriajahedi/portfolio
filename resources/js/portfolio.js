import { createApp } from 'vue';
import 'highlight.js/styles/atom-one-dark.css';
import PortfolioHomePage from './pages/PortfolioHomePage.vue';

const container = document.getElementById('portfolioApp');

if (container) {
    const apiUrls = window.__PORTFOLIO_API_URLS__ ?? {
        site: '/api/site',
        resume: '/api/resume-items',
        portfolio: '/api/portfolio',
        blogPosts: '/api/blog-posts',
        contactStore: '/api/contact-requests',
    };
    const csrfToken = window.__PORTFOLIO_CSRF_TOKEN__ ?? '';
    createApp(PortfolioHomePage, { apiUrls, csrfToken }).mount(container);
}
