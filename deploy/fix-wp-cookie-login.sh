#!/usr/bin/env bash
# Fix "Cookies are blocked due to unexpected output" on wp-login (production).
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
CFG="${WP_PATH}/wp-config.php"

wp() {
  command wp --allow-root --path="$WP_PATH" "$@"
}

if [[ ! -f "$CFG" ]]; then
  echo "Missing wp-config.php at $CFG"
  exit 1
fi

# Strip UTF-8 BOM if present.
if head -c 3 "$CFG" | grep -q $'\xef\xbb\xbf'; then
  tail -c +4 "$CFG" > "${CFG}.nobom"
  mv "${CFG}.nobom" "$CFG"
  echo "Removed UTF-8 BOM from wp-config.php"
fi

# Remove closing PHP tag + trailing whitespace (common cause of cookie errors).
perl -i -pe 'chomp if eof' "$CFG"
if grep -q '?>' "$CFG"; then
  perl -i -0777 -pe 's/\?\>\s*$//' "$CFG"
  echo "Removed closing ?> from wp-config.php"
fi

# Ensure debug does not print to browser.
wp config set WP_DEBUG false --raw --type=constant 2>/dev/null || true
wp config set WP_DEBUG_DISPLAY false --raw --type=constant 2>/dev/null || true
wp config set WP_DEBUG_LOG true --raw --type=constant 2>/dev/null || true

# Output buffer before WordPress loads (after <?php).
if ! grep -q 'EXPLORE_JAWAI_OUTPUT_BUFFER' "$CFG"; then
  perl -i -pe 'if ($.==1 && /^<\?php/) { $_ .= "\n// EXPLORE_JAWAI_OUTPUT_BUFFER\nif ( ! headers_sent() ) { ob_start(); }\n@ini_set( '\''display_errors'\'', '\''0'\'' );\n" }' "$CFG"
  echo "Added output buffer guard to wp-config.php"
fi

# Hello Dolly sometimes causes admin noise; not needed on production.
wp plugin deactivate hello --quiet 2>/dev/null || true

# Verify bootstrap emits no bytes before headers (CLI).
OUT=$(php -r "define('WP_USE_THEMES', false); ob_start(); require '${WP_PATH}/wp-load.php'; \$b=ob_get_contents(); ob_end_clean(); echo strlen(\$b);" 2>/dev/null || echo "fail")
echo "Bootstrap stray output bytes: ${OUT}"
if [[ "$OUT" != "0" ]] && [[ "$OUT" != "fail" ]]; then
  echo "WARN: Still ${OUT} bytes output during load — check wp-content/debug.log"
fi

wp cache flush --quiet 2>/dev/null || true
echo "Done. Try https://explorejawai.com/wp-login.php in a private window."