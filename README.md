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

### Auto deploy (default)

Every **push to `main`** runs [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml) and runs `deploy/pull.sh` on the server via SSH (GitHub Actions secrets: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`).

You can also trigger a deploy manually: GitHub → **Actions** → **Deploy to explorejawai.com** → **Run workflow**.

### Manual deploy

```bash
ssh root@72.61.255.120 'bash /var/www/html/domain/explorejawai.com/repo/deploy/pull.sh'
```

`wp-config.php` on the server is never overwritten by git.

## Repository

- GitHub: `https://github.com/Vedant-3011/ExploreJawai`
- Hosted on: proppik.cloud (`72.61.255.120`)