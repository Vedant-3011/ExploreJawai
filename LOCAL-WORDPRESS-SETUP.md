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

## Option B — XAMPP (Windows)

**Important:** If `C:\xampp\htdocs\explore-jawai` is a **copy** of the repo (not a junction), Apache uses **that copy’s** `wp-config.php`, not the one you edit under `D:\Office\Project\...` unless you sync or link folders.

To use one folder only, run **as Administrator**: `scripts\xampp-link-public-html.ps1` → then use **`http://localhost/explore-jawai/`**.

Current layout (repo inside htdocs): **`http://localhost/explore-jawai/public_html`**

### 1. Start stack

1. Open **XAMPP Control Panel**.
2. **Start** **Apache** and **MySQL** (both green).

### 2. Point Apache at this project (junction — recommended)

Run **Command Prompt as Administrator** once:

**Either** link **`public_html` only** (clean URL):

```bat
mklink /J C:\xampp\htdocs\explore-jawai "D:\Office\Project\Projects\Explore Jawai\public_html"
```

→ **`http://localhost/explore-jawai`**

**Or** link the **whole repo** (what you have if `htdocs\explore-jawai` contains `public_html` as a subfolder):

```bat
mklink /J C:\xampp\htdocs\explore-jawai "D:\Office\Project\Projects\Explore Jawai"
```

→ **`http://localhost/explore-jawai/public_html`**

*(If you prefer not to use a junction, copy the whole `public_html` folder into `C:\xampp\htdocs\explore-jawai` instead — you must re-copy after theme updates.)*

### 3. Database (phpMyAdmin)

1. Open **`http://localhost/phpmyadmin`**
2. **Databases** → create name: **`explore_jawai_local`** → Collation: **utf8mb4_unicode_ci**

Default XAMPP MySQL login: user **`root`**, password **empty** (unless you changed it).

### 4. Branch + `wp-config.php`

```powershell
cd "D:\Office\Project\Projects\Explore Jawai"
git checkout editorial-local

cd public_html
copy wp-config.example.php wp-config.php
```

Edit **`wp-config.php`**:

| Setting | XAMPP typical value |
|---------|---------------------|
| `DB_NAME` | `explore_jawai_local` |
| `DB_USER` | `root` |
| `DB_PASSWORD` | `''` (empty string) |
| `DB_HOST` | `localhost` |

Add **before** `require_once ABSPATH . 'wp-settings.php';`:

```php
// If htdocs points at public_html only:
define( 'WP_HOME', 'http://localhost/explore-jawai' );
define( 'WP_SITEURL', 'http://localhost/explore-jawai' );
// If htdocs points at the whole repo (nested public_html):
// define( 'WP_HOME', 'http://localhost/explore-jawai/public_html' );
// define( 'WP_SITEURL', 'http://localhost/explore-jawai/public_html' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
```

Generate real salts: https://api.wordpress.org/secret-key/1.1/salt/ — paste into the `AUTH_KEY` … `NONCE_SALT` block.

### 5. WordPress install

1. Open **`http://localhost/explore-jawai`**
2. Run the 5-minute install (title, admin user, password).
3. **Appearance → Themes → Explore Jawai**
4. **Settings → Permalinks → Post name → Save**

Editorial design is on automatically (`EXPLORE_JAWAI_EDITORIAL` is `true` in the theme).

### 6. XAMPP troubleshooting

| Issue | Fix |
|-------|-----|
| Apache won’t start (port 80) | XAMPP **Config → Apache (httpd.conf)** or stop Skype/IIS; or use port **8080** and use `http://localhost:8080/explore-jawai` in `WP_HOME` / `WP_SITEURL` |
| Blank page / white screen | Check `wp-content/debug.log` if `WP_DEBUG` is on |
| Home looks like old design | **Appearance → Editor** → delete customized **Home** template, or reset templates |
| MySQL won’t start | Port **3306** in use — stop other MySQL/MariaDB services |

### 7. WP-CLI with XAMPP (optional)

If `wp` is installed globally:

```powershell
$env:WP_PATH = "D:\Office\Project\Projects\Explore Jawai\public_html"
# Git Bash:
bash deploy/set-plan-your-trip-page.sh
```

Or from `public_html`:

```powershell
wp option update home 'http://localhost/explore-jawai'
wp option update siteurl 'http://localhost/explore-jawai'
```

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