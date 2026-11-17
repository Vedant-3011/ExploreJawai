# ExploreJawai

WordPress site for [explorejawai.com](https://explorejawai.com).

## Layout

| Location | Path |
|----------|------|
| Local clone | `public_html/` |
| Production server | `/var/www/html/domain/explorejawai.com/public_html` |

## Setup (local)

1. Copy `public_html/wp-config.example.php` to `public_html/wp-config.php` and set database credentials (or use production values only on the server).
2. Point your local web server at `public_html/`.

## Deploy to production

From your machine (after pushing to GitHub):

```bash
ssh root@72.61.255.120 'bash /var/www/html/domain/explorejawai.com/repo/deploy/pull.sh'
```

Or on the server, from the site directory:

```bash
cd /var/www/html/domain/explorejawai.com
./deploy/pull.sh
```

`wp-config.php` on the server is never overwritten by git.

## Repository

- GitHub: `https://github.com/Vedant-3011/ExploreJawai`
- Hosted on: proppik.cloud (`72.61.255.120`)