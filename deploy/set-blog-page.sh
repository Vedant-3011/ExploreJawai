#!/usr/bin/env bash
# Blog posts page + static front page (front-page.html + home.html blog index).
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
wp eval-file "${SCRIPT_DIR}/set-blog-page.php"

wp rewrite flush --hard 2>/dev/null || wp rewrite flush
wp cache flush 2>/dev/null || true