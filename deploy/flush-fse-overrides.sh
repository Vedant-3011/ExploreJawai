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

for name in home single; do
	for id in $(wp --allow-root --path="${PUBLIC}" post list --post_type=wp_template --name="${name}" --format=ids 2>/dev/null || true); do
		wp --allow-root --path="${PUBLIC}" post delete "${id}" --force
		echo "Deleted customized wp_template ${name} (ID ${id})"
	done
done

for part in header footer; do
	for id in $(wp --allow-root --path="${PUBLIC}" post list --post_type=wp_template_part --name="${part}" --format=ids 2>/dev/null || true); do
		wp --allow-root --path="${PUBLIC}" post delete "${id}" --force
		echo "Deleted customized template part ${part} (ID ${id})"
	done
done

wp --allow-root --path="${PUBLIC}" cache flush 2>/dev/null || true
echo "FSE overrides flushed"