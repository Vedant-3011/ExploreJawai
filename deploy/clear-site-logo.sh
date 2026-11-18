#!/usr/bin/env bash
# Remove header image logo — text-only branding in theme header.
set -euo pipefail
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
wp option delete site_logo 2>/dev/null || true
wp --allow-root --path="$WP_PATH" cache flush 2>/dev/null || true
echo "Site logo option cleared."