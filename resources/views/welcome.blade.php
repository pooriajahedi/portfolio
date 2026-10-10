<!DOCTYPE html>
<html lang="fa" dir="rtl" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    {{-- The tab icon has a file per theme; the script below and useTheme.js pick the right one. --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" data-themed-href="/favicon-{theme}.svg">
    <script>
        // Apply the saved (or system) theme and language before first paint.
        (function () {
            try {
                var root = document.documentElement;
                var lang = localStorage.getItem('portfolio-lang') === 'en' ? 'en' : 'fa';
                var theme = localStorage.getItem('portfolio-theme');

                if (theme !== 'dark' && theme !== 'light') {
                    theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                }

                root.lang = lang;
                root.dir = lang === 'fa' ? 'rtl' : 'ltr';
                root.dataset.theme = theme;

                var icon = document.querySelector('link[rel="icon"][data-themed-href]');
                if (icon) icon.href = icon.dataset.themedHref.replace('{theme}', theme);
            } catch (e) {}
        })();
    </script>
    @php($metaTitle = trim((string) ($metaTitle ?? 'پوریا جاهدی | برنامه‌نویس بک‌اند و فول‌استک')))
    @php($metaDescription = trim((string) ($metaDescription ?? 'پورتفولیوی شخصی برای معرفی مهارت‌ها، نمونه‌کارها و رزومه.')))
    @php($metaImage = trim((string) ($metaImage ?? url('/images/hero/pooria-hero.jpeg'))))
    @php($metaUrl = trim((string) ($metaUrl ?? url('/'))))
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="پوریا جاهدی">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:secure_url" content="{{ $metaImage }}">
    <meta property="og:image:alt" content="{{ $metaTitle }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
</head>
<body>
<div id="portfolioApp"></div>

<script>
    window.__PORTFOLIO_API_URLS__ = {
        site: @json(route('api.public.site')),
        portfolio: @json(route('api.public.portfolio')),
        portfolioShow: @json(route('api.public.portfolio.show', ['slug' => '__slug__'])),
    };
</script>

@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/portfolio.js'])
</body>
</html>
