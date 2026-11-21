# Notes

Things that are not obvious from the code. Dated; check before relying on them.

## Adding a project to the portfolio

1. **Kit.** Each project's own Claude conversation produces a `portfolio-kit/` folder: `content.json`, `cover-{fa,en}.png` and `gallery-01..06-{fa,en}.png` (1920x1200).
2. **Frame.** Every cover is drawn inside a browser mock ([ProjectCover.vue:141](resources/js/components/portfolio/ProjectCover.vue:141): 12% in from each side, 24% from the top, bleeding 12% off the bottom). Kits are designed for that: titles stay in the middle 88% of the width and the top 75% of the height. A catalog image inside a mock browser is intended, not a double-frame bug.
3. **Local import.** `storage/app/transfer/import-kit.php` (usage in its header) turns a kit into a `projects` row plus 14 files under `storage/app/public/projects/`. It sets an English slug, reuses a category whose English-name slug already exists and writes through Eloquent.
4. **Live, through the deploy (preferred).** Rows and uploads are not code, so a plain deploy does not carry them, but it does run `migrate --force` and copies `database/`. A data migration therefore ships a project: `tools/export-catalog.php --slugs=… --out=database/content/projects/NAME` writes `data.json` plus the pictures as WebP (`cwebp -q 90`: about 120 KB each, against 650 KB as PNG, so a project costs roughly 1.5 MB of git history instead of 13 MB), and the migration calls `(new ProjectCatalog(database_path('content/projects/NAME')))->publish()` ([ProjectCatalog.php](app/Support/ProjectCatalog.php), example: `2026_11_21_120000_publish_lenz_and_refresh_delsa_projects.php`). It creates missing projects, replaces the pictures and link of existing ones and deletes the pictures it replaced. Running it locally does the same to this machine's database. It returns early under tests ([why](#gotchas)).
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

## Checked and rejected

- A fourth `cover_type` for frameless catalog images: not needed, see point 2 above.
- `database/migrations/2026_08_23_120400_seed_english_content.php:31` mentions the deleted bot project by title, but it only updates matching rows, so deleting the project is harmless.

## Open

- The Delsa Gallery images show `delsagallery.test` in their browser bar, so the public site shows a local address. Regenerate them with the real domain or accept it.
- `public/favicon.ico` is 0 bytes and there is no apple-touch-icon; the SVG favicons cover modern browsers only.
