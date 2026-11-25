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

ABOUT_ID=$(wp post list --post_type=page --name=about --field=ID 2>/dev/null || true)
if [[ -z "$ABOUT_ID" ]]; then
	ABOUT_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Explore Jawai' --post_name=about --porcelain)
fi

POST_CONTENT="$(cat "$CONTENT_FILE")"
wp post update "$ABOUT_ID" \
	--post_title='Explore Jawai' \
	--post_name='about' \
	--post_status='publish' \
	--post_excerpt='Discover Jawai granite landscape, wildlife, Jawai Bandh, birdlife and Rabari pastoral culture in Pali, Rajasthan.' \
	--post_content="$POST_CONTENT"

THEME_IMG="$WP_PATH/wp-content/themes/explore-jawai/assets/images/jawai-granite-hills-leopard-hero.webp"
if [[ -f "$THEME_IMG" ]]; then
	ATT_ID=$(wp post list --post_type=attachment --title='Jawai landscape about' --field=ID 2>/dev/null || true)
	if [[ -z "$ATT_ID" ]]; then
		ATT_ID=$(wp media import "$THEME_IMG" --title='Jawai landscape about' --porcelain 2>/dev/null | tail -1 || true)
	fi
	[[ -n "$ATT_ID" ]] && wp post meta update "$ABOUT_ID" _thumbnail_id "$ATT_ID" 2>/dev/null || true
fi

wp cache flush 2>/dev/null || true
echo "About page updated (ID $ABOUT_ID)."