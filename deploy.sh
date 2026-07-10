#!/usr/bin/env bash
#
# Deploy script for Hostinger Business (shared hosting, SSH).
# Run it ON THE SERVER from the app directory:  bash deploy.sh
#
# One-time setup before the first run:
#   1. SSH into Hostinger (hPanel -> Advanced -> SSH Access).
#   2. Clone the repo into APP_DIR (a folder OUTSIDE the public web root), e.g.:
#        git clone <your-repo-url> ~/deploy/assets
#   3. Copy your production .env into APP_DIR (never committed to git) and set:
#        APP_ENV=production, APP_DEBUG=false, real APP_URL (the subdomain),
#        DB_* (from hPanel MySQL), MAIL_* (Gmail App Password).
#   4. Point the SUBDOMAIN's document root at APP_DIR/public. Two ways:
#        (a) In hPanel, set the subdomain's "document root" to
#            .../deploy/assets/public   (preferred), OR
#        (b) Symlink it: enable LINK_PUBLIC=1 below and set PUBLIC_ROOT.
#   5. php artisan key:generate   (once, if APP_KEY is empty)
#
set -euo pipefail

# ============================ CONFIG (edit these) ============================
APP_DIR="$HOME/deploy/assets"                 # where this repo is cloned
BRANCH="main"                                  # branch to deploy
PHP_BIN="php"                                  # try "php8.3" or a full path if needed
COMPOSER="composer"                            # or: "php ~/composer.phar"

# Optional: symlink the subdomain's public_html -> APP_DIR/public.
# Leave LINK_PUBLIC=0 if you set the document root directly in hPanel instead.
LINK_PUBLIC=0
PUBLIC_ROOT="$HOME/domains/sub.example.com/public_html"   # subdomain doc root
# ============================================================================

cd "$APP_DIR"

# Always try to lift maintenance mode again, even if a step fails.
trap '$PHP_BIN artisan up >/dev/null 2>&1 || true' EXIT

echo "==> Pulling latest ($BRANCH)"
git fetch --all --prune
git reset --hard "origin/$BRANCH"     # server should have no local edits

echo "==> Installing PHP dependencies (production)"
$COMPOSER install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Entering maintenance mode"
$PHP_BIN artisan down --retry=15 || true

echo "==> Running migrations"
$PHP_BIN artisan migrate --force

echo "==> Rebuilding caches"
$PHP_BIN artisan config:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "==> Storage symlink"
$PHP_BIN artisan storage:link 2>/dev/null || true

if [ "$LINK_PUBLIC" = "1" ]; then
    echo "==> Linking subdomain document root -> $APP_DIR/public"
    rm -rf "$PUBLIC_ROOT"
    ln -s "$APP_DIR/public" "$PUBLIC_ROOT"
fi

echo "==> Fixing writable permissions"
chmod -R ug+rw storage bootstrap/cache

echo "==> Leaving maintenance mode"
$PHP_BIN artisan up

echo "==> Deploy complete."
