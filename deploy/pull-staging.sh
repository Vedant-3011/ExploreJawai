#!/usr/bin/env bash
set -euo pipefail

SITE_ROOT="/var/www/html/domain/explorejawai.com"
REPO_DIR="${SITE_ROOT}/repo"
PUBLIC="${SITE_ROOT}/staging_public_html"
BRANCH="staging"

cd "${REPO_DIR}"
git fetch origin
git checkout "${BRANCH}"
git pull --ff-only origin "${BRANCH}"

rsync -a --delete \
  --exclude 'wp-config.php' \
  --exclude 'wp-content/cache/' \
  --exclude 'wp-content/uploads/' \
  "${REPO_DIR}/public_html/" "${PUBLIC}/"

chown -R www-data:www-data "${PUBLIC}"

echo "Staging deploy complete: ${PUBLIC}"