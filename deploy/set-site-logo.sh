#!/usr/bin/env bash
set -euo pipefail
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
LOGO="${WP_PATH}/wp-content/themes/explore-jawai/assets/images/explore-jawai-logo-transparent.png"
if [[ ! -f "$LOGO" ]]; then
	LOGO="${WP_PATH}/wp-content/themes/explore-jawai/assets/images/explore-jawai-logo-white.png"
fi
if [[ ! -f "$LOGO" ]]; then
	LOGO="${WP_PATH}/wp-content/themes/explore-jawai/assets/images/explore-jawai-logo.png"
fi
if [[ ! -f "$LOGO" ]]; then
	echo "Logo file missing; skip site logo."
	exit 0
fi
ATT=$(wp --allow-root --path="$WP_PATH" media import "$LOGO" --title="Explore Jawai logo" --porcelain 2>/dev/null | tail -1)
# Always refresh from theme file so header/footer updates match Site Icon/logo in admin.
if [[ -n "$ATT" ]]; then
	wp --allow-root --path="$WP_PATH" option update site_logo "$ATT"
	echo "Site logo set to attachment $ATT"
fi
wp --allow-root --path="$WP_PATH" cache flush 2>/dev/null || true