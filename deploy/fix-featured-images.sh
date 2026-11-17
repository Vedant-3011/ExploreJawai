#!/usr/bin/env bash
# Repair _thumbnail_id meta (must be attachment IDs, not category slugs).
set -euo pipefail
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
IMG_DIR="$WP_PATH/wp-content/themes/explore-jawai/assets/images"

wp() { command wp --allow-root --path="$WP_PATH" "$@" ; }

import_id() {
  local file="$1" title="$2"
  wp media import "$file" --title="$title" --porcelain 2>/dev/null | tail -1
}

IMG_LAND=$(import_id "$IMG_DIR/jawai-landscape.jpg" "Rajasthan granite hills")
IMG_DAWN=$(import_id "$IMG_DIR/jawai-dawn.jpg" "Desert sunrise")
IMG_VILLAGE=$(import_id "$IMG_DIR/jawai-village.jpg" "Village life")
IMG_SAFARI=$(import_id "$IMG_DIR/jawai-safari.jpg" "Safari track")
IMG_HERO=$(import_id "$IMG_DIR/jawai-hero.jpg" "Leopard at dawn")

set_thumb() {
  local post_id="$1" attach_id="$2"
  wp post meta update "$post_id" _thumbnail_id "$attach_id"
}

set_thumb 18 "$IMG_LAND"
set_thumb 17 "$IMG_SAFARI"
set_thumb 16 "$IMG_VILLAGE"
set_thumb 15 "$IMG_DAWN"
set_thumb 14 "$IMG_HERO"

wp cache flush 2>/dev/null || true
echo "Featured images fixed."