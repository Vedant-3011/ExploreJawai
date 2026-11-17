# ExploreJawai

WordPress site for [explorejawai.com](https://explorejawai.com).

## Layout

| Environment | URL | Git branch | Server path |
|-------------|-----|------------|---------------|
| Production | https://explorejawai.com | `main` | `/var/www/html/domain/explorejawai.com/public_html` |
| Staging | https://staging.explorejawai.com | `staging` | `/var/www/html/domain/explorejawai.com/staging_public_html` |
| Local clone | — | — | `public_html/` |

## Setup (local)

1. Copy `public_html/wp-config.example.php` to `public_html/wp-config.php` and set database credentials (or use production values only on the server).
2. Point your local web server at `public_html/`.

## Deploy to production

### Auto deploy (default)

| Branch | Workflow | Server script |
|--------|----------|---------------|
| `main` | [deploy.yml](.github/workflows/deploy.yml) | `deploy/pull.sh` → production |
| `staging` | [deploy-staging.yml](.github/workflows/deploy-staging.yml) | `deploy/pull-staging.sh` → staging |

Secrets: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY` (production), `DEPLOY_SSH_KEY_STAGING` (staging).

Manual runs: GitHub → **Actions** → **Deploy to explorejawai.com** or **Deploy staging**.

### Staging DNS (one-time)

`staging.explorejawai.com` must resolve to this server (`72.61.255.120`) before HTTPS works. In Cloudflare (or your DNS host), add:

- **Type:** `A`
- **Name:** `staging`
- **Content:** `72.61.255.120`
- **Proxy:** DNS only (grey cloud) until the certificate is issued, then you can enable the proxy if you want.

Then on the server:

```bash
certbot --nginx -d staging.explorejawai.com --non-interactive --agree-tos -m admin@explorejawai.com --redirect
```

Staging uses database `explorejawai_wp_staging` (copy of production at first provision). Production and staging configs (`wp-config.php`) stay only on the server.

### Manual deploy

```bash
ssh root@72.61.255.120 'bash /var/www/html/domain/explorejawai.com/repo/deploy/pull.sh'
```

`wp-config.php` on the server is never overwritten by git.

## Repository

- GitHub: `https://github.com/Vedant-3011/ExploreJawai`
- Hosted on: proppik.cloud (`72.61.255.120`)