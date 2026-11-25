#!/usr/bin/env bash
# Install Contact Form 7 and seed the Explore Jawai enquiry form (once).
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
REPO_DIR="${REPO_DIR:-/var/www/html/domain/explorejawai.com/repo}"
PHP_SEED="${REPO_DIR}/deploy/php/create-explore-jawai-cf7.php"

wp() {
	command wp --allow-root --path="${WP_PATH}" "$@"
}

cd "${WP_PATH}"

if ! wp plugin is-installed contact-form-7; then
	wp plugin install contact-form-7 --activate
else
	wp plugin activate contact-form-7 2>/dev/null || true
fi

if [[ ! -f "${PHP_SEED}" ]]; then
	echo "Missing ${PHP_SEED}"
	exit 1
fi

wp eval-file "${PHP_SEED}"

echo "Contact Form 7 ready for /contact/ — edit forms in wp-admin under Contact."