#!/usr/bin/env bash
# Tests the maintenance gate on a real Apache (Docker image httpd:2.4): visitors get the 503 page, the secret link sets
# a cookie that lets a browser through, a wrong secret does not, and the page itself stays reachable. Needs Docker.
set -euo pipefail
cd "$(dirname "$0")/.."
tmp="$(mktemp -d)"
trap 'docker rm -f cn-gate-test >/dev/null 2>&1 || true; rm -rf "$tmp"' EXIT
secret="abcdef0123456789abcd"
mkdir -p "$tmp/htdocs"
node bin/build-maintenance.mjs --secret "$secret" --ip 203.0.113.5 --host localhost --out "$tmp/out" >/dev/null
cp "$tmp/out/maintenance.html" "$tmp/htdocs/"
cp "$tmp/out/htaccess-gate.txt" "$tmp/htdocs/.htaccess"
echo "<h1>the real site</h1>" > "$tmp/htdocs/index.html"
docker run --rm httpd:2.4 cat /usr/local/apache2/conf/httpd.conf > "$tmp/httpd.conf"
sed -i.bak -e 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' -e 's/^#LoadModule headers_module/LoadModule headers_module/' "$tmp/httpd.conf"
sed -i.bak -e '/<Directory "\/usr\/local\/apache2\/htdocs">/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' "$tmp/httpd.conf"
docker run -d --name cn-gate-test -p 8090:80 -v "$tmp/htdocs:/usr/local/apache2/htdocs" -v "$tmp/httpd.conf:/usr/local/apache2/conf/httpd.conf" httpd:2.4 >/dev/null
for _ in $(seq 1 30); do curl -s -o /dev/null http://localhost:8090/maintenance.html && break; sleep 0.5; done

fail=0
check() { if [ "$2" = "$3" ]; then echo "ok   $1"; else echo "FAIL $1 (got '$2', wanted '$3')"; fail=1; fi; }
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }

check "visitor gets 503" "$(code http://localhost:8090/)" 503
check "deep link gets 503" "$(code http://localhost:8090/en/about/)" 503
check "503 page shows the message" "$(curl -s http://localhost:8090/ | grep -c 'almost ready')" 1
check "maintenance page itself is reachable (as the error page)" "$(curl -s http://localhost:8090/ | grep -c 'Onze nieuwe website')" 1
check "wrong secret does not open the site" "$(code 'http://localhost:8090/?cn_preview=wrongwrongwrongwrong')" 503
check "secret link lets the browser in" "$(code "http://localhost:8090/?cn_preview=$secret")" 200
jar="$tmp/jar"; curl -s -o /dev/null -c "$jar" "http://localhost:8090/?cn_preview=$secret"
check "cookie is set" "$(grep -c cn_preview "$jar")" 1
check "cookie keeps the browser in" "$(code -b "$jar" http://localhost:8090/en/about/)" 404
check "search engines are told noindex" "$(curl -sI http://localhost:8090/ | grep -ci 'x-robots-tag: noindex')" 1
exit $fail
