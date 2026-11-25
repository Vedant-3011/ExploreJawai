#!/usr/bin/env bash
# Ensure Contact page exists and uses theme template (content in page-contact.html).
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

CONTACT_ID=$(wp post list --post_type=page --name=contact --field=ID 2>/dev/null | tr -cd '0-9' || true)
if [[ -z "$CONTACT_ID" ]]; then
	CONTACT_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Contact' --post_name=contact \
		--post_content='<!-- Layout: wp-content/themes/explore-jawai/templates/page-contact.html -->' \
		--porcelain | tr -cd '0-9')
fi

if [[ -z "$CONTACT_ID" ]]; then
	echo "Could not resolve Contact page ID." >&2
	exit 1
fi

wp post update "$CONTACT_ID" --post_title='Contact' --post_name=contact \
	--post_content='<!-- Layout: wp-content/themes/explore-jawai/templates/page-contact.html -->' \
	--post_excerpt='Contact Explore Jawai with questions about visiting Jawai, wildlife, nature, culture and travel planning.'

wp cache flush 2>/dev/null || true
echo "Contact page ready (ID $CONTACT_ID), template page-contact.html."