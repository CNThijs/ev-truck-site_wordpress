# .htaccess rules (STRATO, Apache)

Compression, browser caching, the AVIF file type and, optionally, serving cached pages without starting PHP. Everything is wrapped in `<IfModule>`, so a module that STRATO does not offer is skipped instead of causing an error. **Not tested on STRATO yet** (DDEV runs nginx): apply, run the checks below, and remove the block if anything looks wrong.

Put the block **above** the `# BEGIN WordPress` line in the `.htaccess` in the WordPress folder (WordPress rewrites only its own block). Keep a copy of the file before you change it.

```apache
# BEGIN ChargeNet performance
<IfModule mod_mime.c>
	AddType image/avif .avif
	AddType image/webp .webp
	AddType font/woff2 .woff2
</IfModule>

# Compression (Brotli when the host has it, otherwise gzip).
<IfModule mod_brotli.c>
	AddOutputFilterByType BROTLI_COMPRESS text/html text/css text/plain text/xml application/javascript text/javascript application/json application/ld+json application/xml image/svg+xml
</IfModule>
<IfModule mod_deflate.c>
	AddOutputFilterByType DEFLATE text/html text/css text/plain text/xml application/javascript text/javascript application/json application/ld+json application/xml image/svg+xml
</IfModule>

# Browser caching. Built theme files have a hash in their name and never change under the same name.
<IfModule mod_expires.c>
	ExpiresActive On
	ExpiresByType image/avif "access plus 1 year"
	ExpiresByType image/webp "access plus 1 year"
	ExpiresByType image/jpeg "access plus 1 year"
	ExpiresByType image/png "access plus 1 year"
	ExpiresByType image/svg+xml "access plus 1 month"
	ExpiresByType image/x-icon "access plus 1 year"
	ExpiresByType font/woff2 "access plus 1 year"
	ExpiresByType text/css "access plus 1 year"
	ExpiresByType application/javascript "access plus 1 year"
	ExpiresByType text/javascript "access plus 1 year"
</IfModule>
<IfModule mod_headers.c>
	<FilesMatch "\.(css|js|woff2|avif|webp)$">
		Header append Cache-Control "public"
	</FilesMatch>
	<FilesMatch "^.+-(?=[A-Za-z0-9_-]*[0-9A-Z])[A-Za-z0-9_-]{8}\.(css|js)$">
		Header set Cache-Control "public, max-age=31536000, immutable"
	</FilesMatch>
</IfModule>

# Serve pages cached by Cache Enabler without starting PHP (optional second step, see below).
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTPS} =on
	RewriteCond %{REQUEST_METHOD} =GET
	RewriteCond %{QUERY_STRING} =""
	RewriteCond %{HTTP_COOKIE} !(wp-postpass|wordpress_logged_in|comment_author)_
	RewriteCond %{REQUEST_URI} /$
	RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/cache-enabler/%{HTTP_HOST}%{REQUEST_URI}https-index.html -f
	RewriteRule ^ /wp-content/cache/cache-enabler/%{HTTP_HOST}%{REQUEST_URI}https-index.html [L]
</IfModule>
# END ChargeNet performance
```

## Protection and security headers (Epic 13)

Two more blocks for the same file, above `# BEGIN WordPress`. Same rule: keep a copy of the old `.htaccess`, apply, run `npm run check:security -- https://chargenet.energy --production` and `wp eval-file check-hardening.php production`.

**Protection** (closed files, no PHP in uploads, no directory listing, no dot-folders except `.well-known`):

```apache
# BEGIN ChargeNet protection
Options -Indexes
<IfModule mod_authz_core.c>
	<FilesMatch "^(readme\.html|readme\.txt|license\.txt|changelog\.txt|wp-config\.php|wp-config-sample\.php|xmlrpc\.php|composer\.(json|lock)|check-(host|hardening)\.php|.*\.(sql|bak|old|log|sh))$">
		Require all denied
	</FilesMatch>
</IfModule>
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^wp-content/uploads/.*\.(php[0-9]?|phtml|phar)$ - [F,L]
	RewriteRule (^|/)\.(?!well-known/) - [F,L]
</IfModule>
# END ChargeNet protection
```

`wp-config.php` is blocked only against direct requests; PHP still reads it. If a plugin needs one of its own `readme.txt` files over HTTP (none of ours do), narrow the first line.

**Security headers:** one list for all environments lives in `inc/security-headers.php`. Pages served from the cache never reach PHP, so on the server print the block from the live site and paste it in:

```
wp eval 'echo chargenet_security_headers_htaccess();'
```

Then add `define( 'CHARGENET_HEADERS_AT_SERVER', true );` to `wp-config.php` so PHP does not send them a second time. The Content Security Policy starts as `Content-Security-Policy-Report-Only`; violations are written to the PHP error log as `CSP violation: …`. Details and the switch to enforcing: `docs/security.md`.

## Why these choices

- **Hashed files for a year, `immutable`:** the theme build names files `main-<hash>.css` (8 characters including a digit or capital letter), so a new build is a new URL. Other CSS and JS also get a year, which is safe because WordPress adds `?ver=` to their URLs. Images and fonts are also safe for a year; if you replace an uploaded image, upload it under a new name.
- **HTML gets no browser cache header:** pages change when you edit them, and Cache Enabler already serves them quickly.
- **Brotli before gzip:** Brotli is about 15% smaller; browsers that do not ask for it get gzip. STRATO may offer only one of the two; the other block is then ignored.
- **The rewrite block is optional.** Without it Cache Enabler still serves cached pages, but through a small PHP file (about 5 ms instead of about 50 ms for a full WordPress page). With it the web server hands out the file directly. It only matches HTTPS GET requests without a query string, without login cookies and with a trailing slash; everything else goes to WordPress as before. If `DOCUMENT_ROOT` on STRATO is not the WordPress folder, the `-f` test never matches and the rule does nothing, which is safe.

## Check after applying

1. Load the site; if you get a 500 error, restore your copy of `.htaccess` (a directive STRATO does not allow causes this).
2. The three `curl` commands printed by `bin/check-host.php`: expect `content-encoding: br` or `gzip` on the HTML and CSS, and `cache-control: public, max-age=31536000, immutable` on the hashed CSS/JS.
3. Request a page twice while logged out (`curl -sI https://chargenet.energy/en/`): the second answer should be faster. `ls wp-content/cache/cache-enabler/` on the server shows the stored pages.
4. Log in and edit a page: the cached copy is cleared when you save.
5. AVIF: open an uploaded `.avif` URL in the browser; it must display, not download (`content-type: image/avif`).
