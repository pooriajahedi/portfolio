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
# public/build, which only ever hold versioned files. Only cp, mv and rm are
# needed: rsync is not installed on shared hosting.

set -euo pipefail

CORE_DIR="${CORE_DIR:-/home/rfjowhrq/laravel_portfolio_core}"
WEB_DIR="${WEB_DIR:-/home/rfjowhrq/public_html}"
LOG_FILE="${LOG_FILE:-$(dirname "$CORE_DIR")/logs/portfolio-deploy.log}"

# Folders that hold nothing but versioned code, so removed files may be removed here too.
CODE_DIRS=(app config lang resources routes database/migrations database/seeders database/factories)

# Top-level entries of the repository that never go into the core folder.
SKIP_ENTRIES=(.git .cpanel.yml deploy .env public storage vendor node_modules tests .idea .github)

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Run again with the output piped through tee. Process substitution would be
# shorter, but cPanel's deploy shell has no /dev/fd.
if [ -z "${DEPLOY_LOGGED:-}" ]; then
    mkdir -p "$(dirname "$LOG_FILE")"
    set +e
    DEPLOY_LOGGED=1 bash "${BASH_SOURCE[0]}" "$@" 2>&1 | tee -a "$LOG_FILE"
    exit "${PIPESTATUS[0]}"
fi

log() { printf '\n==> %s\n' "$*"; }
fail() { printf '\nDEPLOY FAILED: %s\n' "$*" >&2; exit 1; }

in_list() {
    local needle="$1" item
    shift
    for item in "$@"; do
        [ "$item" = "$needle" ] && return 0
    done
    return 1
}

# Replace a directory with a copy of another, swapping it in last so the old
# one is only gone once the new one is complete.
replace_dir() {
    local source="$1" target="$2"

    rm -rf "$target.new" "$target.old"
    mkdir -p "$(dirname "$target")"
    cp -a "$source" "$target.new"
    [ ! -e "$target" ] || mv "$target" "$target.old"
    mv "$target.new" "$target"
    rm -rf "$target.old"
}

# Copy the application's files over the existing ones, leaving anything the
# server owns (see SKIP_ENTRIES) and anything the repository lacks untouched.
copy_application() {
    local entry name file

    for entry in "$REPO_DIR"/* "$REPO_DIR"/.[!.]*; do
        [ -e "$entry" ] || continue
        name="$(basename "$entry")"

        in_list "$name" "${SKIP_ENTRIES[@]}" && continue
        in_list "$name" "${CODE_DIRS[@]}" && continue

        if [ "$name" = bootstrap ]; then
            # bootstrap/cache belongs to the server.
            mkdir -p "$CORE_DIR/bootstrap"
            for file in "$entry"/*; do
                [ "$(basename "$file")" = cache ] || cp -a "$file" "$CORE_DIR/bootstrap/"
            done
            continue
        fi

        cp -a "$entry" "$CORE_DIR/"
    done

    # Code folders are replaced, so a file removed from the repository goes too.
    for name in "${CODE_DIRS[@]}"; do
        [ -d "$REPO_DIR/$name" ] && replace_dir "$REPO_DIR/$name" "$CORE_DIR/$name"
    done

    return 0
}

# Copy public/ into the document root, keeping the host's own .htaccess.
copy_public() {
    local entry name

    for entry in "$REPO_DIR/public"/* "$REPO_DIR/public"/.[!.]*; do
        [ -e "$entry" ] || continue
        name="$(basename "$entry")"

        in_list "$name" .htaccess build && continue

        cp -a "$entry" "$WEB_DIR/"
    done

    # Hashed build assets: drop the ones the new build no longer references.
    replace_dir "$REPO_DIR/public/build" "$WEB_DIR/build"
    [ -f "$WEB_DIR/.htaccess" ] || cp "$REPO_DIR/public/.htaccess" "$WEB_DIR/.htaccess"
}

find_php() {
    local candidate
    for candidate in "${PHP_BIN:-}" \
        /opt/cpanel/ea-php84/root/usr/bin/php \
        /opt/cpanel/ea-php83/root/usr/bin/php \
        /opt/cpanel/ea-php82/root/usr/bin/php \
        /opt/alt/php84/usr/bin/php \
        /opt/alt/php83/usr/bin/php \
        /opt/alt/php82/usr/bin/php; do
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

find_composer() {
    local candidate
    for candidate in "${COMPOSER_BIN:-}" \
        /opt/cpanel/composer/bin/composer \
        /usr/local/bin/composer \
        /usr/bin/composer \
        "$(dirname "$CORE_DIR")/composer.phar" \
        "${HOME:-}/bin/composer" \
        "${HOME:-}/.composer/composer.phar"; do
        if [ -n "$candidate" ] && [ -f "$candidate" ]; then
            echo "$candidate"
            return
        fi
    done

    command -v composer || true
}

# A phar or PHP script must run on the PHP we picked, not whatever `php` is in PATH.
run_composer() {
    if head -n 1 "$COMPOSER" | grep -qi php || [[ "$COMPOSER" == *.phar ]]; then
        "$PHP" "$COMPOSER" "$@"
    else
        "$COMPOSER" "$@"
    fi
}

COMPOSER="$(find_composer)"
[ -n "$COMPOSER" ] || fail "Composer not found. Set COMPOSER_BIN."
echo "PHP:      $PHP ($("$PHP" -r 'echo PHP_VERSION;'))"
echo "Composer: $COMPOSER"

[ -f "$REPO_DIR/public/build/manifest.json" ] \
    || fail "public/build is missing from the repository. Run 'npm run build' and commit public/build."
[ -d "$CORE_DIR" ] || mkdir -p "$CORE_DIR"
[ -d "$WEB_DIR" ] || mkdir -p "$WEB_DIR"

log "Copying application code to $CORE_DIR"
copy_application

mkdir -p "$CORE_DIR/bootstrap/cache" \
    "$CORE_DIR/storage/app/public" \
    "$CORE_DIR/storage/framework/cache/data" \
    "$CORE_DIR/storage/framework/sessions" \
    "$CORE_DIR/storage/framework/views" \
    "$CORE_DIR/storage/logs"

log "Copying public files to $WEB_DIR"
copy_public

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
run_composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

log "Migrating and rebuilding caches"
[ -f .env ] || fail "$CORE_DIR/.env is missing. Create it once on the server."
"$PHP" artisan migrate --force
"$PHP" artisan optimize:clear
"$PHP" artisan optimize

log "Deploy finished"
