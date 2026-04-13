<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پورتفولیو | برنامه نویس</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6f8fa;
            --bg-soft: #ffffff;
            --text: #1f2328;
            --muted: #656d76;
            --border: #d0d7de;
            --accent: #0969da;
            --accent-soft: #ddf4ff;
            --hero-glow: rgba(9, 105, 218, 0.14);
            --shadow: 0 12px 32px rgba(31, 35, 40, 0.08);
        }

        [data-theme="dark"] {
            --bg: #0d1117;
            --bg-soft: #161b22;
            --text: #e6edf3;
            --muted: #8b949e;
            --border: #30363d;
            --accent: #58a6ff;
            --accent-soft: rgba(56, 139, 253, 0.18);
            --hero-glow: rgba(88, 166, 255, 0.18);
            --shadow: 0 18px 40px rgba(0, 0, 0, 0.4);
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
            font-family: "Vazirmatn", sans-serif;
            background: radial-gradient(circle at 20% 0, var(--hero-glow), transparent 40%), var(--bg);
            color: var(--text);
            line-height: 1.8;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1100px, 92%);
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(10px);
            background: color-mix(in srgb, var(--bg) 85%, transparent);
            border-bottom: 1px solid var(--border);
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 72px;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 18px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 6px var(--accent-soft);
        }

        .nav {
            display: flex;
            gap: 16px;
            align-items: center;
            color: var(--muted);
            font-size: 14px;
        }

        .nav a:hover {
            color: var(--text);
        }

        .theme-btn {
            border: 1px solid var(--border);
            background: var(--bg-soft);
            color: var(--text);
            border-radius: 10px;
            padding: 8px 12px;
            font: inherit;
            cursor: pointer;
        }

        .hero {
            padding: 72px 0 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            align-items: stretch;
        }

        .card {
            background: var(--bg-soft);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 26px;
        }

        .hero h1 {
            font-size: clamp(28px, 5vw, 44px);
            line-height: 1.35;
            margin-bottom: 16px;
        }

        .hero p {
            color: var(--muted);
            font-size: 17px;
            margin-bottom: 22px;
        }

        .badge-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge {
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            color: var(--muted);
            background: var(--bg);
        }

        .hero-side {
            display: grid;
            gap: 14px;
        }

        .stat {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            background: var(--bg-soft);
        }

        .stat strong {
            display: block;
            font-size: 26px;
            line-height: 1.2;
        }

        .stat span {
            color: var(--muted);
            font-size: 13px;
        }

        .section {
            padding: 24px 0;
        }

        .section-title {
            font-size: 26px;
            margin-bottom: 14px;
        }

        .section-desc {
            color: var(--muted);
            margin-bottom: 18px;
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .skill-item {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            background: var(--bg);
        }

        .skill-item strong {
            display: block;
            margin-bottom: 6px;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-item {
            border-right: 3px solid var(--accent);
            padding: 12px 14px;
            background: color-mix(in srgb, var(--bg-soft) 85%, var(--accent-soft));
            border-radius: 8px;
        }

        .timeline-item h3 {
            font-size: 17px;
            margin-bottom: 4px;
        }

        .timeline-item p {
            color: var(--muted);
            font-size: 14px;
        }

        .projects {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .project {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            background: var(--bg-soft);
        }

        .project h3 {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .project p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag {
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 12px;
            padding: 5px 10px;
            color: var(--muted);
            background: var(--bg);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
        }

        .contact-list {
            list-style: none;
            display: grid;
            gap: 10px;
            color: var(--muted);
        }

        .contact-list strong {
            color: var(--text);
            margin-left: 6px;
        }

        footer {
            text-align: center;
            padding: 32px 0 50px;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 920px) {
            .hero,
            .projects,
            .grid-2,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .nav {
                display: none;
            }
        }
    </style>
</head>
<body>
<div id="app"></div>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
    const { createApp, ref, computed, onMounted } = Vue;
    const initialData = @json($portfolioData);

    createApp({
        setup() {
            const currentYear = new Date().getFullYear();
            const theme = ref('light');

            const profile = initialData.profile;
            const about = initialData.about;
            const skills = initialData.skills;
            const timeline = initialData.timeline;
            const projects = initialData.projects;
            const contacts = initialData.contacts;

            const themeLabel = computed(() => theme.value === 'dark' ? 'روشن' : 'تاریک');

            const applyTheme = (nextTheme) => {
                theme.value = nextTheme;
                document.documentElement.setAttribute('data-theme', nextTheme);
                localStorage.setItem('portfolio-theme', nextTheme);
            };

            const toggleTheme = () => {
                applyTheme(theme.value === 'dark' ? 'light' : 'dark');
            };

            onMounted(() => {
                const saved = localStorage.getItem('portfolio-theme');
                const preferredDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                applyTheme(saved || (preferredDark ? 'dark' : 'light'));
            });

            return {
                currentYear,
                profile,
                about,
                skills,
                timeline,
                projects,
                contacts,
                toggleTheme,
                themeLabel
            };
        },
        template: `
        <header class="topbar">
            <div class="container topbar-inner">
                <div class="brand">
                    <span class="dot"></span>
                    <span>@{{ profile.name }}</span>
                </div>
                <nav class="nav">
                    <a href="#hero">خانه</a>
                    <a href="#about">درباره من</a>
                    <a href="#skills">مهارت ها</a>
                    <a href="#resume">رزومه</a>
                    <a href="#projects">پروژه ها</a>
                    <a href="#contact">تماس</a>
                </nav>
                <button class="theme-btn" @click="toggleTheme">حالت @{{ themeLabel }}</button>
            </div>
        </header>

        <main class="container">
            <section id="hero" class="hero">
                <article class="card">
                    <h1>@{{ profile.headline }}</h1>
                    <p class="section-desc">@{{ profile.role }}</p>
                    <p>@{{ profile.intro }}</p>
                    <div class="badge-row">
                        <span class="badge" v-for="item in profile.highlights" :key="item">@{{ item }}</span>
                    </div>
                </article>
                <aside class="hero-side">
                    <div class="stat">
                        <strong>+10</strong>
                        <span>سال تجربه مداوم</span>
                    </div>
                    <div class="stat">
                        <strong>180</strong>
                        <span>جدول دیتابیس بهینه شده</span>
                    </div>
                    <div class="stat">
                        <strong>1M</strong>
                        <span>کاربر فعال در محصولات</span>
                    </div>
                </aside>
            </section>

            <section id="about" class="section">
                <div class="card">
                    <h2 class="section-title">@{{ about.title }}</h2>
                    <p class="section-desc">@{{ about.paragraphOne }}</p>
                    <p class="section-desc" v-if="about.paragraphTwo">@{{ about.paragraphTwo }}</p>
                </div>
            </section>

            <section id="skills" class="section">
                <h2 class="section-title">مهارت ها</h2>
                <div class="grid grid-2">
                    <article class="skill-item" v-for="skill in skills" :key="skill.title">
                        <strong>@{{ skill.title }}</strong>
                        <span>@{{ skill.description }}</span>
                    </article>
                </div>
            </section>

            <section id="resume" class="section">
                <h2 class="section-title">رزومه</h2>
                <div class="timeline">
                    <article class="timeline-item" v-for="item in timeline" :key="item.title">
                        <h3>@{{ item.title }}</h3>
                        <p>@{{ item.text }}</p>
                    </article>
                </div>
            </section>

            <section id="projects" class="section">
                <h2 class="section-title">پروژه ها</h2>
                <div class="projects">
                    <article class="project" v-for="project in projects" :key="project.title">
                        <h3>@{{ project.title }}</h3>
                        <p>@{{ project.text }}</p>
                        <div class="tags">
                            <span class="tag" v-for="tag in project.tags" :key="tag">@{{ tag }}</span>
                        </div>
                        <a v-if="project.projectUrl" :href="project.projectUrl" target="_blank" rel="noopener noreferrer" class="tag" style="display: inline-block; margin-top: 10px;">
                            مشاهده پروژه
                        </a>
                    </article>
                </div>
            </section>

            <section id="contact" class="section">
                <div class="contact-grid">
                    <article class="card">
                        <h2 class="section-title">@{{ contacts.title }}</h2>
                        <p class="section-desc">@{{ contacts.description }}</p>
                    </article>
                    <article class="card">
                        <ul class="contact-list">
                            <li><strong>ایمیل:</strong> @{{ contacts.email }}</li>
                            <li><strong>GitHub:</strong> @{{ contacts.github }}</li>
                            <li><strong>LinkedIn:</strong> @{{ contacts.linkedin }}</li>
                            <li><strong>Telegram:</strong> @{{ contacts.telegram }}</li>
                        </ul>
                    </article>
                </div>
            </section>
        </main>

        <footer>
            @{{ currentYear }} - طراحی شده با Vue.js
        </footer>
        `
    }).mount('#app');
</script>
</body>
</html>
