#!/usr/bin/env bash
# Draft the Plan Your Trip page if it exists (content kept in DB, not public).
set -euo pipefail

WP_PATH="${1:-/var/www/html/domain/explorejawai.com/public_html}"

if ! command -v wp &>/dev/null || [[ ! -f "${WP_PATH}/wp-config.php" ]]; then
	exit 0
fi

PAGE_ID="$(wp --allow-root --path="${WP_PATH}" post list --post_type=page --name=plan-your-trip --field=ID 2>/dev/null || true)"
if [[ -n "${PAGE_ID}" ]]; then
	wp --allow-root --path="${WP_PATH}" post update "${PAGE_ID}" --post_status=draft
	echo "Plan Your Trip page ${PAGE_ID} set to draft."
fi