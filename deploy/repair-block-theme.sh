#!/usr/bin/env bash
# Reset customized block templates/parts so theme files render (fixes blank front end).
set -euo pipefail

PUBLIC="${1:-/var/www/html/domain/explorejawai.com/public_html}"

if ! command -v wp &>/dev/null || [[ ! -f "${PUBLIC}/wp-config.php" ]]; then
	echo "repair-block-theme: skip (no wp-cli or WordPress)"
	exit 0
fi

wp() {
	command wp --allow-root --path="${PUBLIC}" "$@"
}

for type in wp_template wp_template_part; do
	for id in $(wp post list --post_type="${type}" --format=ids 2>/dev/null || true); do
		[[ -z "${id}" ]] && continue
		wp post delete "${id}" --force 2>/dev/null || true
		echo "Deleted ${type} ID ${id}"
	done
done

if ! wp theme is-installed twentytwentyfour 2>/dev/null; then
	wp theme install twentytwentyfour --activate=false 2>/dev/null || true
fi

wp theme activate explore-jawai 2>/dev/null || true
wp rewrite flush 2>/dev/null || true
wp cache flush 2>/dev/null || true

echo "repair-block-theme: done"