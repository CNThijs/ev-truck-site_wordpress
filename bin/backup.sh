#!/usr/bin/env bash
# Encrypted backup of database and files, written outside the web root. Runs on the server (STRATO, over SSH) from the
# WordPress folder, or with WP_ROOT set. Needs WP-CLI, tar and openssl. Copy it off the server with bin/backup-pull.sh.
#
#   WP_ROOT     folder with wp-config.php and wp-content   (default: current folder)
#   BACKUP_DIR  where the encrypted files go               (default: ~/chargenet-backups, not in the web root)
#   KEY_FILE    file holding the passphrase, mode 600      (default: ~/.chargenet-backup.key; create it once, keep a copy
#                                                           in your password manager, without it a backup cannot be opened)
#   KEEP        how many backups to keep on the server     (default: 7)
#
# Content: database dump, uploads, themes, plugins, wp-config.php and .htaccess (they hold the secrets, hence the
# encryption). Not in it: the page cache. Restore: docs/backups.md.
set -euo pipefail

WP_ROOT="${WP_ROOT:-$PWD}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/chargenet-backups}"
KEY_FILE="${KEY_FILE:-$HOME/.chargenet-backup.key}"
KEEP="${KEEP:-7}"
WP="${WP:-wp}"

[ -f "$WP_ROOT/wp-config.php" ] || { echo "No wp-config.php in $WP_ROOT (set WP_ROOT)." >&2; exit 1; }
[ -s "$KEY_FILE" ] || { echo "Passphrase file $KEY_FILE is missing or empty." >&2; exit 1; }
[ "$(stat -c %a "$KEY_FILE" 2>/dev/null || stat -f %Lp "$KEY_FILE")" = "600" ] || { echo "Run: chmod 600 $KEY_FILE" >&2; exit 1; }

umask 077
mkdir -p "$BACKUP_DIR"
work="$(mktemp -d)"
trap 'rm -rf "$work"' EXIT
stamp="$(date -u +%Y%m%dT%H%M%SZ)"
out="$BACKUP_DIR/chargenet-$stamp.tar.gz.enc"

"$WP" --path="$WP_ROOT" db export "$work/database.sql" --single-transaction --quiet
files=(wp-content/uploads wp-content/themes wp-content/plugins wp-config.php)
[ -f "$WP_ROOT/.htaccess" ] && files+=(.htaccess)
tar -czf "$work/files.tar.gz" -C "$WP_ROOT" --exclude='wp-content/cache' "${files[@]}"
tar -czf "$work/bundle.tar.gz" -C "$work" database.sql files.tar.gz
openssl enc -aes-256-cbc -pbkdf2 -salt -pass "file:$KEY_FILE" -in "$work/bundle.tar.gz" -out "$out"
(cd "$BACKUP_DIR" && { sha256sum "$(basename "$out")" 2>/dev/null || shasum -a 256 "$(basename "$out")"; } > "$(basename "$out").sha256")

# Keep the newest $KEEP backups on the server.
ls -1t "$BACKUP_DIR"/chargenet-*.tar.gz.enc 2>/dev/null | tail -n +"$((KEEP + 1))" | while read -r old; do rm -f "$old" "$old.sha256"; done

echo "Backup written: $out ($(du -h "$out" | cut -f1))"
