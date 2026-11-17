#!/usr/bin/env bash
# Push Jawai-only About page content into WordPress (repo file is source of truth).
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
		--post_title='About Explore Jawai' --post_name=about --porcelain)
fi

wp eval "
\$about_id = (int) '${ABOUT_ID}';
\$content = file_get_contents( '${CONTENT_FILE}' );
if ( \$content === false ) {
	WP_CLI::error( 'Could not read about content file.' );
}
wp_update_post( array(
	'ID'           => \$about_id,
	'post_title'   => 'About Explore Jawai',
	'post_name'    => 'about',
	'post_status'  => 'publish',
	'post_excerpt' => 'Independent travel blog for Jawai Bandh — leopards, Rabari culture, and responsible safari guides in Rajasthan.',
	'post_content' => \$content,
) );
"

# Optional featured image for SEO / social (not shown when using in-content hero).
THEME_IMG="$WP_PATH/wp-content/themes/explore-jawai/assets/images/jawai-landscape.jpg"
if [[ -f "$THEME_IMG" ]]; then
	ATT_ID=$(wp post list --post_type=attachment --title='Jawai landscape about' --field=ID 2>/dev/null || true)
	if [[ -z "$ATT_ID" ]]; then
		ATT_ID=$(wp media import "$THEME_IMG" --title='Jawai landscape about' --porcelain 2>/dev/null | tail -1 || true)
	fi
	[[ -n "$ATT_ID" ]] && wp post meta update "$ABOUT_ID" _thumbnail_id "$ATT_ID" 2>/dev/null || true
fi

# Ensure About is in Main menu
MENU_ID=$(wp menu list --fields=term_id,name --format=csv | awk -F, '$2=="Main" {print $1}')
if [[ -n "$MENU_ID" ]]; then
	wp menu item list "$MENU_ID" --format=ids 2>/dev/null | grep -q . || {
		wp menu item add-custom "$MENU_ID" Home https://explorejawai.com/ 2>/dev/null || true
	}
	wp menu item add-post "$MENU_ID" "$ABOUT_ID" 2>/dev/null || true
fi

wp cache flush 2>/dev/null || true
echo "About page updated (ID $ABOUT_ID)."