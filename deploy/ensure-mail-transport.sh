#!/usr/bin/env bash
# WordPress wp_mail() needs sendmail/postfix (Contact Form 7 on /contact/).
set -euo pipefail

if command -v sendmail >/dev/null 2>&1 || [[ -x /usr/sbin/sendmail ]]; then
	echo "Mail transport OK ($(command -v sendmail || echo /usr/sbin/sendmail))."
	exit 0
fi

if ! command -v apt-get >/dev/null 2>&1; then
	echo "WARN: No sendmail and apt-get unavailable — configure SMTP in WP Mail SMTP plugin."
	exit 0
fi

export DEBIAN_FRONTEND=noninteractive
apt-get install -y -qq postfix || apt-get install -y postfix

postconf -e 'myhostname = explorejawai.com'
postconf -e 'myorigin = explorejawai.com'
postconf -e 'inet_interfaces = all'
systemctl enable postfix 2>/dev/null || true
systemctl restart postfix 2>/dev/null || true

echo "Postfix installed for wp_mail / Contact Form 7."