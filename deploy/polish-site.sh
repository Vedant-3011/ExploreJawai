#!/usr/bin/env bash
set -euo pipefail
WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
IMG="$WP_PATH/wp-content/themes/explore-jawai/assets/images/jawai-hero.jpg"

wp() { command wp --allow-root --path="$WP_PATH" "$@" ; }

wp theme activate explore-jawai
wp option update blogdescription 'Leopard hills, Rabari culture & safari guides from Jawai, Rajasthan'

# Site logo for header
LOGO_ID=$(wp media import "$IMG" --title="Explore Jawai logo" --porcelain 2>/dev/null | tail -1 || true)
if [[ -n "$LOGO_ID" ]]; then
  wp option update site_logo "$LOGO_ID"
fi

# Main menu
MENU_ID=$(wp menu list --fields=term_id,name --format=csv | awk -F, '$2=="Main" {print $1}')
[[ -z "$MENU_ID" ]] && MENU_ID=$(wp menu create Main --porcelain)
wp menu item list "$MENU_ID" --format=count 2>/dev/null | grep -q '^[1-9]' || {
  wp menu item add-custom "$MENU_ID" Home https://explorejawai.com/
  wp menu item add-post "$MENU_ID" 2
  wp menu item add-post "$MENU_ID" $(wp post list --post_type=page --name=contact --field=ID)
}

for loc in primary header; do
  wp menu location assign "$MENU_ID" "$loc" 2>/dev/null && break
done

bash "$(dirname "$0")/fix-featured-images.sh" 2>/dev/null || true

# Remove customized template parts saved in DB (use theme files)
for slug in footer header sidebar; do
  TID=$(wp post list --post_type=wp_template_part --name="$slug" --field=ID 2>/dev/null || true)
  [[ -n "$TID" ]] && wp post delete "$TID" --force 2>/dev/null || true
done

wp cache flush 2>/dev/null || true
wp rewrite flush --hard 2>/dev/null || true
echo "Site polish complete."