#!/usr/bin/env bash
# Create/update Plan Your Trip page (local or server). Requires WP-CLI.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH="${WP_PATH:-.}"
CONTENT_FILE="${SCRIPT_DIR}/content/plan-your-trip.html"

if [[ "$WP_PATH" == "." ]]; then
	WP_PATH="$(cd "${SCRIPT_DIR}/../public_html" && pwd)"
fi

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

if [[ ! -f "$CONTENT_FILE" ]]; then
	echo "Missing $CONTENT_FILE" >&2
	exit 1
fi

PAGE_ID=$(wp post list --post_type=page --name=plan-your-trip --field=ID 2>/dev/null || true)
if [[ -z "$PAGE_ID" ]]; then
	PAGE_ID=$(wp post create --post_type=page --post_status=publish \
		--post_title='Plan Your Trip' --post_name=plan-your-trip --porcelain)
fi

CONTENT=$(cat "$CONTENT_FILE")
wp post update "$PAGE_ID" --post_title='Plan Your Trip' --post_name=plan-your-trip \
	--post_status=publish --post_content="$CONTENT"

echo "Plan Your Trip page ID: $PAGE_ID"
wp cache flush 2>/dev/null || true