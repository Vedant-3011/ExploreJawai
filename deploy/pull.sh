#!/usr/bin/env bash
set -euo pipefail

SITE_ROOT="/var/www/html/domain/explorejawai.com"
REPO_DIR="${SITE_ROOT}/repo"
PUBLIC="${SITE_ROOT}/public_html"

THEME_FUNCTIONS="${REPO_DIR}/public_html/wp-content/themes/explore-jawai/functions.php"
if [[ -f "${REPO_DIR}/deploy/DO-NOT-DEPLOY-EDITORIAL.txt" ]] \
	&& [[ -f "${THEME_FUNCTIONS}" ]] \
	&& grep -q "EXPLORE_JAWAI_EDITORIAL', true" "${THEME_FUNCTIONS}" 2>/dev/null; then
	echo "ABORT: Editorial staging build on main — owner approval required before production deploy."
	echo "Merge only after approval or set EXPLORE_JAWAI_EDITORIAL to false."
	exit 1
fi

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

bash "${REPO_DIR}/deploy/flush-fse-overrides.sh" "${PUBLIC}"
bash "${REPO_DIR}/deploy/ensure-journal-posts.sh" "${PUBLIC}"
bash "${REPO_DIR}/deploy/set-site-logo.sh"

echo "Deploy complete: ${PUBLIC}"