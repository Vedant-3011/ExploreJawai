#!/usr/bin/env bash
# Drop customized block templates so git theme files are used (home, header).
set -euo pipefail

PUBLIC="${1:-/var/www/html/domain/explorejawai.com/public_html}"

if ! command -v wp &>/dev/null; then
	echo "wp-cli not found; skipping FSE template flush"
	exit 0
fi

if [[ ! -f "${PUBLIC}/wp-config.php" ]]; then
	echo "WordPress not found at ${PUBLIC}; skipping"
	exit 0
fi

for id in $(wp --allow-root --path="${PUBLIC}" post list --post_type=wp_template --name=home --format=ids 2>/dev/null || true); do
	wp --allow-root --path="${PUBLIC}" post delete "${id}" --force
	echo "Deleted customized wp_template home (ID ${id})"
done

for id in $(wp --allow-root --path="${PUBLIC}" post list --post_type=wp_template_part --name=header --format=ids 2>/dev/null || true); do
	wp --allow-root --path="${PUBLIC}" post delete "${id}" --force
	echo "Deleted customized header template part (ID ${id})"
done

wp --allow-root --path="${PUBLIC}" cache flush 2>/dev/null || true
echo "FSE overrides flushed"