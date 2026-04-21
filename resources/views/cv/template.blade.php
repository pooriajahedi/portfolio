<!doctype html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <style>
        @if($isPdf ?? false)
        @page {
            margin: 16mm 0;
        }
        @else
        @page {
            margin: 0;
        }
        @endif

        @font-face {
            font-family: 'pinar';
            src: url('{{ $fontMediumSrc ?? '/fonts/pinar/Pinar-DS1-FD-Medium.ttf' }}') format('truetype');
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'pinar';
            src: url('{{ $fontBoldSrc ?? '/fonts/pinar/Pinar-DS1-FD-Bold.ttf' }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #0f172a;
            background: {{ ($isPdf ?? false) ? '#ffffff' : '#e5e7eb' }};
            font-family: pinar, xbriyaz, dejavusans, sans-serif;
            font-size: 11pt;
            line-height: 1.8;
        }

        .preview-shell {
            @if(!($isPdf ?? false))
            min-height: 100vh;
            padding: 24px;
            box-sizing: border-box;
            @endif
        }

        .sheet {
            @if(!($isPdf ?? false))
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .18);
            @else
            width: 100%;
            margin: 0;
            @endif
            position: relative;
            background: #ffffff;
            box-sizing: border-box;
            overflow: hidden;
        }

        .sheet-strip {
            width: 50px;
            background: #0b1220;
            overflow: hidden;
            pointer-events: none;
        }

        @if($isPdf ?? false)
        .sheet-strip {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        @else
        .sheet-strip {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            z-index: 1;
        }
        @endif

        .sheet-strip img {
            display: block;
            width: 50px;
            min-width: 50px;
            max-width: 50px;
            height: 100%;
            object-fit: cover;
            object-position: {{ $codePatternPosition ?? 'left' }} center;
            opacity: .92;
        }

        .sheet-content {
            position: relative;
            z-index: 2;
            padding: 16mm 28px 16mm 28px;
            padding-right: 96px;
            box-sizing: border-box;
        }

        @if($isPdf ?? false)
        .sheet-content {
            padding-top: 0;
            padding-bottom: 0;
        }
        @endif

        .header-table,
        .contacts-table,
        .skills-table,
        .resume-main {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            border-bottom: 1px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .header-main {
            width: 72%;
            vertical-align: top;
            text-align: right;
        }

        .header-avatar {
            width: 28%;
            vertical-align: baseline;
            text-align: left;
            justify-items: end;
        }

        .name {
            margin: 0;
            font-size: 24pt;
            font-weight: 700;
            line-height: 1.2;
        }

        .role {
            margin: 3px 0 10px;
            font-size: 14pt;
            font-weight: 700;
        }

        .avatar {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            border: 1px solid #d1d5db;
            object-fit: cover;
            background: #e2e8f0;
        }

        .contacts-table td {
            width: 50%;
            padding: 2px 0;
            vertical-align: middle;
            font-size: 10pt;
            color: #1f2937;
        }

        .contact-item {
            direction: ltr;
            text-align: left;
            white-space: nowrap;
        }

        .contact-link {
            color: inherit;
            text-decoration: none;
        }

        .contact-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            margin-right: 6px;
            vertical-align: middle;
            flex: 0 0 20px;
        }

        .contact-icon img {
            width: 20px;
            height: 20px;
            object-fit: contain;
            display: block;
        }

        .section {
            margin-top: 12px;
        }

        .section-title {
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #0f172a;
            font-size: 15pt;
            font-weight: 700;
        }

        .summary {
            margin: 0;
            text-align: justify;
            white-space: pre-line;
        }

        .skills-table td {
            width: 50%;
            vertical-align: top;
            padding: 2px 8px 8px 0;
        }

        table.skills-table {
            direction: ltr;
            text-align: left;
        }

        .skill-title {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .resume-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .resume-main td {
            vertical-align: top;
        }

        .resume-title {
            font-size: 12pt;
            font-weight: 700;
            text-align: right;
        }

        .resume-period {
            font-size: 10pt;
            color: #334155;
            white-space: nowrap;
            direction: ltr;
            text-align: left;
        }

        .resume-description {
            margin: 2px 0 0;
            text-align: justify;
            color: #1f2937;
        }

        .project-item {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .project-title {
            margin: 0 0 2px;
            font-size: 11.5pt;
            font-weight: 700;
        }

        .project-description {
            margin: 0;
            color: #1f2937;
            text-align: justify;
        }

        .project-tags {
            margin-top: 4px;
            font-size: 9.5pt;
            color: #334155;
        }

        .project-link {
            margin-top: 3px;
            font-size: 9.8pt;
        }

        .project-link a {
            color: #2563eb;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="preview-shell">
    <div class="sheet">
        @if(!empty($codePatternPath))
            <div class="sheet-strip">
                <img src="{{ $codePatternPath }}" alt="code pattern">
            </div>
        @endif

        <div class="sheet-content">
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-main">
                            <h1 class="name">{{ $name }}</h1>
                            <p class="role">{{ $role }}</p>

                            @php $chunks = array_chunk($contactItems ?? [], 2); @endphp

                            <table class="contacts-table">
                                @foreach($chunks as $row)
                                    <tr>
                                        @foreach($row as $contactItem)
                                            <td>
                                                <div class="contact-item">
                                                    <span class="contact-icon">
                                                        @if(!empty($contactItem['icon_src']))
                                                            <img src="{{ $contactItem['icon_src'] }}" alt="{{ $contactItem['label'] }}">
                                                        @else
                                                            <span>{{ $contactItem['icon'] ?? '•' }}</span>
                                                        @endif
                                                    </span>
                                                    @if(!empty($contactItem['href']))
                                                        <a class="contact-link" href="{{ $contactItem['href'] }}">{{ $contactItem['value'] }}</a>
                                                    @else
                                                        <span>{{ $contactItem['value'] }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                        @if(count($row) === 1)
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td class="header-avatar">
                            @if($avatarPath)
                                <img src="{{ $avatarPath }}" class="avatar" alt="avatar">
                            @else
                                <div class="avatar"></div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2 class="section-title">خلاصه</h2>
                <p class="summary">{{ $summary !== '' ? $summary : 'خلاصه‌ای از تجربه‌ها از طریق پنل مدیریت ثبت خواهد شد.' }}</p>
            </div>

            <div class="section">
                <h2 class="section-title">مهارت‌ها</h2>
                @php
                    $orderedCategories = array_keys($skillCategoryLabels ?? []);
                    if ($orderedCategories === []) {
                        $orderedCategories = array_keys($skillsByCategory ?? []);
                    }
                    $categoryRows = array_chunk($orderedCategories, 2);
                @endphp

                <table class="skills-table">
                    @foreach($categoryRows as $row)
                        <tr>
                            @foreach($row as $categoryKey)
                                <td>
                                    <div class="skill-title">{{ $skillCategoryLabels[$categoryKey] ?? strtoupper($categoryKey) }}</div>
                                    <div>{{ $skillsByCategory[$categoryKey] ?? '---' }}</div>
                                </td>
                            @endforeach
                            @if(count($row) === 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="section">
                <h2 class="section-title">سوابق حرفه‌ای</h2>
                @forelse($resumeItems as $item)
                    <div class="resume-item">
                        <table class="resume-main">
                            <tr>
                                <td class="resume-title">{{ $item['title'] }}</td>
                                <td class="resume-period">{{ $item['period'] ?: '---' }}</td>
                            </tr>
                        </table>
                        <p class="resume-description">{{ $item['description'] }}</p>
                    </div>
                @empty
                    <p class="resume-description">هنوز سابقه‌ای ثبت نشده است.</p>
                @endforelse
            </div>

            <div class="section">
                <h2 class="section-title">نمونه کارها</h2>
                @forelse(($projects ?? []) as $project)
                    <div class="project-item">
                        <h3 class="project-title">{{ $project['title'] }}</h3>
                        <p class="project-description">{{ $project['description'] !== '' ? $project['description'] : '---' }}</p>
                        @if(!empty($project['tags']) && is_array($project['tags']))
                            <p class="project-tags">تکنولوژی‌ها: {{ implode('، ', $project['tags']) }}</p>
                        @endif
                        @if(!empty($project['url']))
                            <p class="project-link">
                                <a href="{{ $project['url'] }}">مشاهده لینک پروژه ↗</a>
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="resume-description">هنوز نمونه‌کاری ثبت نشده است.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
</body>
</html>
