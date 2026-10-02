#!/usr/bin/env bash
# Builds the theme and zips it for upload (WordPress > Appearance > Themes > Add New > Upload, or SFTP).
set -euo pipefail
cd "$(dirname "$0")/.."

theme=web/wp-content/themes/chargenet
version=$(sed -n 's/^Version: *//p' "$theme/style.css" | tr -d '\r')
out="dist/chargenet-${version}.zip"

npm run build
stage=$(mktemp -d)
trap 'rm -rf "$stage"' EXIT

rsync -a \
	--exclude 'assets/src' --exclude 'hot' --exclude 'node_modules' \
	--exclude '*.map' --exclude '.DS_Store' --exclude '.env*' --exclude '*.local' \
	"$theme/" "$stage/chargenet/"

# Fail rather than ship an unbuilt theme.
test -f "$stage/chargenet/assets/dist/.vite/manifest.json" || { echo "Missing build manifest" >&2; exit 1; }

mkdir -p dist
rm -f "$out"
(cd "$stage" && zip -qr "$OLDPWD/$out" chargenet)
echo "Created $out"
