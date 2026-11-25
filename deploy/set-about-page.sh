#!/usr/bin/env bash
# Push About page content from deploy/content/about-explore-jawai.html (prototype explore-jawai.html).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
CONTENT_FILE="${SCRIPT_DIR}/content/about-explore-jawai.html"

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

if [[ ! -f "$CONTENT_FILE" ]]; then
	echo "Missing $CONTENT_FILE" >&2
	exit 1
fi

ABOUT_ID=$(wp post list --post_type=page --name=about --field=ID 2>/dev/null | tr -cd '0-9' || true)
if [[ -z "$ABOUT_ID" ]]; then
	ABOUT_ID=$(wp post list --post_type=page --search='about' --field=ID 2>/dev/null | head -1 | tr -cd '0-9' || true)
fi
if [[ -z "$ABOUT_ID" ]]; then
	ABOUT_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Explore Jawai' --post_name=about --porcelain | tr -cd '0-9')
fi
if [[ -z "$ABOUT_ID" || "$ABOUT_ID" == "0" ]]; then
	echo "Could not resolve About page ID." >&2
	exit 1
fi

export ABOUT_PAGE_ID="$ABOUT_ID"
export CONTENT_FILE
wp eval-file "${SCRIPT_DIR}/update-about-content.php"

wp cache flush 2>/dev/null || true
echo "About page updated (ID $ABOUT_ID)."