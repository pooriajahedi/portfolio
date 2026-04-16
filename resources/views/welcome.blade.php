<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پورتفولیو | {{ $portfolioData['profile']['name'] ?? 'برنامه نویس' }}</title>
    @php
        $appearance = $portfolioData['appearance'] ?? [];
        $primaryColor = $appearance['primaryColor'] ?? '#F4C64F';
        $backgroundMode = $appearance['mode'] ?? 'gradient';
        $backgroundImageUrl = trim((string) ($appearance['backgroundImageUrl'] ?? ''));
        $backgroundImageOpacity = max(0, min(1, (float) ($appearance['backgroundImageOpacity'] ?? 0.55)));
        $backgroundImageOverlayOpacity = max(0, min(1, 1 - $backgroundImageOpacity));
        $backgroundColorFrom = $appearance['backgroundColorFrom'] ?? '#101829';
        $backgroundColorTo = $appearance['backgroundColorTo'] ?? '#0B0F19';
        $hasBackgroundImage = $backgroundMode === 'image' && $backgroundImageUrl !== '';
    @endphp

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
            --accent: {{ $primaryColor }};
            --accent-soft: color-mix(in srgb, var(--accent) 22%, transparent);
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
            @if($hasBackgroundImage)
            background-image:
                linear-gradient(
                    rgba(10, 11, 15, {{ number_format($backgroundImageOverlayOpacity, 3, '.', '') }}),
                    rgba(10, 11, 15, {{ number_format($backgroundImageOverlayOpacity, 3, '.', '') }})
                ),
                url('{{ e($backgroundImageUrl) }}'),
                radial-gradient(circle at 25% 0, #171d2a 0, transparent 30%),
                linear-gradient(135deg, {{ $backgroundColorFrom }} 0%, {{ $backgroundColorTo }} 100%);
            background-repeat: no-repeat, no-repeat, no-repeat, no-repeat;
            background-position: center center, center center, center top, center center;
            background-size: cover, cover, auto, auto;
            @else
            background:
                radial-gradient(circle at 25% 0, #171d2a 0, transparent 30%),
                linear-gradient(135deg, {{ $backgroundColorFrom }} 0%, {{ $backgroundColorTo }} 100%);
            @endif
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

        .status-currently_working { background: #3b82f6; }
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

        .resume-download-btn {
            margin-top: 12px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid color-mix(in srgb, var(--accent) 62%, #2f3543);
            background: linear-gradient(
                135deg,
                color-mix(in srgb, var(--accent) 26%, #1b1f28) 0%,
                color-mix(in srgb, var(--accent) 14%, #161a22) 100%
            );
            color: #ffffff;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .1px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .28);
            transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
        }

        .resume-download-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.06);
            box-shadow: 0 12px 26px rgba(0, 0, 0, .34);
        }

        .resume-download-btn:active {
            transform: translateY(0);
        }

        .inline-icon {
            display: inline-block;
            width: 1em;
            height: 1em;
            object-fit: contain;
            flex-shrink: 0;
            vertical-align: middle;
        }

        .resume-download-btn .inline-icon {
            width: 24px;
            height: 24px;
        }

        .resume-download-btn span {
            line-height: 1.2;
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

        .contact-icon .inline-icon {
            width: 30px;
            height: 30px;
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

        .site-credit {
            margin-top: 30px;
            border-top: 1px solid #2f3441;
            padding-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: #aeb6c4;
            font-size: 13px;
        }

        .site-credit strong {
            color: #e7ebf2;
            font-weight: 600;
        }

        .site-credit-version {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #343949;
            border-radius: 999px;
            padding: 4px 10px;
            color: var(--accent);
            background: #1a1f2a;
        }

        .site-credit-version .inline-icon {
            width: 16px;
            height: 16px;
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
            padding: 18px 18px 16px;
        }

        .service-title {
            font-weight: 700;
            font-size: clamp(20px, 1.6vw, 24px);
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .service-desc {
            color: var(--muted);
            font-size: clamp(16px, 1.1vw, 18px);
            line-height: 1.85;
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

        .skill-category-head .inline-icon {
            width: 18px;
            height: 18px;
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

        .skill-item .inline-icon {
            width: 28px;
            height: 28px;
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

        .resume-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .resume-head .resume-download-btn {
            width: auto;
            margin-top: 0;
        }

        .portfolio-filter {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .portfolio-filter span {
            border: 1px solid #353a47;
            color: #c3cad4;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            user-select: none;
            cursor: pointer;
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
            overflow: hidden;
            position: relative;
        }

        .portfolio-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.28s ease;
        }

        .portfolio-thumb.has-image:hover img {
            transform: scale(1.08);
        }

        .portfolio-zoom-trigger {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(8, 10, 16, 0.28);
            color: #f6d26e;
            display: grid;
            place-items: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 2;
        }

        .portfolio-zoom-trigger .inline-icon {
            width: 34px;
            height: 34px;
            filter: drop-shadow(0 0 12px rgba(244, 198, 79, 0.55));
        }

        .portfolio-thumb.has-image:hover .portfolio-zoom-trigger {
            opacity: 1;
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

        .portfolio-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .portfolio-tag {
            display: inline-flex;
            align-items: center;
            border: 1px solid #353a47;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 12px;
            color: #cbd2dc;
            background: #1a1e27;
        }

        .image-modal {
            position: fixed;
            inset: 0;
            z-index: 1200;
            display: none;
        }

        .image-modal.is-open {
            display: block;
        }

        .image-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(4, 6, 10, 0.78);
            backdrop-filter: blur(4px);
        }

        .image-modal-dialog {
            position: relative;
            z-index: 2;
            width: min(1100px, 92vw);
            height: min(84vh, 760px);
            margin: 6vh auto 0;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #363d4b;
            background: #11141b;
            display: grid;
            place-items: center;
            box-shadow: 0 24px 44px rgba(0, 0, 0, 0.5);
        }

        .image-modal-dialog img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #0f1218;
        }

        .image-modal-close {
            position: absolute;
            top: 16px;
            left: 16px;
            width: 42px;
            height: 42px;
            z-index: 3;
            border-radius: 12px;
            border: 1px solid #3a4254;
            background: rgba(18, 21, 29, 0.9);
            color: #e8ebf1;
            display: grid;
            place-items: center;
        }

        .image-modal-close .inline-icon {
            width: 24px;
            height: 24px;
        }

        .blog-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .blog-grid.is-hidden {
            display: none;
        }

        .blog-card small {
            color: #9ca5b2;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .blog-card-image {
            margin-bottom: 12px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #343b48;
            background: #131720;
            position: relative;
            min-height: 170px;
        }

        .blog-card-image img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            display: block;
            transition: transform 0.28s ease;
        }

        .blog-card:hover .blog-card-image img {
            transform: scale(1.04);
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

        .blog-card[data-blog-open] {
            cursor: pointer;
        }

        .blog-open {
            margin-top: 10px;
            border: 1px solid #3a4252;
            background: #1b202a;
            color: #e7ebf2;
            border-radius: 10px;
            padding: 8px 12px;
            font: inherit;
            font-size: 13px;
            width: max-content;
        }

        .blog-detail {
            display: none;
        }

        .blog-detail.is-open {
            display: block;
        }

        .blog-back {
            border: 1px solid #3a4252;
            background: #1b202a;
            color: #e7ebf2;
            border-radius: 10px;
            padding: 8px 12px;
            font: inherit;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .blog-detail-image {
            margin-bottom: 14px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #343b48;
            position: relative;
            background: #121620;
        }

        .blog-detail-image img {
            width: 100%;
            max-height: 460px;
            object-fit: cover;
            display: block;
            transition: transform 0.28s ease;
        }

        .blog-detail-image:hover img {
            transform: scale(1.05);
        }

        .blog-detail-image .blog-zoom-trigger {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(8, 10, 16, 0.28);
            color: #f6d26e;
            display: grid;
            place-items: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .blog-detail-image:hover .blog-zoom-trigger {
            opacity: 1;
        }

        .blog-detail-image .blog-zoom-icon {
            width: 34px;
            height: 34px;
            filter: drop-shadow(0 0 12px rgba(244, 198, 79, 0.55));
        }

        .blog-detail-content {
            background: #191c23;
            border: 1px solid #2a2f3b;
            border-radius: 16px;
            padding: 18px;
            color: #d9dee7;
            line-height: 1.9;
        }

        .blog-detail-content h1,
        .blog-detail-content h2,
        .blog-detail-content h3,
        .blog-detail-content h4,
        .blog-detail-content h5,
        .blog-detail-content h6 {
            color: #f4f6fb;
            margin: 14px 0 6px;
            line-height: 1.45;
        }

        .blog-detail-content p {
            margin: 10px 0;
            color: #cfd5df;
            font-size: 18px;
        }

        .blog-detail-content ul,
        .blog-detail-content ol {
            margin: 10px 0;
            padding-right: 22px;
        }

        .blog-detail-content img {
            max-width: 100%;
            border-radius: 10px;
            margin: 10px 0;
        }

        .blog-empty {
            color: var(--muted);
            font-size: 18px;
            padding: 8px 0;
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

        .submit[disabled] {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .form-alert {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            display: none;
        }

        .form-alert.is-visible {
            display: block;
        }

        .form-alert-success {
            background: #163321;
            border: 1px solid #22593a;
            color: #c8f0d6;
        }

        .form-alert-error {
            background: #3a1717;
            border: 1px solid #7b2a2a;
            color: #f4cccc;
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
                font-size: 21px;
            }
        }

        @media (pointer: fine) {
            .portfolio-zoom-trigger,
            .portfolio-thumb.has-image,
            .blog-zoom-trigger,
            .blog-detail-image {
                cursor: zoom-in !important;
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
    $projectCategories = $portfolioData['projectCategories'] ?? [];
    $portfolio = $portfolioData['portfolio'] ?? [];
    $blogPosts = $portfolioData['blogPosts'] ?? [];
    $contacts = $portfolioData['contacts'];
    $appVersion = (string) config('app.version', '1.0.0');
    $currentStatus = $profile['currentStatus']['key'] ?? 'looking_for_job';
    $avatarImage = trim((string) ($profile['avatarImage'] ?? '/images/hero/pooria-hero.jpeg'));
    $resumeFileUrl = trim((string) ($profile['resumeFile'] ?? ''));
    $iconAsset = \App\Support\IconAsset::class;

    if ($avatarImage !== '' && !str_starts_with($avatarImage, 'http://') && !str_starts_with($avatarImage, 'https://') && !str_starts_with($avatarImage, '/')) {
        $avatarImage = '/storage/' . ltrim($avatarImage, '/');
    }

    if ($resumeFileUrl !== '' && !str_starts_with($resumeFileUrl, 'http://') && !str_starts_with($resumeFileUrl, 'https://') && !str_starts_with($resumeFileUrl, '/')) {
        $resumeFileUrl = '/storage/' . ltrim($resumeFileUrl, '/');
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

    $normalizeProjectUrl = static function (?string $value) use ($normalizeUrl): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return $normalizeUrl($value);
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
        @if($resumeFileUrl !== '')
            <a class="resume-download-btn" href="{{ $resumeFileUrl }}" target="_blank" rel="noopener noreferrer" download>
                {!! $iconAsset::img('mdi:file-download-outline', 'دانلود رزومه', 24) !!}
                <span>دانلود رزومه</span>
            </a>
        @endif

        <div class="contact-list">
            @foreach($contactItems as $item)
                <div class="contact-item">
                    <div class="contact-icon">
                        {!! $iconAsset::img($item['icon'], $item['label'] ?? '', 30) !!}
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

            <h2 style="font-size: clamp(26px, 2.2vw, 32px); margin-top: 22px;">بیشتر روی چه چیزهایی تمرکز دارم</h2>
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
                                {!! $iconAsset::img('mdi:hexagram', '', 18) !!}
                                <h3 class="skill-category-label">{{ $categoryLabel }}</h3>
                            </div>
                            <div class="skill-items">
                                @foreach($skillsByCategory[$categoryKey] as $item)
                                    <article class="skill-item">
                                        <span>{{ $item['title'] }}</span>
                                        @if(!empty($item['icon']))
                                            {!! $iconAsset::img($item['icon'], $item['title'], 28) !!}
                                        @else
                                            {!! $iconAsset::img('mdi:star-four-points-circle', $item['title'], 28) !!}
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
            <div class="resume-head">
                <h2>رزومه</h2>
                @if($resumeFileUrl !== '')
                    <a class="resume-download-btn" href="{{ $resumeFileUrl }}" target="_blank" rel="noopener noreferrer" download>
                        {!! $iconAsset::img('mdi:file-download-outline', 'دانلود فایل رزومه', 24) !!}
                        <span>دانلود فایل رزومه</span>
                    </a>
                @endif
            </div>
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
            <h2>{{ $portfolio['title'] ?? 'نمونه کارها' }}</h2>
            <div class="underline"></div>

            <div class="portfolio-filter">
                <span class="active" data-category="all">همه</span>
                @foreach($projectCategories as $category)
                    @if(($category['slug'] ?? '') !== 'all')
                        <span data-category="{{ $category['slug'] ?? '' }}">{{ $category['title'] ?? '' }}</span>
                    @endif
                @endforeach
            </div>

            <div class="portfolio-grid">
                @foreach($projects as $item)
                    @php
                        $projectCategorySlug = $item['category']['slug'] ?? 'uncategorized';
                        $projectLink = $normalizeProjectUrl($item['projectUrl'] ?? null);
                    @endphp
                    <article class="portfolio-card" data-category="{{ $projectCategorySlug }}">
                        <div class="portfolio-thumb {{ !empty($item['imageUrl']) ? 'has-image' : '' }}">
                            @if(!empty($item['imageUrl']))
                                <img src="{{ $item['imageUrl'] }}" alt="{{ $item['title'] }}">
                                <button
                                    type="button"
                                    class="portfolio-zoom-trigger"
                                    data-zoom-src="{{ $item['imageUrl'] }}"
                                    data-zoom-alt="{{ $item['title'] }}"
                                    aria-label="بزرگ‌نمایی تصویر {{ $item['title'] }}">
                                    {!! $iconAsset::img('mdi:magnify-plus', '', 34) !!}
                                </button>
                            @else
                                {{ $item['title'] }}
                            @endif
                        </div>
                        @if($projectLink)
                            <h4><a href="{{ $projectLink }}" target="_blank" rel="noopener noreferrer">{{ $item['title'] }}</a></h4>
                        @else
                            <h4>{{ $item['title'] }}</h4>
                        @endif
                        <p>{{ $item['text'] }}</p>
                        @if(!empty($item['tags'] ?? []))
                            <div class="portfolio-tags">
                                @foreach($item['tags'] as $tag)
                                    <span class="portfolio-tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($projectLink)
                            <a href="{{ $projectLink }}" target="_blank" rel="noopener noreferrer">مشاهده پروژه</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="blog">
            <h2>وبلاگ</h2>
            <div class="underline"></div>

            @if(empty($blogPosts))
                <p class="blog-empty">هنوز مقاله‌ای منتشر نشده است.</p>
            @else
                <div class="blog-grid" id="blogList">
                    @foreach($blogPosts as $index => $item)
                        <article class="blog-card" data-blog-open="{{ $index }}">
                            @if(!empty($item['imageUrl']))
                                <div class="blog-card-image">
                                    <img src="{{ $item['imageUrl'] }}" alt="{{ $item['title'] }}">
                                </div>
                            @endif
                            <small>{{ $item['date'] ?? '' }}</small>
                            <h4>{{ $item['title'] }}</h4>
                            <p>{{ $item['excerpt'] }}</p>
                            <button class="blog-open" type="button" data-blog-open="{{ $index }}">مشاهده مقاله</button>
                        </article>
                    @endforeach
                </div>

                <div class="blog-detail" id="blogDetail">
                    <button class="blog-back" type="button" id="blogBackButton">بازگشت به لیست مقالات</button>

                    @foreach($blogPosts as $index => $item)
                        <article class="blog-detail-item" data-blog-detail="{{ $index }}" style="display:none;">
                            <small style="display:block;color:#9ca5b2;margin-bottom:8px;">{{ $item['date'] ?? '' }}</small>
                            <h3 style="font-size:clamp(28px,2.4vw,36px);line-height:1.3;margin-bottom:12px;">{{ $item['title'] }}</h3>
                            @if(!empty($item['imageUrl']))
                                <div class="blog-detail-image">
                                    <img src="{{ $item['imageUrl'] }}" alt="{{ $item['title'] }}">
                                    <button
                                        type="button"
                                        class="blog-zoom-trigger"
                                        data-zoom-src="{{ $item['imageUrl'] }}"
                                        data-zoom-alt="{{ $item['title'] }}"
                                        aria-label="بزرگ‌نمایی تصویر {{ $item['title'] }}">
                                        {!! $iconAsset::img('mdi:magnify-plus', '', 34, 'inline-icon blog-zoom-icon') !!}
                                    </button>
                                </div>
                            @endif
                            <div class="blog-detail-content">
                                {!! $item['content'] !!}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="section" id="contact">
            <h2>تماس با من</h2>
            <div class="underline"></div>

            @if(!empty($contacts['description']))
                <p class="text-block">{{ $contacts['description'] }}</p>
            @endif

            <div id="contactFormAlert" class="form-alert" aria-live="polite"></div>

            @if(session('contact_success'))
                <div class="form-alert form-alert-success is-visible">{{ session('contact_success') }}</div>
            @endif
            @if(session('contact_error'))
                <div class="form-alert form-alert-error is-visible">{{ session('contact_error') }}</div>
            @endif
            @if($errors->any())
                <div class="form-alert form-alert-error is-visible">
                    @foreach($errors->all() as $error)
                        <p style="margin:4px 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <h2 style="font-size: clamp(24px, 2vw, 30px); margin-bottom: 12px;">فرم تماس</h2>
            <form id="contactForm" class="contact-form" action="{{ route('contact-requests.store') }}" method="POST">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="نام و نام خانوادگی" required>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="آدرس ایمیل" dir="ltr" style="text-align:left;" required>
                <input type="text" name="subject" value="{{ old('subject') }}" class="full" placeholder="موضوع" required>
                <textarea name="message" class="full" placeholder="پیام شما" required>{{ old('message') }}</textarea>
                <input
                    type="text"
                    name="company_website"
                    value=""
                    tabindex="-1"
                    autocomplete="off"
                    aria-hidden="true"
                    style="position:absolute;left:-9999px;opacity:0;height:0;width:0;pointer-events:none;">
                <button class="submit" type="submit" id="contactSubmitButton">ارسال پیام</button>
            </form>
        </section>

        <footer class="site-credit">
            <p>طراحی و توسعه این قالب توسط <strong>{{ $profile['name'] ?? 'پوریا جاهدی' }}</strong> انجام شده است.</p>
            <span class="site-credit-version">
                {!! $iconAsset::img('mdi:tag-outline', '', 16) !!}
                <span>نسخه {{ $appVersion }}</span>
            </span>
        </footer>
    </main>
</div>

<div class="image-modal" id="imageModal" aria-hidden="true">
    <div class="image-modal-backdrop" data-close-modal="true"></div>
    <button class="image-modal-close" type="button" data-close-modal="true" aria-label="بستن تصویر">
        {!! $iconAsset::img('mdi:close', 'بستن', 24) !!}
    </button>
    <div class="image-modal-dialog" role="dialog" aria-modal="true" aria-label="نمایش تصویر">
        <img id="imageModalImage" src="" alt="">
    </div>
</div>

<script src="/vendor/lenis/lenis.min.js"></script>
<script src="/vendor/gsap/gsap.min.js"></script>
<script src="/vendor/gsap/ScrollTrigger.min.js"></script>
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

    const getSectionAnimationTargets = (section) => {
        return section?.querySelectorAll('h2, .underline, .text-block, .service-card, .skill-item, .timeline-item, .portfolio-card, .blog-card, .blog-detail-item, .contact-form, .resume-download-btn, .site-credit') ?? [];
    };

    const animateSectionOnTabOpen = (id) => {
        const section = sectionsById[id];
        if (!section) return;

        const targets = Array.from(getSectionAnimationTargets(section));
        if (!targets.length) return;

        gsap.killTweensOf(targets);
        gsap.set(targets, { autoAlpha: 0, y: 18 });
        gsap.to(targets, {
            autoAlpha: 1,
            y: 0,
            duration: 0.55,
            stagger: 0.04,
            ease: 'power2.out',
            clearProps: 'transform,opacity',
        });
    };

    const activateTab = (rawId, options = {}) => {
        const { shouldScrollTop = false, animate = true } = options;
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

        if (animate) {
            requestAnimationFrame(() => animateSectionOnTabOpen(id));
        }
    };

    const initialTab = location.hash?.replace('#', '') || sections[0]?.id || 'about';
    activateTab(initialTab, { animate: true });

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const id = link.getAttribute('href')?.replace('#', '');
            if (!id) return;
            activateTab(id, { shouldScrollTop: true });
        });
    });

    const portfolioFilters = Array.from(document.querySelectorAll('.portfolio-filter [data-category]'));
    const portfolioCards = Array.from(document.querySelectorAll('.portfolio-card[data-category]'));

    const activatePortfolioFilter = (category) => {
        const selected = category || 'all';

        portfolioFilters.forEach((chip) => {
            chip.classList.toggle('active', chip.dataset.category === selected);
        });

        portfolioCards.forEach((card) => {
            const cardCategory = card.dataset.category || 'uncategorized';
            const shouldShow = selected === 'all' || cardCategory === selected;
            card.style.display = shouldShow ? '' : 'none';
        });
    };

    if (portfolioFilters.length && portfolioCards.length) {
        portfolioFilters.forEach((chip) => {
            chip.addEventListener('click', () => {
                activatePortfolioFilter(chip.dataset.category);
            });
        });
        activatePortfolioFilter('all');
    }

    const blogList = document.getElementById('blogList');
    const blogDetail = document.getElementById('blogDetail');
    const blogBackButton = document.getElementById('blogBackButton');
    const blogOpenButtons = Array.from(document.querySelectorAll('[data-blog-open]'));
    const blogDetailItems = Array.from(document.querySelectorAll('[data-blog-detail]'));

    const showBlogList = () => {
        if (!blogList || !blogDetail) return;
        blogList.classList.remove('is-hidden');
        blogDetail.classList.remove('is-open');
        blogDetailItems.forEach((item) => {
            item.style.display = 'none';
        });
    };

    const showBlogDetail = (index) => {
        if (!blogList || !blogDetail) return;
        blogList.classList.add('is-hidden');
        blogDetail.classList.add('is-open');

        blogDetailItems.forEach((item) => {
            item.style.display = item.dataset.blogDetail === String(index) ? '' : 'none';
        });
    };

    blogOpenButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            if (button.classList.contains('blog-open')) {
                event.preventDefault();
                event.stopPropagation();
            }
            showBlogDetail(button.dataset.blogOpen);
        });
    });

    if (blogBackButton) {
        blogBackButton.addEventListener('click', showBlogList);
    }

    const imageModal = document.getElementById('imageModal');
    const imageModalImage = document.getElementById('imageModalImage');
    const zoomTriggers = Array.from(document.querySelectorAll('[data-zoom-src]'));

    const closeImageModal = () => {
        if (!imageModal || !imageModalImage) return;
        imageModal.classList.remove('is-open');
        imageModal.setAttribute('aria-hidden', 'true');
        imageModalImage.setAttribute('src', '');
    };

    const openImageModal = (src, alt = '') => {
        if (!imageModal || !imageModalImage || !src) return;
        imageModalImage.setAttribute('src', src);
        imageModalImage.setAttribute('alt', alt);
        imageModal.classList.add('is-open');
        imageModal.setAttribute('aria-hidden', 'false');
    };

    zoomTriggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            openImageModal(trigger.dataset.zoomSrc, trigger.dataset.zoomAlt || '');
        });
    });

    if (imageModal) {
        imageModal.addEventListener('click', (event) => {
            const target = event.target;
            if (target instanceof HTMLElement && target.dataset.closeModal === 'true') {
                closeImageModal();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });

    const contactForm = document.getElementById('contactForm');
    const contactFormAlert = document.getElementById('contactFormAlert');
    const contactSubmitButton = document.getElementById('contactSubmitButton');

    const setContactAlert = (type, messages) => {
        if (!contactFormAlert) return;
        const normalizedMessages = Array.isArray(messages) ? messages : [messages];
        contactFormAlert.className = `form-alert is-visible ${type === 'success' ? 'form-alert-success' : 'form-alert-error'}`;
        contactFormAlert.innerHTML = normalizedMessages.map((message) => `<p style="margin:4px 0;">${message}</p>`).join('');
    };

    if (contactForm) {
        contactForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!contactSubmitButton) return;

            contactSubmitButton.disabled = true;
            contactSubmitButton.textContent = 'در حال ارسال...';
            if (contactFormAlert) {
                contactFormAlert.className = 'form-alert';
                contactFormAlert.innerHTML = '';
            }

            try {
                const formData = new FormData(contactForm);
                const response = await fetch(contactForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => null);

                if (!response.ok) {
                    const validationErrors = payload?.errors ? Object.values(payload.errors).flat() : [];
                    const fallbackMessage = payload?.message || 'ارسال فرم با خطا مواجه شد.';
                    setContactAlert('error', validationErrors.length ? validationErrors : [fallbackMessage]);
                    return;
                }

                setContactAlert('success', payload?.message || 'پیام شما با موفقیت ثبت شد.');
                contactForm.reset();
            } catch {
                setContactAlert('error', 'مشکل ارتباط با سرور رخ داد. لطفا دوباره تلاش کنید.');
            } finally {
                contactSubmitButton.disabled = false;
                contactSubmitButton.textContent = 'ارسال پیام';
            }
        });
    }

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

    document.addEventListener('contextmenu', (event) => {
        event.preventDefault();
    });
</script>
</body>
</html>
