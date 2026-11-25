# Preview editorial design (local only — not on production)

## What changed (in repo only)

- **Design:** Green / ivory editorial from the Complete Website package
- **Logo:** `explore-jawai-logo-lockup.png` (same white lockup as explorejawai.com)
- **Home:** New hero + 3 experience cards + band + journal grid
- **Nav:** Home · Explore Jawai · Plan Your Trip · Blog · Contact Us · Start Planning
- **Flag:** `EXPLORE_JAWAI_EDITORIAL` in `functions.php` (set `false` to revert look)

## Preview on your PC

1. Use Local WP, XAMPP, or copy `public_html` to your local WordPress.
2. Activate **Explore Jawai** child theme.
3. Delete customized Home template in WP admin (or run `scripts/reset_home_template.py` on staging only).
4. Open `http://localhost/.../` — body class `ej-editorial` loads `assets/css/editorial.css`.

## Pages to create before full parity

| Slug | Status |
|------|--------|
| `/about/` | Use existing About content (maps to “Explore Jawai” in nav) |
| `/plan-your-trip/` | Create from `Explore_Jawai_Complete_Website_With_Images/.../plan-your-trip.html` |
| `/contact/` | Existing |

## Production

**Do not** `git push` main or run `deploy/pull.sh` until you approve go-live.  
See `deploy/DO-NOT-DEPLOY-EDITORIAL.txt`.

To disable editorial on a staging copy without removing files:

```php
// functions.php
define( 'EXPLORE_JAWAI_EDITORIAL', false );
```