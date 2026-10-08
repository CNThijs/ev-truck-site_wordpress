# Security baseline (Epic 13)

Security contact and notifications: **security@chargenet.energy** (published in `/.well-known/security.txt`, served by the theme). Update owner of WordPress, theme and plugins: the administrator account `thijs@chargenet.energy` (`docs/plugins.md`).

**Position of the Security page:** the website's Security page says ChargeNet works with an ISMS compliant with the ISO 27001 principles. The policy framework follows ISO/IEC 27001:2022 but ChargeNet is **not yet certified**. Decision (owner, 8 October 2026): the page wording stays as it is. Revisit it when certification is planned or if a customer reads the heading as a certificate.

## Checks (run them on every environment)

| Command                                                                                 | What it checks                                                                                                                                                                                                                                                                                                                                            |
| --------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `npm run check:security -- <url> [--production]`                                        | Over HTTP: security headers on both language homes, CSP quality, HSTS and the https redirect, XML-RPC, REST user list, `?author=` enumeration, login error text, exposed files (`wp-config.php` copies, `.env`, `.git`, `debug.log`, directory listings, `readme.html`, `license.txt`), `security.txt`. `--production` demands an enforcing CSP and HSTS. |
| `wp eval-file bin/check-hardening.php [production]` (locally `npm run check:hardening`) | On the server: file editing off, debug off, salts, registration, default role, users and administrators, two-factor for every administrator and editor, approved plugins only, no inactive plugins, updates pending, permissions, no PHP in uploads, no dumps or check scripts in the web root, `.htaccess` blocks present.                               |

On STRATO copy `check-hardening.php` next to `wp-config.php`, run it over SSH, delete it. CI runs `check:security` (local rules) on every push.

## What the site does

- **Theme (`inc/security.php`):** file editing off, XML-RPC refused (403, including `system.multicall`), REST user list hidden for visitors, `?author=` answers 404 (Rank Math redirects author pages first), one generic login error, login throttling (5 failed attempts per IP + user name in 15 minutes block that pair for 15 minutes, even with the right password), no generator tag, no pingback header, `security.txt`.
- **Two-factor (`inc/security-2fa.php` + the Two Factor plugin):** every administrator and editor must set up an authenticator app (TOTP) and backup codes. Until then the admin only opens the profile page. Email codes are disabled on purpose. **Lock-out recovery:** a backup code; or over SSH `wp user meta delete <id> _two_factor_enabled_providers` for that user, then set it up again.
- **Roles:** only `administrator` (1 to 3 people) and `editor`; visitors cannot register; default role subscriber. Use an editor account for daily writing, the administrator account for updates and settings.
- **Headers (`inc/security-headers.php`):** HSTS (1 year, subdomains), `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy` (camera, microphone, geolocation, payment, USB off), `Cross-Origin-Opener-Policy`, Content Security Policy. Pages from the page cache never reach PHP, so on STRATO the same list goes into `.htaccess` (`docs/htaccess.md`) and `CHARGENET_HEADERS_AT_SERVER` stops PHP from repeating them.

## Content Security Policy: report-only, then enforce

Allowed: our own origin; scripts also from `www.googletagmanager.com` and `challenges.cloudflare.com` (Turnstile, only when switched on); connections and images to Google Analytics/Tag Manager domains; frames for Turnstile; no `object`, `base`, `form-action` or `frame-ancestors` outside our origin. **No map provider is approved**; add its domains when one is chosen.

`'unsafe-inline'` is allowed for scripts and styles. Reason: the page cache makes per-request nonces impossible, and the consent banner and Tag Manager write inline code. This still blocks scripts and frames from any other origin, plugins, base-tag and form hijacking, and clickjacking. To go stricter later, move the remaining inline scripts into files and use hashes.

1. Today the policy is `Content-Security-Policy-Report-Only`. Violations are written to the PHP error log (`CSP violation: <directive> blocked <address> on <page>`).
2. Browse the whole site (both languages, banner accepted and rejected, forms, the Turnstile page when on) for a few days and read the log. Fix or allow what is legitimate.
3. Enforce with `define( 'CHARGENET_CSP_MODE', 'enforce' );` in `wp-config.php` (and regenerate the `.htaccess` block). Re-run `check:security --production`.
4. After enforcing, any new external service (analytics tool, video, map) needs its domain added to `chargenet_csp()` first, otherwise it is blocked.

## File permissions (STRATO, over SSH)

```
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 wp-config.php          # 640 if the web server group must read it
chmod 600 ~/.chargenet-backup.key
chmod 700 <folder>/backup.sh
```

Nothing world-writable. `wp-content/uploads` and `wp-content/cache` must be writable by the PHP user only. Run the commands again after a restore.

## wp-config.php (on the server, not in the repository)

```php
define( 'WP_DEBUG', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_ENVIRONMENT_TYPE', 'production' );
define( 'CHARGENET_GTM_ID', 'GTM-XXXXXXX' );
define( 'WP_CACHE', true );
define( 'CHARGENET_HEADERS_AT_SERVER', true );   // once the .htaccess header block is in place
@ini_set( 'display_errors', '0' );               // STRATO's php.ini cannot be changed: this covers a host default of 1
```

All 8 keys and salts must be unique and long; generate new ones at https://api.wordpress.org/secret-key/1.1/salt/ and change them after any suspected compromise. Secrets (mail, Turnstile) are constants here only (`docs/integrations.md`).

## When something goes wrong

1. Report arrives at `security@chargenet.energy`: acknowledge within 2 working days, fix, tell the reporter.
2. Suspected compromise: restore from backup (`docs/backups.md`), change the salts and all administrator passwords, reset two-factor, review users and plugins, check `check-hardening` and `check:security`.
3. Personal data involved: the GDPR clock (72 hours to the authority, if the risk requires it) starts when you become aware. **LEGAL:** the decision to notify is yours, not the scripts'.
