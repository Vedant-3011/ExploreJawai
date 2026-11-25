# Local WordPress — editorial design (check before production)

Use this flow: **local WordPress → you review → you approve → we merge & deploy online.**

Production **explorejawai.com** is unchanged until you say **“approved for production”**.

---

## Option A — Laragon (recommended on Windows)

1. Install [Laragon](https://laragon.org/) (includes Apache, MySQL, PHP).
2. **Menu → Quick app → WordPress** (or copy existing `public_html` into Laragon’s `www` folder).
3. Point the site root to this repo’s WordPress folder:
   - **Document root:** `D:\Office\Project\Projects\Explore Jawai\public_html`
4. Create database `explore_jawai_local` in Laragon (HeidiSQL or `mysql` CLI).
5. Copy config:
   ```powershell
   cd "D:\Office\Project\Projects\Explore Jawai\public_html"
   copy wp-config.example.php wp-config.php
   ```
6. Edit `wp-config.php`: set `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`.
7. Add for local URLs (top of `wp-config.php`, before `require wp-settings.php`):
   ```php
   define( 'WP_HOME', 'http://explore-jawai.test' );
   define( 'WP_SITEURL', 'http://explore-jawai.test' );
   define( 'WP_DEBUG', true );
   ```
8. In Laragon, add virtual host **`explore-jawai.test`** → same `public_html` folder.
9. Open `http://explore-jawai.test` — run WordPress install if fresh.
10. **Appearance → Themes → Explore Jawai** (child theme).
11. **Settings → Permalinks → Save** (flush rewrite rules).

**Editorial design** loads automatically (`EXPLORE_JAWAI_EDITORIAL` is `true` in `functions.php`).

---

## Option B — XAMPP

1. Install XAMPP; start Apache + MySQL.
2. Put or symlink `public_html` under `C:\xampp\htdocs\explore-jawai`.
3. Create MySQL database `explore_jawai_local`.
4. Same `wp-config.php` steps as above; use `http://localhost/explore-jawai`.

---

## After WordPress is running locally

| Step | Action |
|------|--------|
| Home template | If home looks old: delete customized **Home** template in **Appearance → Editor**, or run WP-CLI `wp template delete` for `home`. |
| Pages | Ensure **About** (`/about/`) and **Contact** (`/contact/`) exist. |
| Plan Your Trip | Create page slug `plan-your-trip` (or run `deploy/set-plan-your-trip-page.sh` with WP-CLI when available). |
| Posts | Import sample posts or copy from staging; journal section needs published posts. |
| Check | Home = green hero + 3 cards + journal; header = **white logo lockup**; footer = green. |

---

## Git workflow (local vs production)

| Branch | Purpose |
|--------|---------|
| `main` | What production deploy uses — **editorial not merged here until you approve** |
| `editorial-local` | Your editorial theme work — safe to commit locally |

**Do not merge `editorial-local` → `main` or push `main` until you approve.**

When approved, we will:

1. Set `EXPLORE_JAWAI_EDITORIAL` as needed on production  
2. Remove or clear `deploy/DO-NOT-DEPLOY-EDITORIAL.txt`  
3. Merge to `main` and run deploy  

---

## Turn off editorial look (local test)

In `public_html/wp-content/themes/explore-jawai/functions.php`:

```php
define( 'EXPLORE_JAWAI_EDITORIAL', false );
```

---

## Still static HTML only?

Open the original prototype (no WordPress):

```powershell
cd "D:\Office\Project\Projects\Explore Jawai\Explore_Jawai_Complete_Website_With_Images\explore-jawai-website"
python -m http.server 8765
```

→ `http://localhost:8765/` (logo there is SVG; WordPress local uses explorejawai.com lockup.)