#!/usr/bin/env bash
# Fix define( 'FS_METHOD', direct ); → define( 'FS_METHOD', 'direct' );
set -euo pipefail
PUBLIC="${1:-/var/www/html/domain/explorejawai.com/public_html}"
WP_CONFIG="${PUBLIC}/wp-config.php"
[[ -f "${WP_CONFIG}" ]] || exit 0
if grep -qE "FS_METHOD',\s*direct\s*\)" "${WP_CONFIG}" || grep -qE 'FS_METHOD",\s*direct\s*\)' "${WP_CONFIG}"; then
	sed -i -E "s/(define\\(\\s*'FS_METHOD'\\s*,\\s*)direct(\\s*\\)\\s*;)/\\1'direct'\\2/" "${WP_CONFIG}"
	sed -i -E 's/(define\\(\\s*"FS_METHOD"\\s*,\\s*)direct(\\s*\\)\\s*;)/\\1'\''direct'\''\\2/' "${WP_CONFIG}"
	echo "Repaired FS_METHOD in wp-config.php"
	grep FS_METHOD "${WP_CONFIG}"
else
	echo "No broken FS_METHOD line found"
fi