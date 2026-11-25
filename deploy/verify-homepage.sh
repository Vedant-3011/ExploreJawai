#!/usr/bin/env bash
# Fail deploy if public homepage returns an empty body (blank white screen guard).
set -euo pipefail

URL="${1:-https://explorejawai.com/}"
BYTES="$(curl -fsSL -A 'Mozilla/5.0 ExploreJawaiDeploy/1.0' -o /tmp/ej-home-check.html -w '%{size_download}' "${URL}" || echo 0)"
if [[ "${BYTES}" -lt 4096 ]]; then
	echo "ABORT: homepage too small (${BYTES} bytes) — possible blank site."
	head -c 500 /tmp/ej-home-check.html 2>/dev/null || true
	exit 1
fi
echo "Homepage OK: ${BYTES} bytes"