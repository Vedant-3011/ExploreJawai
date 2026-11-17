#!/usr/bin/env bash
# One-time / safe to re-run: Jawai travel blog content via WP-CLI.
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/html/domain/explorejawai.com/public_html}"
MARKER="${WP_PATH}/.explore-jawai-seeded"

wp() {
  command wp --allow-root --path="$WP_PATH" "$@"
}

cd "$WP_PATH"

if [[ -f "$MARKER" ]]; then
  echo "Already seeded ($MARKER). Remove file to run again."
  exit 0
fi

wp theme activate explore-jawai 2>/dev/null || {
  echo "explore-jawai theme missing — deploy repo first."
  exit 1
}

wp option update blogname 'Explore Jawai'
wp option update blogdescription 'Leopard hills, Rabari culture & safari guides from Jawai, Rajasthan'
wp option update timezone_string 'Asia/Kolkata'
wp option update date_format 'F j, Y'
wp option update show_on_front 'posts'
wp option update page_on_front 0
wp option update page_for_posts 0

# Categories
for slug in safari wildlife culture travel-tips; do
  case $slug in
    safari) name='Safari' ;;
    wildlife) name='Wildlife' ;;
    culture) name='Culture' ;;
    travel-tips) name='Travel Tips' ;;
  esac
  wp term create category "$name" --slug="$slug" 2>/dev/null || true
done

THEME_IMG="${WP_PATH}/wp-content/themes/explore-jawai/assets/images"
import_media() {
  local file="$1"
  local title="$2"
  if [[ ! -f "$file" ]]; then
    echo ""
    return
  fi
  wp media import "$file" --title="$title" --porcelain 2>/dev/null | tail -1
}

IMG_HERO=$(import_media "${THEME_IMG}/jawai-hero.jpg" "Leopard at dawn")
IMG_LAND=$(import_media "${THEME_IMG}/jawai-landscape.jpg" "Rajasthan granite hills")
IMG_SAFARI=$(import_media "${THEME_IMG}/jawai-safari.jpg" "Safari track")
IMG_VILLAGE=$(import_media "${THEME_IMG}/jawai-village.jpg" "Village life")
IMG_DAWN=$(import_media "${THEME_IMG}/jawai-dawn.jpg" "Desert sunrise")

wp post delete 1 --force 2>/dev/null || true

create_post() {
  local title="$1"
  local content="$2"
  local cats="$3"
  local thumb="$4"
  local id
  id=$(wp post create --post_type=post --post_status=publish --post_title="$title" \
    --post_content="$content" --porcelain)
  # shellcheck disable=SC2086
  wp post term set "$id" category $cats 2>/dev/null || true
  if [[ -n "$thumb" && -n "$id" ]]; then
    wp post meta update "$id" _thumbnail_id "$thumb"
  fi
}

create_post "Welcome to Explore Jawai: India's Leopard Country" \
"<p>Jawai sits where granite hills rise above thorn scrub in southern Rajasthan — one of the best places in India to see leopards living alongside Rabari herders and flamingos on the reservoir.</p>
<p>On this blog we share field notes from safari drives, village walks, and the seasonal rhythms that make Jawai unlike any other wildlife destination.</p>
<!-- wp:image -->
<figure class=\"wp-block-image\"><img alt=\"Rajasthan landscape\" /></figure>
<!-- /wp:image -->
<p><em>Photo: stock imagery for illustration — replace with your own Jawai shots in wp-admin.</em></p>" \
safari wildlife "$IMG_HERO"

create_post "Best Time for a Jawai Safari" \
"<p>October through March offers cool mornings ideal for leopard tracking. Summer brings dramatic light and fewer guests, but plan drives at dawn and dusk.</p>
<ul>
<li><strong>Winter (Nov–Feb):</strong> Peak season, migratory birds on the dam.</li>
<li><strong>Monsoon tail (Sep–Oct):</strong> Green hills, active reptiles.</li>
<li><strong>Summer (Apr–Jun):</strong> Waterholes concentrate wildlife — bring sun protection.</li>
</ul>" \
travel-tips safari "$IMG_DAWN"

create_post "Granite Hills & Rabari Villages: Beyond the Big Cats" \
"<p>Leopards may headline every trip, but Jawai's human landscape is just as compelling. Rabari communities still graze livestock among the boulders where cats rest by day.</p>
<p>Respect village privacy, hire local guides, and choose lodges that work with communities rather than only the forest fringe.</p>" \
culture wildlife "$IMG_VILLAGE"

create_post "Photography Tips for Dawn Patrol in Jawai" \
"<p>Start 20 minutes before legal light. A 70–200mm or 100–400mm covers most encounters; wider lenses capture herons against pink granite.</p>
<p>Turn off flash, keep voices low, and never bait or crowd a cat on a kill. Ethical images travel farther than stressed animals.</p>" \
travel-tips wildlife "$IMG_SAFARI"

create_post "Planning Your First Stay Near Jawai Bandh" \
"<p>Most visitors base themselves within 15–30 minutes of Jawai Dam. Compare lodges on guiding quality, naturalist ratios, and whether jeep routes are shared or private.</p>
<p>Build in two full safari days minimum — leopards cooperate, but never on a strict timetable.</p>" \
travel-tips safari "$IMG_LAND"

# Pages
wp post update 2 --post_title='About Explore Jawai' --post_name='about' --post_status=publish \
  --post_content='<p><strong>Explore Jawai</strong> is an independent travel blog dedicated to wildlife tourism, responsible safaris, and stories from the Jawai Bandh region of Rajasthan.</p>
<p>Use our <a href="/">latest posts</a> for itineraries and field notes. For partnerships or corrections, use the contact page.</p>'

CONTACT_ID=$(wp post list --post_type=page --name=contact --field=ID 2>/dev/null || true)
if [[ -z "$CONTACT_ID" ]]; then
  CONTACT_ID=$(wp post create --post_type=page --post_status=publish --post_title='Contact' --post_name=contact \
    --post_content='<p>Reach the Explore Jawai team at <a href="mailto:admin@explorejawai.com">admin@explorejawai.com</a>.</p>
<p>For lodge bookings, please contact operators directly — we list stories, not inventory.</p>' --porcelain)
fi

# Navigation
MENU_ID=$(wp menu list --fields=term_id,name --format=csv | awk -F, '$2=="Main" {print $1}')
if [[ -z "$MENU_ID" ]]; then
  MENU_ID=$(wp menu create Main --porcelain)
fi
wp menu item add-custom "$MENU_ID" Home https://explorejawai.com/ 2>/dev/null || true
wp menu item add-post "$MENU_ID" 2 2>/dev/null || true
wp menu location assign "$MENU_ID" primary 2>/dev/null || \
  wp menu location assign "$MENU_ID" header 2>/dev/null || true

wp rewrite flush --hard
touch "$MARKER"
echo "Explore Jawai seed complete ($(date -Is))"