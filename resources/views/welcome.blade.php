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
            scroll-behavior: smooth;
        }

        body {
            font-family: "Pinar", sans-serif;
            background: radial-gradient(circle at 25% 0, #171d2a 0, transparent 30%), var(--bg);
            color: var(--text);
            line-height: 1.8;
            min-height: 100vh;
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
            text-align: center;
            color: var(--accent);
            font-size: 12px;
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
            grid-template-columns: 44px 1fr;
            gap: 10px;
            align-items: center;
            background: #171a21;
            border: 1px solid #252a35;
            border-radius: 14px;
            padding: 10px;
        }

        .contact-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #1e222c;
            color: var(--accent);
            font-size: 18px;
        }

        .contact-meta small {
            display: block;
            color: #8e97a6;
            font-size: 11px;
            line-height: 1.2;
        }

        .contact-meta span {
            display: block;
            color: #e7e9ee;
            font-size: 15px;
            line-height: 1.5;
            direction: ltr;
            text-align: left;
        }

        .socials {
            margin-top: 14px;
            display: flex;
            justify-content: center;
            gap: 12px;
            color: #b3bbc8;
        }

        .socials a {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #2b3040;
            display: grid;
            place-items: center;
            background: #1b1e26;
            font-size: 13px;
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

        .section h2 {
            font-size: 48px;
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
        .skills-grid,
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
        .skill-card,
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

        .skills-grid {
            margin-top: 20px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .skill-card {
            text-align: center;
            min-height: 120px;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 600;
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
            .skills-grid,
            .contact-form {
                grid-template-columns: 1fr;
            }

            .tabs {
                overflow-x: auto;
                white-space: nowrap;
            }

            .section h2 {
                font-size: 36px;
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
    $skills = $portfolioData['skills'];
    $timeline = $portfolioData['timeline'];
    $projects = $portfolioData['projects'];
    $contacts = $portfolioData['contacts'];
    $currentStatus = $profile['currentStatus']['key'] ?? 'looking_for_job';

    $serviceCards = collect($skills)->take(4)->values();
    $blogCards = [
        [
            'date' => 'Blog • Nov 18, 2024',
            'title' => 'Flutter Vs. Flock: Cross-Platform Evaluation',
            'excerpt' => 'مقایسه عمیق دو رویکرد توسعه کراس پلتفرم و اثر آن روی سرعت تحویل، کیفیت کد و نگهداری طولانی مدت.',
        ],
        [
            'date' => 'Blog • Nov 13, 2024',
            'title' => 'Flutter\'s Impact On Future Cross-Platform Apps',
            'excerpt' => 'بررسی نقش Flutter در آینده توسعه اپلیکیشن‌های چندسکویی برای موبایل، وب و دستگاه‌های هوشمند.',
        ],
    ];
@endphp

<div class="layout">
    <aside class="sidebar">
        <div class="avatar-wrap">
            <img src="/images/hero/pooria-hero.jpeg" alt="{{ $profile['name'] }}">
            <span class="status-dot status-{{ $currentStatus }}"></span>
        </div>

        <h1 class="profile-name">{{ $profile['name'] }}</h1>
        <p class="profile-role">{{ $profile['role'] }}</p>
        <p class="status-label">{{ $profile['currentStatus']['label'] ?? '' }}</p>

        <div class="contact-list">
            <div class="contact-item">
                <div class="contact-icon">✉</div>
                <div class="contact-meta">
                    <small>EMAIL</small>
                    <span>{{ $contacts['email'] }}</span>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">☎</div>
                <div class="contact-meta">
                    <small>PHONE</small>
                    <span>{{ $contacts['telegram'] }}</span>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">⌖</div>
                <div class="contact-meta">
                    <small>GITHUB</small>
                    <span>{{ $contacts['github'] }}</span>
                </div>
            </div>
        </div>

        <div class="socials">
            <a href="https://{{ ltrim($contacts['linkedin'] ?? '', 'https://') }}" target="_blank" rel="noopener noreferrer">in</a>
            <a href="https://{{ ltrim($contacts['github'] ?? '', 'https://') }}" target="_blank" rel="noopener noreferrer">gh</a>
            <a href="https://t.me/{{ ltrim($contacts['telegram'] ?? '', '@') }}" target="_blank" rel="noopener noreferrer">tg</a>
        </div>
    </aside>

    <main class="content-shell" id="app">
        <nav class="tabs" id="tabs">
            <a href="#about" class="active">About</a>
            <a href="#resume">Resume</a>
            <a href="#portfolio">Portfolio</a>
            <a href="#blog">Blog</a>
            <a href="#contact">Contact</a>
        </nav>

        <section class="section" id="about">
            <h2>About Me</h2>
            <div class="underline"></div>
            <p class="text-block">{{ $about['paragraphOne'] }}</p>
            @if(!empty($about['paragraphTwo']))
                <p class="text-block">{{ $about['paragraphTwo'] }}</p>
            @endif

            <h2 style="font-size: 42px; margin-top: 22px;">What I'm Doing</h2>
            <div class="service-grid">
                @foreach($serviceCards as $item)
                    <article class="service-card">
                        <h3 class="service-title">{{ $item['title'] }}</h3>
                        <p class="service-desc">{{ $item['description'] }}</p>
                    </article>
                @endforeach
            </div>

            <h2 style="font-size: 42px; margin-top: 30px;">Skills</h2>
            <div class="skills-grid">
                @foreach($skills as $item)
                    <article class="skill-card">{{ $item['title'] }}</article>
                @endforeach
            </div>
        </section>

        <section class="section" id="resume">
            <h2>Resume</h2>
            <div class="underline"></div>
            <div class="timeline">
                @foreach($timeline as $item)
                    <article class="timeline-item">
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="portfolio">
            <h2>Portfolio</h2>
            <div class="underline"></div>

            <div class="portfolio-filter">
                <span class="active">All</span>
                <span>Applications</span>
                <span>Web development</span>
                <span>UI/UX</span>
            </div>

            <div class="portfolio-grid">
                @foreach($projects as $item)
                    <article class="portfolio-card">
                        <div class="portfolio-thumb">{{ $item['title'] }}</div>
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['text'] }}</p>
                        @if(!empty($item['projectUrl']))
                            <a href="{{ $item['projectUrl'] }}" target="_blank" rel="noopener noreferrer">View Project</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="blog">
            <h2>Blog</h2>
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
            <h2>Contact</h2>
            <div class="underline"></div>

            <div class="map">
                <iframe
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps?q=Kathmandu&output=embed">
                </iframe>
            </div>

            <h2 style="font-size: 36px; margin-bottom: 12px;">Contact Form</h2>
            <form class="contact-form" onsubmit="event.preventDefault();">
                <input type="text" placeholder="Full name">
                <input type="email" placeholder="Email address" dir="ltr" style="text-align:left;">
                <input type="text" class="full" placeholder="Subject">
                <textarea class="full" placeholder="Your Message"></textarea>
                <button class="submit" type="submit">Send Message</button>
            </form>
        </section>
    </main>
</div>

<script>
    const links = Array.from(document.querySelectorAll('#tabs a'));
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    const setActive = (id) => {
        links.forEach((link) => {
            link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

        if (visible?.target?.id) {
            setActive(visible.target.id);
        }
    }, {
        rootMargin: '-30% 0px -55% 0px',
        threshold: [0.2, 0.4, 0.6],
    });

    sections.forEach((section) => observer.observe(section));
</script>
</body>
</html>
