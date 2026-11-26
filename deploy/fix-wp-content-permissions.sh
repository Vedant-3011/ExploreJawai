#!/usr/bin/env bash
# WordPress needs the web-server user to write wp-content/{upgrade,plugins,themes,uploads}.
# Without this, plugin install/upload fails with "Could not create directory" in wp-content/upgrade/.
set -euo pipefail

PUBLIC="${1:-/var/www/html/domain/explorejawai.com/public_html}"
WC="${PUBLIC}/wp-content"

WEB_USER="www-data"
WEB_GROUP="www-data"
if compgen -G "/etc/php/*/fpm/pool.d/www.conf" >/dev/null 2>&1; then
	u="$(grep -E '^[[:space:]]*user[[:space:]]*=' /etc/php/*/fpm/pool.d/www.conf 2>/dev/null | head -1 | awk -F= '{print $2}' | tr -d '[:space:]')"
	g="$(grep -E '^[[:space:]]*group[[:space:]]*=' /etc/php/*/fpm/pool.d/www.conf 2>/dev/null | head -1 | awk -F= '{print $2}' | tr -d '[:space:]')"
	[[ -n "${u}" ]] && WEB_USER="${u}"
	[[ -n "${g}" ]] && WEB_GROUP="${g}"
fi

echo "WordPress content permissions (${WEB_USER}:${WEB_GROUP}) → ${WC}"

install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 775 "${WC}"
install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 775 "${WC}/upgrade"
install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 775 "${WC}/plugins"
install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 775 "${WC}/themes"
install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 775 "${WC}/uploads" 2>/dev/null || true

chown -R "${WEB_USER}:${WEB_GROUP}" "${WC}"

# Writable trees for admin installs; files stay world-readable.
find "${WC}/upgrade" "${WC}/plugins" "${WC}/themes" "${WC}/uploads" -type d -print0 2>/dev/null \
	| xargs -0 -r chmod 775 2>/dev/null || true
find "${WC}/upgrade" "${WC}/plugins" "${WC}/themes" "${WC}/uploads" -type f -print0 2>/dev/null \
	| xargs -0 -r chmod 664 2>/dev/null || true

if id "${WEB_USER}" >/dev/null 2>&1; then
	su -s /bin/bash -c "mkdir -p '${WC}/upgrade/.perm-check' && rmdir '${WC}/upgrade/.perm-check'" "${WEB_USER}" \
		&& echo "OK: ${WEB_USER} can create directories under wp-content/upgrade" \
		|| {
			echo "ERROR: ${WEB_USER} still cannot write to ${WC}/upgrade" >&2
			exit 1
		}
fi

if command -v wp >/dev/null 2>&1 && [[ -f "${PUBLIC}/wp-config.php" ]]; then
	if ! wp --allow-root --path="${PUBLIC}" config get FS_METHOD 2>/dev/null | grep -q direct; then
		wp --allow-root --path="${PUBLIC}" config set FS_METHOD direct --type=constant --raw 2>/dev/null \
			&& echo "Set FS_METHOD=direct in wp-config.php" \
			|| true
	fi
fi

echo "wp-content permissions updated."