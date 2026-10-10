#!/usr/bin/env bash
#
# Deploys the app from cPanel's "Git Version Control" (see ../.cpanel.yml).
#
#   <repo>/ (everything except public/)  ->  $CORE_DIR   code, vendor, storage
#   <repo>/public/                       ->  $WEB_DIR    document root
#
# Never overwritten on the server: .env, storage/, vendor/ (Composer owns it),
# and the .htaccess that cPanel's MultiPHP writes into the document root.
# Server-only files are never deleted, except inside the code folders and
# public/build, which only ever hold generated or versioned files.

set -euo pipefail

CORE_DIR="${CORE_DIR:-/home/rfjowhrq/laravel_portfolio_core}"
WEB_DIR="${WEB_DIR:-/home/rfjowhrq/public_html}"
LOG_FILE="${LOG_FILE:-$(dirname "$CORE_DIR")/logs/portfolio-deploy.log}"

# Folders that hold nothing but versioned code, so removed files may be removed here too.
CODE_DIRS=(app config lang resources routes database/migrations database/seeders database/factories)

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

mkdir -p "$(dirname "$LOG_FILE")"
exec > >(tee -a "$LOG_FILE") 2>&1

log() { printf '\n==> %s\n' "$*"; }
fail() { printf '\nDEPLOY FAILED: %s\n' "$*" >&2; exit 1; }

find_php() {
    local candidate
    for candidate in "${PHP_BIN:-}" \
        /opt/cpanel/ea-php84/root/usr/bin/php \
        /opt/cpanel/ea-php83/root/usr/bin/php \
        /opt/cpanel/ea-php82/root/usr/bin/php; do
        if [ -n "$candidate" ] && [ -x "$candidate" ]; then
            echo "$candidate"
            return
        fi
    done

    command -v php || true
}

printf '\n######## Deploy %s  commit %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$(git -C "$REPO_DIR" rev-parse --short HEAD 2>/dev/null || echo '?')"

PHP="$(find_php)"
[ -n "$PHP" ] || fail "No PHP binary found. Set PHP_BIN to an EA-PHP 8.2+ binary."
"$PHP" -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' || fail "$PHP is older than PHP 8.2."

COMPOSER="${COMPOSER_BIN:-/opt/cpanel/composer/bin/composer}"
[ -x "$COMPOSER" ] || COMPOSER="$(command -v composer || true)"
[ -n "$COMPOSER" ] || fail "Composer not found. Set COMPOSER_BIN."

command -v rsync >/dev/null || fail "rsync is not available on this server."
[ -f "$REPO_DIR/public/build/manifest.json" ] \
    || fail "public/build is missing from the repository. Run 'npm run build' and commit public/build."
[ -d "$CORE_DIR" ] || mkdir -p "$CORE_DIR"
[ -d "$WEB_DIR" ] || mkdir -p "$WEB_DIR"

log "Syncing application code to $CORE_DIR"
rsync -a \
    --exclude='/.git/' --exclude='/.cpanel.yml' --exclude='/deploy/' \
    --exclude='/.env' --exclude='/public' --exclude='/storage/' --exclude='/vendor/' \
    --exclude='/bootstrap/cache/' --exclude='/node_modules/' --exclude='/tests/' \
    --exclude='/.idea/' --exclude='/.github/' \
    "$REPO_DIR/" "$CORE_DIR/"

for dir in "${CODE_DIRS[@]}"; do
    [ -d "$REPO_DIR/$dir" ] || continue
    rsync -a --delete "$REPO_DIR/$dir/" "$CORE_DIR/$dir/"
done

mkdir -p "$CORE_DIR/bootstrap/cache" \
    "$CORE_DIR/storage/app/public" \
    "$CORE_DIR/storage/framework/cache/data" \
    "$CORE_DIR/storage/framework/sessions" \
    "$CORE_DIR/storage/framework/views" \
    "$CORE_DIR/storage/logs"

log "Syncing public files to $WEB_DIR"
rsync -a --exclude='/.htaccess' --exclude='/build/' "$REPO_DIR/public/" "$WEB_DIR/"
# Hashed build assets: drop the ones the new build no longer references.
rsync -a --delete "$REPO_DIR/public/build/" "$WEB_DIR/build/"
[ -f "$WEB_DIR/.htaccess" ] || cp "$REPO_DIR/public/.htaccess" "$WEB_DIR/.htaccess"

log "Linking public/ and storage"
# Laravel resolves public_path() to <core>/public. Pointing that at the document
# root makes Vite, the CV generator and artisan all see the real files.
if [ -L "$CORE_DIR/public" ]; then
    ln -sfn "$WEB_DIR" "$CORE_DIR/public"
elif [ -e "$CORE_DIR/public" ]; then
    backup="$CORE_DIR/public.bak.$(date +%Y%m%d%H%M%S)"
    mv "$CORE_DIR/public" "$backup"
    ln -s "$WEB_DIR" "$CORE_DIR/public"
    echo "Existing $CORE_DIR/public moved to $backup"
else
    ln -s "$WEB_DIR" "$CORE_DIR/public"
fi

if [ -d "$WEB_DIR/storage" ] && [ ! -L "$WEB_DIR/storage" ]; then
    echo "WARNING: $WEB_DIR/storage is a real directory; the storage link was not created."
else
    ln -sfn "$CORE_DIR/storage/app/public" "$WEB_DIR/storage"
fi

cd "$CORE_DIR"
export COMPOSER_HOME="${COMPOSER_HOME:-$(dirname "$CORE_DIR")/.composer}"
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=-1

log "Installing PHP dependencies"
"$PHP" "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

log "Migrating and rebuilding caches"
[ -f .env ] || fail "$CORE_DIR/.env is missing. Create it once on the server."
"$PHP" artisan migrate --force
"$PHP" artisan optimize:clear
"$PHP" artisan optimize

log "Deploy finished"
