#!/usr/bin/env bash
# Run on ANOTHER machine (your Mac, a NAS, a second host), not on the web server: starts the backup on STRATO over SSH
# and copies the encrypted files here. The server needs no credentials for the destination, and the passphrase never
# leaves the server and your password manager. Needs ssh and rsync.
#
#   SSH_HOST     user@host of the STRATO account               (required, e.g. ssh-user@ssh.strato.de)
#   WP_ROOT      WordPress folder on the server                (required, e.g. /path/to/the/site)
#   SCRIPT_DIR   folder on the server that holds backup.sh     (default: $WP_ROOT/..)
#   LOCAL_DIR    where to keep the copies                      (default: ~/chargenet-backups-offsite)
#   KEEP_LOCAL   how many to keep here                         (default: 30)
#
# Schedule it with cron/launchd on that machine, e.g. daily 03:30: 30 3 * * * SSH_HOST=... WP_ROOT=... /path/backup-pull.sh
set -euo pipefail

: "${SSH_HOST:?set SSH_HOST}"
: "${WP_ROOT:?set WP_ROOT}"
SCRIPT_DIR="${SCRIPT_DIR:-$WP_ROOT/..}"
LOCAL_DIR="${LOCAL_DIR:-$HOME/chargenet-backups-offsite}"
KEEP_LOCAL="${KEEP_LOCAL:-30}"

umask 077
mkdir -p "$LOCAL_DIR"
ssh "$SSH_HOST" "WP_ROOT='$WP_ROOT' bash '$SCRIPT_DIR/backup.sh'"
rsync -a --ignore-existing -e ssh "$SSH_HOST:chargenet-backups/" "$LOCAL_DIR/"

# Check every file against its checksum, then trim old copies.
(cd "$LOCAL_DIR" && for f in chargenet-*.tar.gz.sha256; do { sha256sum -c "$f" --quiet 2>/dev/null || shasum -a 256 -c "$f" >/dev/null; } || { echo "Checksum failed: $f" >&2; exit 1; }; done)
ls -1t "$LOCAL_DIR"/chargenet-*.tar.gz.enc | tail -n +"$((KEEP_LOCAL + 1))" | while read -r old; do rm -f "$old" "$old.sha256"; done
echo "Offsite copies in $LOCAL_DIR: $(ls -1 "$LOCAL_DIR"/chargenet-*.tar.gz.enc | wc -l)"
