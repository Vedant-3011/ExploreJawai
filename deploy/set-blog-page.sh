#!/usr/bin/env bash
# Blog posts page + static front page (front-page.html + home.html blog index).
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

HOME_ID=$(wp post list --post_type=page --name=home --field=ID 2>/dev/null | tr -cd '0-9' || true)
if [[ -z "$HOME_ID" ]]; then
	HOME_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Home' --post_name=home --post_content='<!-- Front page layout is in front-page.html -->' \
		--porcelain | tr -cd '0-9')
fi

BLOG_ID=$(wp post list --post_type=page --name=blog --field=ID 2>/dev/null | tr -cd '0-9' || true)
if [[ -z "$BLOG_ID" ]]; then
	BLOG_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Blog' --post_name=blog --post_content='<!-- Blog layout is in home.html -->' \
		--porcelain | tr -cd '0-9')
fi

if [[ -z "$HOME_ID" || -z "$BLOG_ID" ]]; then
	echo "Could not resolve Home or Blog page IDs." >&2
	exit 1
fi

wp option update show_on_front 'page'
wp option update page_on_front "$HOME_ID"
wp option update page_for_posts "$BLOG_ID"

wp rewrite flush --hard 2>/dev/null || wp rewrite flush
wp cache flush 2>/dev/null || true
echo "Reading settings: front=$HOME_ID posts=$BLOG_ID"