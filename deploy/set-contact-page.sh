#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
CONTENT_FILE="${SCRIPT_DIR}/content/contact-explore-jawai.html"

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

if [[ ! -f "$CONTENT_FILE" ]]; then
	echo "Missing $CONTENT_FILE" >&2
	exit 1
fi

CONTACT_ID=$(wp post list --post_type=page --name=contact --field=ID 2>/dev/null || true)
if [[ -z "$CONTACT_ID" ]]; then
	CONTACT_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Contact' --post_name=contact --porcelain)
fi

wp eval "
\$page_id = (int) '${CONTACT_ID}';
\$content = file_get_contents( '${CONTENT_FILE}' );
if ( \$content === false ) {
	WP_CLI::error( 'Could not read contact content file.' );
}
wp_update_post( array(
	'ID'           => \$page_id,
	'post_title'   => 'Contact',
	'post_name'    => 'contact',
	'post_status'  => 'publish',
	'post_excerpt' => 'Contact Explore Jawai — Jawai safari questions, blog feedback, and map of Jawai Bandh, Rajasthan.',
	'post_content' => \$content,
) );
"

THEME_IMG="$WP_PATH/wp-content/themes/explore-jawai/assets/images/jawai-dawn.jpg"
if [[ -f "$THEME_IMG" ]]; then
	ATT_ID=$(wp post list --post_type=attachment --title='Jawai dawn contact' --field=ID 2>/dev/null || true)
	if [[ -z "$ATT_ID" ]]; then
		ATT_ID=$(wp media import "$THEME_IMG" --title='Jawai dawn contact' --porcelain 2>/dev/null | tail -1 || true)
	fi
	[[ -n "$ATT_ID" ]] && wp post meta update "$CONTACT_ID" _thumbnail_id "$ATT_ID" 2>/dev/null || true
fi

MENU_ID=$(wp menu list --fields=term_id,name --format=csv | awk -F, '$2=="Main" {print $1}')
[[ -n "$MENU_ID" ]] && wp menu item add-post "$MENU_ID" "$CONTACT_ID" 2>/dev/null || true

wp cache flush 2>/dev/null || true
echo "Contact page updated (ID $CONTACT_ID)."