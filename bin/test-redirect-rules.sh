#!/usr/bin/env bash
# Tests the generated .htaccess redirect block on a real Apache (Docker image httpd:2.4) with the same check as the
# site: node bin/check-redirects.mjs --no-target. No WordPress involved; only the server rules. Needs Docker.
set -euo pipefail
cd "$(dirname "$0")/.."
tmp="$(mktemp -d)"
trap 'docker rm -f cn-redirect-test >/dev/null 2>&1 || true; rm -rf "$tmp"' EXIT
mkdir -p "$tmp/htdocs"
node bin/build-redirects-htaccess.mjs --base http://localhost:8089 > "$tmp/htdocs/.htaccess"
docker run --rm httpd:2.4 cat /usr/local/apache2/conf/httpd.conf > "$tmp/httpd.conf"
sed -i.bak -e 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' "$tmp/httpd.conf"
sed -i.bak -e '/<Directory "\/usr\/local\/apache2\/htdocs">/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' "$tmp/httpd.conf"
docker run -d --name cn-redirect-test -p 8089:80 -v "$tmp/htdocs:/usr/local/apache2/htdocs" -v "$tmp/httpd.conf:/usr/local/apache2/conf/httpd.conf" httpd:2.4 >/dev/null
for _ in $(seq 1 30); do curl -s -o /dev/null http://localhost:8089/ && break; sleep 0.5; done
node bin/check-redirects.mjs http://localhost:8089 --no-target
