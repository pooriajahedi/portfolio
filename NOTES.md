# Notes

Things that are not obvious from the code. Dated; check before relying on them.

## Adding a project to the portfolio

1. **Kit.** Each project's own Claude conversation produces a `portfolio-kit/` folder: `content.json`, `cover-{fa,en}.png` and `gallery-01..06-{fa,en}.png` (1920x1200).
2. **Frame.** Every cover is drawn inside a browser mock ([ProjectCover.vue:141](resources/js/components/portfolio/ProjectCover.vue:141): 12% in from each side, 24% from the top, bleeding 12% off the bottom). Kits are designed for that: titles stay in the middle 88% of the width and the top 75% of the height. A catalog image inside a mock browser is intended, not a double-frame bug.
3. **Local import.** `storage/app/transfer/import-kit.php` (usage in its header) turns a kit into a `projects` row plus 14 files under `storage/app/public/projects/`. It sets an English slug, reuses a category whose English-name slug already exists and writes through Eloquent.
4. **Live, through the deploy (preferred).** Rows and uploads are not code, so a plain deploy does not carry them, but it does run `migrate --force` and copies `database/`. A data migration therefore ships a project: `tools/export-catalog.php --slugs=… --out=database/content/projects/NAME` writes `data.json` plus the pictures as WebP (`cwebp -q 90`: about 120 KB each, against 650 KB as PNG, so a project costs roughly 1.5 MB of git history instead of 13 MB), and the migration calls `(new ProjectCatalog(database_path('content/projects/NAME')))->publish()` ([ProjectCatalog.php](app/Support/ProjectCatalog.php), example: `2026_11_21_120000_publish_lenz_and_refresh_delsa_projects.php`). It creates missing projects, replaces the pictures and link of existing ones and deletes the pictures it replaced. Two per-item flags in `data.json` go further: `sync_text` also rewrites the title and description of an existing project, and `update_only` guarantees a project missing from that database is never created (`export-catalog.php --texts=…` writes such items; see `2026_11_21_140000_publish_vesam_and_rewrite_descriptions.php`). Running it locally does the same to this machine's database. It returns early under tests ([why](#gotchas)).
5. **Live, by hand (fallback).** `export.php --slugs=a,b` builds `storage/app/transfer/out/portfolio-transfer.zip`; upload and extract it in the home folder (outside `public_html`) and run `import.php` with the EA-PHP 8.2+ binary. It is a dry run until `--apply`, skips slugs that already exist unless `--refresh-media` is given, and `--undo --apply` reverses the last run. Tested only against a scratch database.
6. `storage/app/transfer/` is untracked: [storage/app/.gitignore](storage/app/.gitignore) ignores everything except `private/` and `public/`. The tooling (including the screenshot and catalog-image scripts under `tools/`) exists only on this machine until someone moves it, for example into `deploy/`.

## Gotchas

- **Slugs.** `projects.slug` is nullable. When it is empty the API derives one from the Persian title plus the row id ([ProjectSlug.php:20](app/Support/ProjectSlug.php:20), used at [ProjectResource.php:22](app/Http/Resources/ProjectResource.php:22)), e.g. `rbat-tlgramy-dydhban-throt-5`, so the URL depends on the id. The admin form fills the same transliteration on blur ([ProjectResource.php:55-60](app/Filament/Resources/Projects/ProjectResource.php:55)). Give new projects an explicit English slug.
- **Cache.** The public API is cached under a version key and the observers in `AppServiceProvider` bump it on Eloquent writes. Raw `DB::table()` writes do not: call `PublicApiCache::flush()` ([PublicApiCache.php:21](app/Support/PublicApiCache.php:21)). The cache store is `database`.
- **Grid layout depends on the count.** The first card spans the full row only when the visible project count is odd ([ProjectsSection.vue:43](resources/js/components/portfolio/ProjectsSection.vue:43)), and a filter chip exists only for categories that have a project ([:30](resources/js/components/portfolio/ProjectsSection.vue:30)). Adding or removing a project reflows the grid.
- **Data migrations that insert rows must skip under tests.** `RefreshDatabase` runs every migration on each test database, and `PortfolioContentSeeder` then assumes `projects` starts empty, so such a migration returns early on `app()->runningUnitTests()`. It would also write real files into `storage/app/public`.
- **Test the deploy path on a scratch database and storage.** `DB_DATABASE=scratch LARAVEL_STORAGE_PATH=/tmp/x/storage php artisan migrate --force` touches neither your data nor your uploads; `LARAVEL_STORAGE_PATH` moves `storage_path()`, and with it the `public` disk.
- **Test against a scratch database without touching `.env`.** A process env var wins over `.env`, so `DB_DATABASE=scratch php artisan migrate --force` and the importer both use the scratch database. Drop it afterwards.
- **Shell hook.** The `rtk` hook rewrites `grep` and can summarize it as "N matches in 0 files", and it prints "ok" for empty output. Use `awk '/pat/ {print FILENAME":"FNR": "$0}' file` when the actual lines matter.

