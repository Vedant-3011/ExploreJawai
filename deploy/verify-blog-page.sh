#!/usr/bin/env bash
set -euo pipefail

URL="${1:-https://explorejawai.com/blog/}"
HTML="$(curl -fsSL --max-time 25 "$URL" || true)"
if [[ -z "$HTML" ]]; then
	echo "Blog page verify FAIL: empty response from $URL" >&2
	exit 1
fi
if ! grep -q 'ej-blog-page-hero' <<<"$HTML"; then
	echo "Blog page verify FAIL: missing ej-blog-page-hero at $URL" >&2
	exit 1
fi
echo "Blog page OK: $URL"