#!/usr/bin/env bash
set -euo pipefail

SITE_ROOT="/var/www/html/domain/explorejawai.com"
REPO_DIR="${SITE_ROOT}/repo"
PUBLIC="${SITE_ROOT}/public_html"

# Preserves DB content: rsync never touches wp-config.php, uploads, or posts.
cd "${REPO_DIR}"
git fetch origin
git checkout main
git pull --ff-only origin main
# origin on server: git@github-explorejawai:Vedant-3011/ExploreJawai.git

rsync -a --delete \
  --exclude 'wp-config.php' \
  --exclude 'wp-content/cache/' \
  --exclude 'wp-content/uploads/' \
  "${REPO_DIR}/public_html/" "${PUBLIC}/"

chown -R www-data:www-data "${PUBLIC}"

bash "${REPO_DIR}/deploy/repair-block-theme.sh" "${PUBLIC}"
bash "${REPO_DIR}/deploy/ensure-journal-posts.sh" "${PUBLIC}"
bash "${REPO_DIR}/deploy/set-site-logo.sh"
bash "${REPO_DIR}/deploy/verify-homepage.sh" "https://explorejawai.com/"

echo "Deploy complete: ${PUBLIC}"