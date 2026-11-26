"""Diagnose and fix WordPress wp-content permissions for plugin install/upload."""
import os
import sys

import paramiko

HOST = "72.61.255.120"
WP = "/var/www/html/domain/explorejawai.com/public_html"
WC = f"{WP}/wp-content"


def run(c: paramiko.SSHClient, cmd: str) -> tuple[str, str]:
    print(">>", cmd)
    _, stdout, stderr = c.exec_command(cmd, timeout=60)
    out = stdout.read().decode(errors="replace")
    err = stderr.read().decode(errors="replace")
    if out:
        print(out, end="" if out.endswith("\n") else "\n")
    if err.strip():
        print("stderr:", err)
    return out, err


def main() -> int:
    pw = os.environ.get("PROPPIK_SSH_PASSWORD")
    if not pw:
        print("Set PROPPIK_SSH_PASSWORD", file=sys.stderr)
        return 1

    fix = "--fix" in sys.argv

    c = paramiko.SSHClient()
    c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    c.connect(HOST, username="root", password=pw, timeout=30)

    run(c, f"ls -la {WC}/ | head -25")
    run(c, f"ls -lad {WC}/upgrade {WC}/plugins {WC}/uploads 2>&1")
    run(
        c,
        f"stat -c '%U:%G %a %n' {WC} {WC}/plugins {WC}/upgrade 2>&1",
    )
    run(c, "grep -E '^user|^group' /etc/php/*/fpm/pool.d/www.conf 2>/dev/null | head -6")
    run(c, f"sudo -u www-data test -w {WC} && echo www-data-writable-wp-content || echo www-data-NOT-writable")
    run(
        c,
        f"sudo -u www-data mkdir -p {WC}/upgrade/.perm-test && rmdir {WC}/upgrade/.perm-test && echo upgrade-mkdir-ok || echo upgrade-mkdir-FAIL",
    )

    if fix:
        print("\n--- Applying fix ---")
        fix_cmds = f"""
set -e
WP="{WP}"
WC="$WP/wp-content"
install -d -o www-data -g www-data -m 755 "$WC/upgrade"
install -d -o www-data -g www-data -m 755 "$WC/upgrade-temp-backup" 2>/dev/null || true
chown -R www-data:www-data "$WC"
find "$WC" -type d -exec chmod 755 {{}} \\;
find "$WC" -type f -exec chmod 644 {{}} \\;
# Allow WordPress to write plugins, themes, uploads, upgrade
chmod 775 "$WC"
chmod 775 "$WC/plugins" "$WC/themes" "$WC/upgrade" "$WC/uploads" 2>/dev/null || true
chgrp -R www-data "$WP"
# wp-config must stay readable by web server only
chown www-data:www-data "$WP/wp-config.php" 2>/dev/null || true
chmod 640 "$WP/wp-config.php" 2>/dev/null || true
sudo -u www-data mkdir -p "$WC/upgrade/.perm-test" && rmdir "$WC/upgrade/.perm-test"
echo FIX_APPLIED
"""
        out, _ = run(c, fix_cmds)
        if "FIX_APPLIED" not in out:
            print("Fix may have failed — check output above", file=sys.stderr)
            c.close()
            return 1
    else:
        print("\nDry run only. Re-run with --fix to apply permissions.")

    c.close()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())