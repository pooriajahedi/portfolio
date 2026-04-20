<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <title>پوریا جاهدی | برنامه‌نویس بک‌اند و فول‌استک</title>
    @php($themeStyle = in_array(($themeStyle ?? 'gold'), ['gold', 'green'], true) ? (string) $themeStyle : 'gold')

</head>
<body data-theme-style="{{ $themeStyle }}">
<div id="portfolioApp"></div>

<script>
    window.__PORTFOLIO_API_URLS__ = {
        site: @json(route('api.public.site')),
        resume: @json(route('api.public.resume')),
        portfolio: @json(route('api.public.portfolio')),
        portfolioShow: @json(route('api.public.portfolio.show', ['slug' => '__slug__'])),
        blogPosts: @json(route('api.public.blog-posts')),
        contactStore: @json(route('api.public.contact-requests.store')),
    };
    window.__PORTFOLIO_CSRF_TOKEN__ = @json(csrf_token());
</script>

@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/portfolio.js'])
</body>
</html>
