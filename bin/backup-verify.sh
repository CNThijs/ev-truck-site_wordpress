#!/usr/bin/env bash
# Restore test, step 1: open a backup, check that it is complete, and print what is in it. Does not touch any site.
# Usage: KEY_FILE=~/.chargenet-backup.key bin/backup-verify.sh chargenet-<stamp>.tar.gz.enc [extract-dir]
# With an extract-dir the content is unpacked there (database.sql and files/) for the restore steps in docs/backups.md.
set -euo pipefail

file="${1:?usage: backup-verify.sh <file.tar.gz.enc> [extract-dir]}"
KEY_FILE="${KEY_FILE:-$HOME/.chargenet-backup.key}"
dest="${2:-$(mktemp -d)}"
mkdir -p "$dest"

[ ! -f "$file.sha256" ] || (cd "$(dirname "$file")" && { sha256sum -c "$(basename "$file").sha256" --quiet 2>/dev/null || shasum -a 256 -c "$(basename "$file").sha256" >/dev/null; }) || { echo "FAIL checksum" >&2; exit 1; }
openssl enc -d -aes-256-cbc -pbkdf2 -pass "file:$KEY_FILE" -in "$file" | tar -xz -C "$dest"
mkdir -p "$dest/files" && tar -xzf "$dest/files.tar.gz" -C "$dest/files" && rm "$dest/files.tar.gz"

tables="$(grep -c '^CREATE TABLE' "$dest/database.sql" || true)"
posts="$(grep -c '^INSERT INTO `[a-z0-9_]*posts`' "$dest/database.sql" || true)"
[ "$tables" -ge 10 ] || { echo "FAIL database.sql has only $tables tables" >&2; exit 1; }
for path in wp-config.php wp-content/uploads wp-content/themes/chargenet; do
	[ -e "$dest/files/$path" ] || { echo "FAIL missing $path" >&2; exit 1; }
done
echo "OK  $file"
echo "    database: $tables tables, $posts INSERT statements for posts"
echo "    files:    $(find "$dest/files" -type f | wc -l | tr -d ' ') files, $(du -sh "$dest/files" | cut -f1)"
echo "    unpacked: $dest"
