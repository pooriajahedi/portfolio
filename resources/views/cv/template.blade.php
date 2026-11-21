<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>رزومه</title>
    <style>
        {{-- The PDF is laid out by mPDF: tables and floats, no flexbox or grid, and the font is registered in PHP.
             Only the browser preview needs @font-face and the paper-on-gray look. --}}
        @unless($isPdf ?? false)
        @font-face {
            font-family: 'vazirmatn';
            src: url('/fonts/vazirmatn/Vazirmatn-Regular.woff2') format('woff2');
            font-weight: 400;
        }
        @font-face {
            font-family: 'vazirmatn';
            src: url('/fonts/vazirmatn/Vazirmatn-Bold.woff2') format('woff2');
            font-weight: 700;
        }
        html { background: #e5e7eb; }
        .preview-shell { padding: 24px; }
        .sheet { width: 210mm; min-height: 297mm; margin: 0 auto; background: #ffffff; position: relative; box-shadow: 0 10px 30px rgba(15, 23, 42, .18); }
        .sheet-strip { position: absolute; top: 0; right: 0; height: 100%; width: 13mm; background: #0b1220; }
        .sheet-strip img { width: 13mm; height: 100%; display: block; object-fit: cover; object-position: {{ $codePatternPosition ?? 'left' }} center; opacity: .92; }
        .sheet-content { padding-top: 16mm; padding-bottom: 16mm; }
        @endunless

        body {
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            color: #0f172a;
            font-family: vazirmatn, sans-serif;
            font-size: 10.5pt;
            line-height: 1.75;
        }

        .sheet-content { padding-left: 8mm; padding-right: 25mm; }

        table { border-collapse: collapse; width: 100%; }
        td { vertical-align: top; text-align: right; }

        .header { border-bottom: 1px solid #0f172a; padding-bottom: 10px; margin-bottom: 12px; }
        .header-main { width: 74%; }
        .header-avatar { width: 26%; text-align: left; }
        .name { margin: 0; font-size: 23pt; font-weight: bold; line-height: 1.3; }
        .role { margin: 0 0 8px; font-size: 13pt; font-weight: bold; }
        .avatar { width: 28mm; height: 28mm; }

        .contacts td { width: 50%; padding: 1px 0; font-size: 9.5pt; color: #1f2937; vertical-align: middle; }
        .contact-label { color: #64748b; font-size: 9pt; }
        .contact-value a { color: #1f2937; text-decoration: none; }

        .section { margin-top: 11px; }
        .section-title { margin: 0 0 7px; padding-bottom: 3px; border-bottom: 1px solid #0f172a; font-size: 14pt; font-weight: bold; }
        .summary { margin: 0; text-align: right; }

        .skills td { width: 50%; padding: 1px 0 7px 8px; }
        .skill-title { font-size: 11pt; font-weight: bold; }

        .resume-item, .project-item { page-break-inside: avoid; margin-bottom: 10px; }
        .resume-title { font-size: 11.5pt; font-weight: bold; }
        .resume-period { width: 34%; font-size: 9.5pt; color: #334155; text-align: left; white-space: nowrap; }
        .resume-description { margin: 1px 0 0; color: #1f2937; text-align: right; }

        .project-title { margin: 0 0 1px; font-size: 11pt; font-weight: bold; }
        .project-description { margin: 0; color: #1f2937; text-align: right; }
        .project-tags { margin: 3px 0 0; font-size: 9.5pt; color: #334155; }
        .project-link { margin: 2px 0 0; font-size: 9.5pt; }
        .project-link a { color: #2563eb; text-decoration: underline; font-weight: bold; }
    </style>
</head>
<body>
<div class="preview-shell">
    <div class="sheet">
        @if(!empty($codePatternPath))
            <div class="sheet-strip">
                <img src="{{ $codePatternPath }}" alt="">
            </div>
        @endif

        <div class="sheet-content">
            <div class="header">
                <table>
                    <tr>
                        <td class="header-main">
                            <h1 class="name">{{ $name }}</h1>
                            <p class="role">{{ $role }}</p>

                            <table class="contacts">
                                @foreach(array_chunk($contactItems ?? [], 2) as $row)
                                    <tr>
                                        @foreach($row as $contactItem)
                                            <td>
                                                <span class="contact-label">{{ $contactItem['label'] }}</span>
                                                <span class="contact-value" dir="ltr">
                                                    @if(!empty($contactItem['href']))
                                                        <a href="{{ $contactItem['href'] }}">{{ $contactItem['value'] }}</a>
                                                    @else
                                                        {{ $contactItem['value'] }}
                                                    @endif
                                                </span>
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
                                <img src="{{ $avatarPath }}" class="avatar" alt="">
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2 class="section-title">خلاصه</h2>
                <p class="summary">{!! nl2br(e($summary !== '' ? $summary : 'خلاصه‌ای از تجربه‌ها از طریق پنل مدیریت ثبت خواهد شد.')) !!}</p>
            </div>

            <div class="section">
                <h2 class="section-title">مهارت‌ها</h2>
                @php
                    $orderedCategories = array_keys($skillCategoryLabels ?? []);
                    if ($orderedCategories === []) {
                        $orderedCategories = array_keys($skillsByCategory ?? []);
                    }
                @endphp

                <table class="skills">
                    @foreach(array_chunk($orderedCategories, 2) as $row)
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
                        <table>
                            <tr>
                                <td class="resume-title">{{ $item['title'] }}</td>
                                <td class="resume-period" dir="rtl">{{ $item['period'] ?: '---' }}</td>
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
                            <p class="project-link"><a href="{{ $project['url'] }}">مشاهده لینک پروژه</a></p>
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
