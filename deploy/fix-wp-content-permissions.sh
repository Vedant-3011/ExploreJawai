#!/usr/bin/env bash
# WordPress (core / plugin / theme updates) must create dirs under wp-content/upgrade/
# and write into the install root. Fix ownership, cleanup stale upgrades, verify disk space.
set -euo pipefail

PUBLIC="${1:-/var/www/html/domain/explorejawai.com/public_html}"
WC="${PUBLIC}/wp-content"
UP="${WC}/upgrade"

WEB_USER="www-data"
WEB_GROUP="www-data"

# Collect PHP-FPM pool users (site may not use www-data).
PHP_USERS="www-data"
if compgen -G "/etc/php/*/fpm/pool.d/*.conf" >/dev/null 2>&1; then
	while IFS= read -r u; do
		[[ -n "${u}" ]] && PHP_USERS="${PHP_USERS} ${u}"
	done < <(grep -hE '^[[:space:]]*user[[:space:]]*=' /etc/php/*/fpm/pool.d/*.conf 2>/dev/null \
		| awk -F= '{print $2}' | tr -d '[:space:]' | sort -u)
	u="$(grep -hE '^[[:space:]]*user[[:space:]]*=' /etc/php/*/fpm/pool.d/www.conf 2>/dev/null | head -1 \
		| awk -F= '{print $2}' | tr -d '[:space:]')"
	g="$(grep -hE '^[[:space:]]*group[[:space:]]*=' /etc/php/*/fpm/pool.d/www.conf 2>/dev/null | head -1 \
		| awk -F= '{print $2}' | tr -d '[:space:]')"
	[[ -n "${u}" ]] && WEB_USER="${u}"
	[[ -n "${g}" ]] && WEB_GROUP="${g}"
fi
for extra in nginx apache httpd litespeed; do
	if id "${extra}" >/dev/null 2>&1; then
		PHP_USERS="${PHP_USERS} ${extra}"
	fi
done
PHP_USERS="$(echo "${PHP_USERS}" | tr ' ' '\n' | sort -u | tr '\n' ' ')"

echo "=== WordPress filesystem fix ==="
echo "Site: ${PUBLIC}"
echo "PHP-FPM (default pool): ${WEB_USER}:${WEB_GROUP}"
echo "Known web users: ${PHP_USERS}"

df -h "${PUBLIC}" 2>/dev/null | tail -1 || df -h / | tail -1

# Stale/broken upgrade paths break the next update attempt.
if [[ -e "${UP}" && ! -d "${UP}" ]]; then
	echo "Removing non-directory at ${UP}"
	rm -f "${UP}"
fi
rm -rf "${UP}"/wordpress-* "${UP}"/jetpack.* 2>/dev/null || true
mkdir -p "${UP}"

# Core updates replace wp-admin, wp-includes, and root PHP — entire tree must be web-writable.
chown -R "${WEB_USER}:${WEB_GROUP}" "${PUBLIC}"
find "${PUBLIC}" -type d -exec chmod 755 {} +
find "${PUBLIC}" -type f -exec chmod 644 {} +

chmod 775 "${WC}" "${UP}" "${WC}/plugins" "${WC}/themes"
[[ -d "${WC}/uploads" ]] && chmod 775 "${WC}/uploads"

if [[ -f "${PUBLIC}/wp-config.php" ]]; then
	chmod 640 "${PUBLIC}/wp-config.php"
	chown "${WEB_USER}:${WEB_GROUP}" "${PUBLIC}/wp-config.php"
fi

# Group-writable + ACL so secondary pool users (nginx, etc.) can still install updates.
if command -v setfacl >/dev/null 2>&1; then
	for u in ${PHP_USERS}; do
		id "${u}" >/dev/null 2>&1 || continue
		setfacl -m "u:${u}:rwx" "${WC}" "${UP}" "${WC}/plugins" "${WC}/themes" 2>/dev/null || true
		setfacl -d -m "u:${u}:rwx" "${UP}" 2>/dev/null || true
	done
else
	chmod g+rws "${WC}" "${UP}" "${WC}/plugins" "${WC}/themes" 2>/dev/null || true
fi

if command -v lsattr >/dev/null 2>&1; then
	lsattr -d "${WC}" "${UP}" 2>/dev/null || true
fi

write_ok=0
for u in ${PHP_USERS}; do
	id "${u}" >/dev/null 2>&1 || continue
	if su -s /bin/bash -c "mkdir -p '${UP}/.perm-check-${u}' && rmdir '${UP}/.perm-check-${u}'" "${u}" 2>/dev/null; then
		echo "OK: user '${u}' can create directories in wp-content/upgrade"
		write_ok=1
	fi
done
if [[ "${write_ok}" -eq 0 ]]; then
	echo "ERROR: No web user could write to ${UP}" >&2
	ls -la "${WC}" "${UP}" 2>&1 || true
	namei -l "${UP}" 2>&1 || true
	exit 1
fi

if command -v wp >/dev/null 2>&1 && [[ -f "${PUBLIC}/wp-config.php" ]]; then
	disallow="$(wp --allow-root --path="${PUBLIC}" config get DISALLOW_FILE_MODS 2>/dev/null || echo "")"
	if [[ "${disallow}" == "1" || "${disallow}" == "true" ]]; then
		echo "WARN: DISALLOW_FILE_MODS is enabled — admin updates will fail until disabled." >&2
	fi
	wp --allow-root --path="${PUBLIC}" config set FS_METHOD direct --type=constant --raw 2>/dev/null \
		&& echo "FS_METHOD=direct in wp-config.php" || true
fi

echo "=== wp-content permissions updated ==="
ls -lad "${WC}" "${UP}"