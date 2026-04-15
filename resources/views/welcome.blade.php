<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پورتفولیو | {{ $portfolioData['profile']['name'] ?? 'برنامه نویس' }}</title>

    <style>
        @font-face {
            font-family: "Pinar";
            src: url("/fonts/pinar/Pinar-DS1-FD-Light.ttf") format("truetype");
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Pinar";
            src: url("/fonts/pinar/Pinar-DS1-FD-Medium.ttf") format("truetype");
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Pinar";
            src: url("/fonts/pinar/Pinar-DS1-FD-Bold.ttf") format("truetype");
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --bg: #0a0b0f;
            --surface: #17191f;
            --surface-soft: #1d2028;
            --line: #2a2f3a;
            --text: #f3f4f6;
            --muted: #a6adbb;
            --accent: #f4c64f;
            --accent-soft: rgba(244, 198, 79, 0.2);
            --radius: 22px;
            --sidebar-width: 322px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: auto;
        }

        body {
            font-family: "Pinar", sans-serif;
            background: radial-gradient(circle at 25% 0, #171d2a 0, transparent 30%), var(--bg);
            color: var(--text);
            line-height: 1.8;
            min-height: 100vh;
        }

        @media (pointer: fine) {
            body,
            a,
            button,
            input,
            textarea,
            select,
            .service-card,
            .skill-card,
            .portfolio-card,
            .blog-card,
            .contact-item,
            .tabs a,
            .submit {
                cursor: none !important;
            }
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            width: min(1280px, 96%);
            margin: 28px auto;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            gap: 20px;
            align-items: start;
        }

        .sidebar,
        .content-shell {
            background: linear-gradient(180deg, #1a1c23 0%, #15171d 100%);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: 0 26px 45px rgba(0, 0, 0, 0.35);
        }

        .sidebar {
            position: sticky;
            top: 20px;
            padding: 28px 24px 20px;
        }

        .avatar-wrap {
            width: 142px;
            height: 142px;
            border-radius: 30px;
            background: #2b2e35;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }

        .avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-dot {
            position: absolute;
            left: 10px;
            bottom: 14px;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 3px solid #232730;
        }

        .status-looking_for_job { background: #22c55e; }
        .status-resting { background: #f59e0b; }
        .status-unemployed { background: #ef4444; }

        .profile-name {
            text-align: center;
            font-size: 40px;
            line-height: 1.2;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-role {
            width: max-content;
            margin: 0 auto;
            padding: 5px 12px;
            border-radius: 999px;
            background: #2a2e38;
            color: #d9dde5;
            font-size: 13px;
        }

        .status-label {
            margin-top: 10px;
            border: 1px solid #2f3543;
            background: #1b1f28;
            border-radius: 16px;
            padding: 8px 12px;
            width: 100%;
            max-width: 280px;
            margin-inline: auto;
            text-align: center;
        }

        .status-label strong {
            display: block;
            color: #b8c0ce;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .status-label span {
            display: block;
            color: var(--accent);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.45;
        }

        .contact-list {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
            display: grid;
            gap: 12px;
        }

        .contact-item {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            background: #171a21;
            border: 1px solid #252a35;
            border-radius: 14px;
            padding: 10px;
        }

        .contact-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #1e222c;
            color: var(--accent);
            font-size: 26px;
        }

        .contact-icon iconify-icon {
            font-size: 30px;
            line-height: 1;
        }

        .contact-meta small {
            display: block;
            color: #8e97a6;
            font-size: 11px;
            line-height: 1.2;
        }

        .contact-meta {
            min-width: 0;
        }

        .contact-meta span,
        .contact-meta a {
            display: block;
            color: #e7e9ee;
            font-size: 15px;
            line-height: 1.5;
            direction: ltr;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .content-shell {
            padding: 0 26px 28px;
            overflow: hidden;
        }

        .tabs {
            position: sticky;
            top: 0;
            z-index: 20;
            background: linear-gradient(180deg, #232630 0%, #1b1d25 100%);
            border-bottom: 1px solid #303441;
            margin: 0 -26px;
            padding: 0 26px;
            display: flex;
            justify-content: flex-start;
            gap: 6px;
        }

        .tabs a {
            color: #c8ced8;
            font-weight: 500;
            font-size: 15px;
            padding: 16px 18px;
            display: inline-block;
            position: relative;
        }

        .tabs a:hover,
        .tabs a.active {
            color: var(--accent);
        }

        .tabs a.active::after {
            content: "";
            position: absolute;
            right: 18px;
            left: 18px;
            bottom: 8px;
            height: 2px;
            border-radius: 99px;
            background: var(--accent);
        }

        .section {
            padding-top: 28px;
            scroll-margin-top: 74px;
        }

        .section.is-hidden {
            display: none;
        }

        .section h2 {
            font-size: clamp(30px, 2.8vw, 38px);
            line-height: 1.15;
            margin-bottom: 6px;
        }

        .section .underline {
            width: 44px;
            height: 5px;
            border-radius: 999px;
            background: var(--accent);
            margin-bottom: 22px;
        }

        .text-block {
            color: var(--muted);
            font-size: 22px;
            margin-bottom: 16px;
        }

        .service-grid,
        .blog-grid,
        .portfolio-grid {
            display: grid;
            gap: 14px;
        }

        .service-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 16px;
        }

        .service-card,
        .blog-card,
        .portfolio-card,
        .panel {
            background: #191c23;
            border: 1px solid #2a2f3b;
            border-radius: 16px;
            padding: 16px;
        }

        .service-title {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 4px;
        }

        .service-desc {
            color: var(--muted);
            font-size: 20px;
        }

        .skills-categories {
            margin-top: 20px;
            display: grid;
            gap: 24px;
            direction: ltr;
        }

        .skill-category-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            align-items: start;
        }

        .skill-category-head {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            min-height: 42px;
            padding-left: 4px;
            width: fit-content;
        }

        .skill-category-head::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 2px;
            height: 1px;
            background: linear-gradient(90deg, var(--accent-soft), transparent 75%);
        }

        .skill-category-head iconify-icon {
            color: var(--accent);
            font-size: 18px;
            filter: drop-shadow(0 0 8px rgba(244, 198, 79, 0.45));
        }

        .skill-category-label {
            font-size: 20px;
            line-height: 1.1;
            color: #d7dbe2;
            letter-spacing: 0.6px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
        }

        .skill-items {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 12px;
            direction: ltr;
        }

        .skill-item {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 7px;
            min-height: 40px;
            border-radius: 12px;
            border: 1px solid #32384a;
            background: linear-gradient(180deg, #171b25 0%, #141822 100%);
            padding: 6px 10px;
            width: fit-content;
            max-width: 100%;
        }

        .skill-item iconify-icon {
            font-size: 28px;
            flex-shrink: 0;
            order: 1;
        }

        .skill-item span {
            color: #e7ebf2;
            font-size: 21px;
            line-height: 1.3;
            font-weight: 400;
            text-align: left;
            direction: ltr;
            order: 2;
            white-space: nowrap;
        }

        .timeline {
            margin-top: 10px;
            display: grid;
            gap: 16px;
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            right: 12px;
            top: 12px;
            bottom: 12px;
            width: 2px;
            background: #2d3340;
        }

        .timeline-item {
            position: relative;
            padding-right: 34px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            right: 5px;
            top: 8px;
            width: 16px;
            height: 16px;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 4px rgba(244, 198, 79, 0.2);
        }

        .timeline-item h4 {
            font-size: 28px;
            margin-bottom: 2px;
        }

        .timeline-period {
            display: block;
            color: var(--accent);
            font-size: 15px;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .timeline-item p {
            color: var(--muted);
            font-size: 20px;
        }

        .portfolio-filter {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .portfolio-filter span {
            border: 1px solid #353a47;
            color: #c3cad4;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
        }

        .portfolio-filter span.active {
            color: #111318;
            background: var(--accent);
            border-color: var(--accent);
            font-weight: 700;
        }

        .portfolio-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .portfolio-thumb {
            height: 130px;
            border-radius: 12px;
            background: linear-gradient(135deg, #d9dce3, #f4f6fa);
            color: #1a1d25;
            display: grid;
            place-items: center;
            font-weight: 700;
            margin-bottom: 12px;
            text-align: center;
            padding: 8px;
        }

        .portfolio-card h4 {
            font-size: 24px;
            margin-bottom: 2px;
        }

        .portfolio-card p {
            color: var(--muted);
            font-size: 18px;
            margin-bottom: 10px;
        }

        .portfolio-card a {
            font-size: 13px;
            color: var(--accent);
        }

        .blog-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .blog-card small {
            color: #9ca5b2;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .blog-card h4 {
            font-size: 32px;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .blog-card p {
            color: var(--muted);
            font-size: 20px;
        }

        .map {
            width: 100%;
            min-height: 280px;
            border-radius: 16px;
            border: 1px solid #2e3440;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .map iframe {
            width: 100%;
            height: 280px;
            border: 0;
            filter: grayscale(0.2) contrast(1.02);
        }

        .contact-form {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #343b48;
            background: #171a21;
            color: #edf0f5;
            font: inherit;
            font-size: 16px;
            padding: 12px 14px;
            direction: rtl;
            text-align: right;
            outline: none;
        }

        .contact-form .full {
            grid-column: 1 / -1;
        }

        .contact-form textarea {
            min-height: 140px;
            resize: vertical;
        }

        .submit {
            width: max-content;
            background: var(--accent);
            color: #161a21;
            border: none;
            border-radius: 10px;
            font: inherit;
            padding: 10px 20px;
            font-weight: 700;
            cursor: pointer;
        }

        .cursor-icon {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 9999;
            opacity: 1;
            visibility: hidden;
            width: 30px;
            height: 30px;
            transform: translate(-3px, -3px);
            transition: transform 0.16s ease, filter 0.16s ease;
            filter: drop-shadow(0 0 12px rgba(244, 198, 79, 0.4));
        }

        .cursor-icon.cursor-hover {
            transform: translate(-3px, -3px) scale(1.14);
            filter: drop-shadow(0 0 16px rgba(244, 198, 79, 0.65));
        }

        @media (pointer: coarse) {
            .cursor-icon {
                display: none !important;
            }
        }

        @media (max-width: 1160px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .profile-name {
                font-size: 30px;
            }
        }

        @media (max-width: 900px) {
            .service-grid,
            .portfolio-grid,
            .blog-grid,
            .skill-items,
            .contact-form {
                grid-template-columns: 1fr;
            }

            .skill-category-row {
                grid-template-columns: 1fr;
            }

            .skill-category-label {
                font-size: 18px;
            }

            .skill-category-head::after {
                opacity: 0.7;
            }

            .skill-item {
                max-width: 100%;
            }

            .skill-item span {
                white-space: normal;
            }

            .tabs {
                overflow-x: auto;
                white-space: nowrap;
            }

            .section h2 {
                font-size: 30px;
            }

            .text-block,
            .timeline-item p,
            .portfolio-card p,
            .blog-card p,
            .service-desc {
                font-size: 18px;
            }

            .service-title,
            .timeline-item h4,
            .portfolio-card h4,
            .blog-card h4 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
@php
    $profile = $portfolioData['profile'];
    $about = $portfolioData['about'];
    $services = $portfolioData['services'] ?? [];
    $skills = $portfolioData['skills'];
    $timeline = $portfolioData['timeline'];
    $projects = $portfolioData['projects'];
    $contacts = $portfolioData['contacts'];
    $currentStatus = $profile['currentStatus']['key'] ?? 'looking_for_job';
    $avatarImage = trim((string) ($profile['avatarImage'] ?? '/images/hero/pooria-hero.jpeg'));

    if ($avatarImage !== '' && !str_starts_with($avatarImage, 'http://') && !str_starts_with($avatarImage, 'https://') && !str_starts_with($avatarImage, '/')) {
        $avatarImage = '/storage/' . ltrim($avatarImage, '/');
    }

    $serviceCards = collect($services)->values();
    $skillsByCategory = collect($skills)
        ->groupBy(fn ($item) => $item['category'] ?? 'frontend');
    $skillCategoryLabels = $portfolioData['skillCategoryLabels'] ?? [
        'frontend' => 'FRONTEND',
        'backend' => 'BACKEND',
        'database' => 'DATABASE',
        'tools' => 'TOOLS',
    ];
    $blogCards = [
        [
            'date' => 'وبلاگ • ۱۸ نوامبر ۲۰۲۴',
            'title' => 'مقایسه Flutter و Flock در توسعه چندسکویی',
            'excerpt' => 'مقایسه عمیق دو رویکرد توسعه کراس پلتفرم و اثر آن روی سرعت تحویل، کیفیت کد و نگهداری طولانی مدت.',
        ],
        [
            'date' => 'وبلاگ • ۱۳ نوامبر ۲۰۲۴',
            'title' => 'تاثیر Flutter بر آینده اپ‌های چندسکویی',
            'excerpt' => 'بررسی نقش Flutter در آینده توسعه اپلیکیشن‌های چندسکویی برای موبایل، وب و دستگاه‌های هوشمند.',
        ],
    ];

    $normalizeUrl = static function (?string $value): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, 'mailto:')) {
            return $value;
        }

        return 'https://' . ltrim($value, '/');
    };

    $compactUrl = static function (?string $value): string {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = preg_replace('#^https?://#i', '', $value) ?? $value;
        $value = preg_replace('#^www\.#i', '', $value) ?? $value;

        return rtrim($value, '/');
    };

    $resolveIcon = static function (?string $icon, string $fallback): string {
        $icon = trim((string) $icon);

        if ($icon === '' || $icon === null) {
            return $fallback;
        }

        // Telegram plane is removed from UI options; normalize old persisted values.
        if ($icon === 'mdi:telegram-plane' || $icon === 'fa6-brands:telegram-plane') {
            return 'mdi:telegram';
        }

        return $icon;
    };

    $emailAddress = trim((string) ($contacts['email'] ?? ''));
    $telegramId = ltrim(trim((string) ($contacts['telegram'] ?? '')), '@');
    $githubUrl = $normalizeUrl($contacts['github'] ?? '');
    $linkedinUrl = $normalizeUrl($contacts['linkedin'] ?? '');

    $contactItems = array_values(array_filter([
        [
            'label' => 'ایمیل',
            'value' => $emailAddress,
            'display' => $emailAddress,
            'icon' => $resolveIcon($contacts['emailIcon'] ?? null, 'logos:google-gmail'),
            'href' => $emailAddress !== '' ? 'mailto:' . $emailAddress : null,
        ],
        [
            'label' => 'تلگرام',
            'value' => $telegramId !== '' ? '@' . $telegramId : '',
            'display' => $telegramId !== '' ? '@' . $telegramId : '',
            'icon' => $resolveIcon($contacts['telegramIcon'] ?? null, 'mdi:telegram'),
            'href' => $telegramId !== '' ? 'https://t.me/' . $telegramId : null,
        ],
        [
            'label' => 'گیت‌هاب',
            'value' => (string) ($contacts['github'] ?? ''),
            'display' => $compactUrl($contacts['github'] ?? ''),
            'icon' => $resolveIcon($contacts['githubIcon'] ?? null, 'mdi:github'),
            'href' => $githubUrl,
        ],
        [
            'label' => 'لینکدین',
            'value' => (string) ($contacts['linkedin'] ?? ''),
            'display' => $compactUrl($contacts['linkedin'] ?? ''),
            'icon' => $resolveIcon($contacts['linkedinIcon'] ?? null, 'mdi:linkedin'),
            'href' => $linkedinUrl,
        ],
    ], static fn (array $item): bool => trim((string) $item['display']) !== ''));
@endphp

<img class="cursor-icon" id="cursorIcon" src="/images/cursor/cursor-yellow.svg" alt="">

<div class="layout">
    <aside class="sidebar">
        <div class="avatar-wrap">
            <img src="{{ $avatarImage }}" alt="{{ $profile['name'] }}">
            <span class="status-dot status-{{ $currentStatus }}"></span>
        </div>

        <h1 class="profile-name">{{ $profile['name'] }}</h1>
        <p class="profile-role">{{ $profile['role'] }}</p>
        <p class="status-label">
            <strong>وضعیت فعلی</strong>
            <span>{{ $profile['currentStatus']['label'] ?? '' }}</span>
        </p>

        <div class="contact-list">
            @foreach($contactItems as $item)
                <div class="contact-item">
                    <div class="contact-icon">
                        <iconify-icon icon="{{ $item['icon'] }}"></iconify-icon>
                    </div>
                    <div class="contact-meta">
                        <small>{{ $item['label'] }}</small>
                        @if(!empty($item['href']))
                            <a href="{{ $item['href'] }}" target="_blank" rel="noopener noreferrer" title="{{ $item['value'] }}">{{ $item['display'] }}</a>
                        @else
                            <span title="{{ $item['value'] }}">{{ $item['display'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </aside>

    <main class="content-shell" id="app">
        <nav class="tabs" id="tabs">
            <a href="#about" class="active">درباره من</a>
            <a href="#resume">رزومه</a>
            <a href="#portfolio">نمونه کارها</a>
            <a href="#blog">وبلاگ</a>
            <a href="#contact">تماس با من</a>
        </nav>

        <section class="section" id="about">
            <h2>{{ $about['title'] ?? 'درباره من' }}</h2>
            <div class="underline"></div>
            <p class="text-block">{{ $about['paragraphOne'] }}</p>
            @if(!empty($about['paragraphTwo']))
                <p class="text-block">{{ $about['paragraphTwo'] }}</p>
            @endif

            <h2 style="font-size: clamp(26px, 2.2vw, 32px); margin-top: 22px;">در حال انجام چه کارهایی هستم</h2>
            <div class="service-grid">
                @foreach($serviceCards as $item)
                    <article class="service-card">
                        <h3 class="service-title">{{ $item['title'] }}</h3>
                        <p class="service-desc">{{ $item['description'] }}</p>
                    </article>
                @endforeach
            </div>

            <h2 style="font-size: clamp(26px, 2.2vw, 32px); margin-top: 30px;">مهارت‌ها</h2>
            <div class="skills-categories">
                @foreach($skillCategoryLabels as $categoryKey => $categoryLabel)
                    @if(($skillsByCategory[$categoryKey] ?? collect())->isNotEmpty())
                        <div class="skill-category-row">
                            <div class="skill-category-head">
                                <iconify-icon icon="mdi:hexagram"></iconify-icon>
                                <h3 class="skill-category-label">{{ $categoryLabel }}</h3>
                            </div>
                            <div class="skill-items">
                                @foreach($skillsByCategory[$categoryKey] as $item)
                                    <article class="skill-item">
                                        <span>{{ $item['title'] }}</span>
                                        @if(!empty($item['icon']))
                                            <iconify-icon icon="{{ $item['icon'] }}"></iconify-icon>
                                        @else
                                            <iconify-icon icon="mdi:star-four-points-circle"></iconify-icon>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <section class="section" id="resume">
            <h2>رزومه</h2>
            <div class="underline"></div>
            <div class="timeline">
                @foreach($timeline as $item)
                    <article class="timeline-item">
                        @if(!empty($item['period']))
                            <small class="timeline-period">{{ $item['period'] }}</small>
                        @endif
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="portfolio">
            <h2>نمونه کارها</h2>
            <div class="underline"></div>

            <div class="portfolio-filter">
                <span class="active">همه</span>
                <span>اپلیکیشن‌ها</span>
                <span>توسعه وب</span>
                <span>UI/UX</span>
            </div>

            <div class="portfolio-grid">
                @foreach($projects as $item)
                    <article class="portfolio-card">
                        <div class="portfolio-thumb">{{ $item['title'] }}</div>
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['text'] }}</p>
                        @if(!empty($item['projectUrl']))
                            <a href="{{ $item['projectUrl'] }}" target="_blank" rel="noopener noreferrer">مشاهده پروژه</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="blog">
            <h2>وبلاگ</h2>
            <div class="underline"></div>
            <div class="blog-grid">
                @foreach($blogCards as $item)
                    <article class="blog-card">
                        <small>{{ $item['date'] }}</small>
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['excerpt'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="contact">
            <h2>تماس با من</h2>
            <div class="underline"></div>

            <div class="map">
                <iframe
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps?q=Kathmandu&output=embed">
                </iframe>
            </div>

            <h2 style="font-size: clamp(24px, 2vw, 30px); margin-bottom: 12px;">فرم تماس</h2>
            <form class="contact-form" onsubmit="event.preventDefault();">
                <input type="text" placeholder="نام و نام خانوادگی">
                <input type="email" placeholder="آدرس ایمیل" dir="ltr" style="text-align:left;">
                <input type="text" class="full" placeholder="موضوع">
                <textarea class="full" placeholder="پیام شما"></textarea>
                <button class="submit" type="submit">ارسال پیام</button>
            </form>
        </section>
    </main>
</div>

<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
<script src="https://unpkg.com/lenis@1.1.16/dist/lenis.min.js"></script>
<script src="https://unpkg.com/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://unpkg.com/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
    gsap.registerPlugin(ScrollTrigger);

    const lenis = new Lenis({
        duration: 0.62,
        smoothWheel: true,
        wheelMultiplier: 1.18,
        touchMultiplier: 1.35,
        syncTouch: true,
        syncTouchLerp: 0.12,
    });

    lenis.on('scroll', () => ScrollTrigger.update());

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });

    gsap.ticker.lagSmoothing(0);

    const links = Array.from(document.querySelectorAll('#tabs a'));
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);
    const sectionsById = Object.fromEntries(sections.map((section) => [section.id, section]));
    let activeTabId = null;

    const activateTab = (rawId, options = {}) => {
        const { shouldScrollTop = false } = options;
        const fallbackId = sections[0]?.id || 'about';
        const id = sectionsById[rawId] ? rawId : fallbackId;

        if (!id) return;
        activeTabId = id;

        links.forEach((link) => {
            const isActive = link.getAttribute('href') === `#${id}`;
            link.classList.toggle('active', isActive);
        });

        sections.forEach((section) => {
            section.classList.toggle('is-hidden', section.id !== id);
        });

        if (location.hash !== `#${id}`) {
            history.replaceState(null, '', `#${id}`);
        }

        if (shouldScrollTop) {
            lenis.scrollTo(document.body, { immediate: true });
        }

        ScrollTrigger.refresh();
    };

    const initialTab = location.hash?.replace('#', '') || sections[0]?.id || 'about';
    activateTab(initialTab);

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const id = link.getAttribute('href')?.replace('#', '');
            if (!id) return;
            activateTab(id, { shouldScrollTop: true });
        });
    });

    gsap.from('.sidebar', {
        x: -36,
        autoAlpha: 0,
        duration: 0.9,
        ease: 'power2.out',
    });

    gsap.from('.tabs', {
        y: -24,
        autoAlpha: 0,
        duration: 0.8,
        delay: 0.1,
        ease: 'power2.out',
    });

    sections.forEach((section) => {
        const targets = section.querySelectorAll('h2, .underline, .text-block, .service-card, .skill-item, .timeline-item, .portfolio-card, .blog-card, .map, .contact-form');

        if (!targets.length) return;

        gsap.from(targets, {
            y: 24,
            autoAlpha: 0,
            duration: 0.7,
            stagger: 0.08,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: section,
                start: 'top 78%',
                once: true,
            },
        });
    });

    gsap.utils.toArray('.service-card, .skill-item, .portfolio-card, .blog-card, .contact-item').forEach((card) => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -4,
                borderColor: '#3c4250',
                boxShadow: '0 12px 20px rgba(0,0,0,0.24)',
                duration: 0.22,
                ease: 'power2.out',
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                borderColor: '#2a2f3b',
                boxShadow: '0 0 0 rgba(0,0,0,0)',
                duration: 0.22,
                ease: 'power2.out',
            });
        });
    });

    if (window.matchMedia('(pointer: fine)').matches) {
        const cursorIcon = document.getElementById('cursorIcon');
        const hoverTargets = document.querySelectorAll('a, button, input, textarea, .service-card, .skill-item, .portfolio-card, .blog-card, .contact-item, .tabs a');

        let hasMouseMoved = false;
        let isCursorVisible = false;
        let targetX = 0;
        let targetY = 0;
        let currentX = 0;
        let currentY = 0;
        const minLerp = 0.32;
        const maxLerp = 0.68;

        const renderCursor = () => {
            const dx = targetX - currentX;
            const dy = targetY - currentY;
            const distance = Math.hypot(dx, dy);
            const adaptiveLerp = minLerp + (maxLerp - minLerp) * Math.min(1, distance / 180);

            currentX += dx * adaptiveLerp;
            currentY += dy * adaptiveLerp;
            cursorIcon.style.transform = `translate(${currentX}px, ${currentY}px) translate(-3px, -3px)`;
            requestAnimationFrame(renderCursor);
        };
        requestAnimationFrame(renderCursor);

        window.addEventListener('mousemove', (event) => {
            const x = event.clientX;
            const y = event.clientY;

            if (!hasMouseMoved) {
                hasMouseMoved = true;
                cursorIcon.style.visibility = 'visible';
                gsap.set(cursorIcon, { autoAlpha: 1 });
                isCursorVisible = true;
                currentX = x;
                currentY = y;
            }

            targetX = x;
            targetY = y;
        });

        const hideCursor = () => {
            if (!isCursorVisible) return;
            isCursorVisible = false;
            gsap.to(cursorIcon, { autoAlpha: 0, duration: 0.06, ease: 'none' });
        };

        const showCursor = () => {
            if (hasMouseMoved && !isCursorVisible) {
                isCursorVisible = true;
                gsap.to(cursorIcon, { autoAlpha: 1, duration: 0.06, ease: 'none' });
            }
        };

        window.addEventListener('mouseleave', hideCursor);
        window.addEventListener('mouseenter', showCursor);
        window.addEventListener('blur', hideCursor);
        window.addEventListener('focus', showCursor);

        document.addEventListener('mouseout', (event) => {
            if (!event.relatedTarget && !event.toElement) {
                hideCursor();
            }
        });

        document.addEventListener('mouseover', showCursor);

        hoverTargets.forEach((target) => {
            target.addEventListener('mouseenter', () => cursorIcon.classList.add('cursor-hover'));
            target.addEventListener('mouseleave', () => cursorIcon.classList.remove('cursor-hover'));
        });
    }
</script>
</body>
</html>
