#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/home/u643761585/domains/beige-snail-607901.hostingersite.com/public_html}"
BRANCH="${BRANCH:-master}"
LOG_FILE="${LOG_FILE:-$APP_DIR/storage/logs/auto-deploy.log}"
LOCK_DIR="${LOCK_DIR:-$APP_DIR/storage/framework/cache/auto-deploy.lock}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

mkdir -p "$(dirname "$LOG_FILE")" "$(dirname "$LOCK_DIR")"

log() {
    printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S %z')" "$*" >> "$LOG_FILE"
}

if ! mkdir "$LOCK_DIR" 2>/dev/null; then
    log "Skipped: another deploy is already running."
    exit 0
fi

cleanup() {
    rm -rf "$LOCK_DIR"
}
trap cleanup EXIT

cd "$APP_DIR"

log "Checking origin/$BRANCH."
git fetch origin "$BRANCH" --quiet

LOCAL_SHA="$(git rev-parse HEAD)"
REMOTE_SHA="$(git rev-parse "origin/$BRANCH")"

if [[ "$LOCAL_SHA" == "$REMOTE_SHA" ]]; then
    log "No changes. Current commit $LOCAL_SHA."
    exit 0
fi

log "Deploying $LOCAL_SHA -> $REMOTE_SHA."

git reset --hard "origin/$BRANCH" --quiet

if [[ -f composer.json ]]; then
    "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction --quiet
fi

if [[ ! -L public/storage && -d storage/app/public ]]; then
    rm -rf public/storage
    ln -s ../storage/app/public public/storage || true
fi

"$PHP_BIN" artisan config:clear --quiet || true
"$PHP_BIN" artisan route:clear --quiet || true
"$PHP_BIN" artisan view:clear --quiet || true
"$PHP_BIN" artisan optimize --quiet

log "Deployed $REMOTE_SHA."
