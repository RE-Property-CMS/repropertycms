#!/usr/bin/env bash
set -euo pipefail
trap 'echo "[DEPLOY] FAILED at: $BASH_COMMAND (exit $?)" | tee -a "$LOG_FILE"' ERR

# ─────────────────────────────────────────────
# RePropertyCMS — Deployment Script
# Trigger via: bash deploy.sh
# Or via webhook: public/deploy.php
#
# Adjust the variables below for your server.
# ─────────────────────────────────────────────

SITE_DIR="/home/forge/yoursite.com"   # <-- change this
BRANCH="main"
USE_HORIZON=true
USE_NPM=true
PHP_BIN="php"                          # or /usr/bin/php8.2
COMPOSER_BIN="composer"               # or /usr/local/bin/composer
LOG_FILE="$SITE_DIR/storage/logs/deploy.log"
LOCK_FILE="/tmp/deploy_$(echo "$SITE_DIR" | md5sum | cut -d' ' -f1).lock"

# ─── Prevent concurrent deploys ───────────────
if [ -f "$LOCK_FILE" ]; then
    echo "[DEPLOY] Already running (lock: $LOCK_FILE). Aborting."
    exit 1
fi
touch "$LOCK_FILE"
cleanup() { rm -f "$LOCK_FILE"; }
trap cleanup EXIT

# ─── Start ────────────────────────────────────
STARTED_AT=$(date '+%Y-%m-%d %H:%M:%S')
echo "" | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"
echo "[DEPLOY] Started at $STARTED_AT"         | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"

cd "$SITE_DIR"

# ─── Load .env ────────────────────────────────
echo "[DEPLOY] Loading .env"
set -a
# shellcheck disable=SC1091
source .env
set +a

# ─── Git sync ─────────────────────────────────
echo "[DEPLOY] Git sync → origin/$BRANCH"
git fetch --prune origin
git reset --hard "origin/$BRANCH"
git clean -fd --exclude=".env" --exclude="storage/" --exclude="public/images/brand/"

# ─── Composer ─────────────────────────────────
echo "[DEPLOY] Composer install"
$COMPOSER_BIN install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    2>&1 | tee -a "$LOG_FILE"

# ─── Laravel: migrate + cache ─────────────────
echo "[DEPLOY] Running migrations"
$PHP_BIN artisan migrate --force 2>&1 | tee -a "$LOG_FILE"

echo "[DEPLOY] Caching config / routes / views"
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# ─── Storage link ─────────────────────────────
if [ ! -L "$SITE_DIR/public/storage" ]; then
    echo "[DEPLOY] Creating storage symlink"
    $PHP_BIN artisan storage:link
fi

# ─── Frontend build ───────────────────────────
if [ "$USE_NPM" = true ] && [ -f package.json ]; then
    echo "[DEPLOY] Building frontend assets"
    npm ci --no-audit --no-fund 2>&1 | tee -a "$LOG_FILE"
    npm run build 2>&1 | tee -a "$LOG_FILE"
fi

# ─── Queue workers ────────────────────────────
echo "[DEPLOY] Restarting queue workers"
$PHP_BIN artisan queue:restart 2>&1 | tee -a "$LOG_FILE"

# ─── Horizon ──────────────────────────────────
if [ "$USE_HORIZON" = true ]; then
    echo "[DEPLOY] Restarting Horizon"
    $PHP_BIN artisan horizon:terminate 2>&1 | tee -a "$LOG_FILE" || true
fi

# ─── File permissions ─────────────────────────
chmod -R 775 storage bootstrap/cache

# ─── Done ─────────────────────────────────────
ENDED_AT=$(date '+%Y-%m-%d %H:%M:%S')
echo "[DEPLOY] Completed at $ENDED_AT" | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"
exit 0
