#!/usr/bin/env bash
# Recreate starter journal posts when none are published (e.g. after accidental trash).
set -euo pipefail

WP_PATH="${1:-/var/www/html/domain/explorejawai.com/public_html}"

if ! command -v wp &>/dev/null; then
	exit 0
fi

wp() {
	command wp --allow-root --path="$WP_PATH" "$@"
}

count="$(wp post list --post_type=post --post_status=publish --format=count 2>/dev/null || echo 0)"
if [[ "${count:-0}" -gt 0 ]]; then
	echo "Journal OK: ${count} published post(s)"
	exit 0
fi

echo "No published posts — running seed-explore-jawai (remove marker to allow full seed)..."
MARKER="${WP_PATH}/.explore-jawai-seeded"
rm -f "$MARKER" 2>/dev/null || true
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export WP_PATH
bash "${SCRIPT_DIR}/seed-explore-jawai.sh"