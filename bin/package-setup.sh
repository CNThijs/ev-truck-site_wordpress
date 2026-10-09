#!/usr/bin/env bash
# Zips what the setup scripts need on the server (they run over SSH with WP-CLI): bin/*.php, bin/*.sh, content/ (pages,
# blog, media, SEO texts). Not the report PDF (10 MB, upload it by hand to wp-content/uploads/chargenet-downloads/,
# docs/content.md) and no secrets. Unzip next to the WordPress folder, then run the commands in docs/launch-runbook.md.
set -euo pipefail
cd "$(dirname "$0")/.."
version=$(sed -n 's/^Version: *//p' web/wp-content/themes/chargenet/style.css | tr -d '\r')
out="dist/chargenet-setup-${version}.zip"
stage=$(mktemp -d)
trap 'rm -rf "$stage"' EXIT
mkdir -p "$stage/chargenet-setup/bin" "$stage/chargenet-setup/content"
cp bin/*.php bin/*.sh "$stage/chargenet-setup/bin/"
rsync -a --exclude 'downloads' --exclude '.DS_Store' content/ "$stage/chargenet-setup/content/"
mkdir -p dist
rm -f "$out"
(cd "$stage" && zip -qr "$OLDPWD/$out" chargenet-setup)
echo "Created $out ($(du -h "$out" | cut -f1))"
