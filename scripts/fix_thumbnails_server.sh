#!/usr/bin/env bash
# Ensure every published post has a featured image.
set -euo pipefail
WP="/var/www/html/domain/explorejawai.com/public_html"
wp() { command wp --allow-root --path="$WP" "$@" ; }

THEME_IMG="$WP/wp-content/themes/explore-jawai/assets/images"
mapfile -t IDS < <(wp post list --post_type=post --post_status=publish --field=ID)
mapfile -t FILES < <(ls "$THEME_IMG"/jawai-*.jpg 2>/dev/null | sort)
n=${#IDS[@]}
for i in "${!IDS[@]}"; do
  id="${IDS[$i]}"
  thumb=$(wp post meta get "$id" _thumbnail_id 2>/dev/null || true)
  if [[ -z "$thumb" || "$thumb" == "0" ]]; then
    file="${FILES[$((i % ${#FILES[@]}))]}"
    mid=$(wp media import "$file" --porcelain 2>/dev/null | tail -1)
    wp post meta update "$id" _thumbnail_id "$mid"
    echo "post $id -> thumb $mid"
  fi
done
wp cache flush 2>/dev/null || true
echo done