## Decisions

- **Transfer as a bundle plus a server-side script**, not a local-to-production database connection and not an Artisan command (that would need a deploy before it could run). The script prints the database name and storage path before doing anything, so a wrong target is visible in the dry run.
- **Project deletion has no soft delete.** Before removing the bot project on 2026-10-11 its row and files were backed up elsewhere first. There is no undo in the admin.
- **Categories match on the slug of the English name.** The kit's "Web Application" is the existing `web-application`, so no near-duplicate chip. Edna's "Web / Education platform" created `web-education-platform`.

- **Project texts are long on purpose.** The first kit prompt capped descriptions at 400 characters, which made every project read like the same template. They are now 600 to 1,600 characters in first-person paragraphs, written from each project's own docs and git history (the facts in them can be checked there). The card shows only the first four lines; the detail page uses `whitespace-pre-line` ([ProjectDetail.vue:85](resources/js/components/portfolio/ProjectDetail.vue:85)), so a blank line becomes a paragraph break. The source of the current texts is `storage/app/transfer/kits/descriptions.py` (untracked).

## The CV PDF

- **mPDF with Vazirmatn, not Chrome.** Browsershot needs Node, Puppeteer, Chrome and PHP's `proc_open`/`escapeshellarg`, none of which the shared host allows (the live error was `Call to undefined function escapeshellarg()`). [CvPdfGenerator](app/Services/Cv/CvPdfGenerator.php) now renders [cv/template.blade.php](resources/views/cv/template.blade.php) with mPDF, which is plain PHP; the same template is the browser preview.
- **Persian joining lives in `fontdata`.** `useOTL` and `useKashida` only count inside the font's own `fontdata` entry; on the constructor mPDF ignores them without an error and every letter is drawn isolated. A preview at normal size looks fine either way, so [CvPdfGeneratorTest](tests/Feature/CvPdfGeneratorTest.php) reads the glyph codes out of an uncompressed PDF: seven "ب" must use three codes (initial, medial, final), and a control proves the check fails when shaping is off.
- **mPDF caches font metrics by font name** in `storage/app/mpdf-temp/ttfontdata`. A copy generated once without the shaping data is reused by later runs, whatever the config says; delete that folder after changing font settings. The control in the test uses its own temp folder and font key for this reason.
- **Template limits.** Tables, no flexbox or grid; `font-weight: bold`, not `700`, picks the bold file; `border-radius` on images is ignored (the avatar is circle-masked with GD); `position: fixed` takes space in the flow, so the side strip is a watermark image on every page; SVG icons render unreliably, so the contact rows use text labels.
- **Fonts** are `resources/fonts/vazirmatn/*.ttf` (the site's own copies are woff2; the PDF needs files on disk). Project texts on the CV are cut to their first sentences ([projectSummary](app/Services/Cv/CvPdfGenerator.php)), because the full portfolio descriptions would turn two pages into four.

## Checked and rejected

- A fourth `cover_type` for frameless catalog images: not needed, see point 2 above.
- `database/migrations/2026_08_23_120400_seed_english_content.php:31` mentions the deleted bot project by title, but it only updates matching rows, so deleting the project is harmless.

## Open

- The Delsa Gallery images show `delsagallery.test` in their browser bar, so the public site shows a local address. Regenerate them with the real domain or accept it.
- `public/favicon.ico` is 0 bytes and there is no apple-touch-icon; the SVG favicons cover modern browsers only.
