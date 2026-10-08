# Plugin policy (Epic 13)

Free plugins only. A plugin is added only when the theme cannot do the job in a few lines, it is maintained (updated in the last year, active support), and it is listed here with an owner. Everything in `composer.json` (wpackagist) is installed from wordpress.org; no plugin from another source. `bin/check-hardening.php` fails when an active plugin is not on this list or an installed plugin is inactive.

**Update owner of WordPress core, the theme and every plugin: the administrator account `thijs@chargenet.energy`.** Procedure and rollback: `docs/backups.md`. Security notices go to `security@chargenet.energy`.

| Plugin                                        | Why it is needed                                                                                                         | Data it handles / sends out                                                                                      | Alternative considered                                |
| --------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------- |
| Polylang                                      | English and Dutch content with prefixed URLs and linked translations.                                                    | Language cookie `pll_language` (essential, set by the theme's own script).                                       | Multisite or two installs: far more work.             |
| Rank Math SEO (free)                          | Structured data, breadcrumbs, 404 monitor, redirects of author pages. Sitemaps are theme code.                           | Runs without an account; other modules (analytics, content AI, link counter) are off (`bin/setup-rankmath.php`). | Writing structured data by hand for 15 templates.     |
| WPConsent (free)                              | Cookie banner, blocking of services until consent, Google Consent Mode v2, cookie list.                                  | Stores the choice in the browser; the consent log is theme code. Its own banner statistics are off.              | A banner written in the theme: legal risk, more code. |
| Cache Enabler (free)                          | Page cache on shared hosting (production only).                                                                          | Writes HTML files to `wp-content/cache`. No external calls.                                                      | WP Super Cache, W3 Total Cache, LiteSpeed Cache.      |
| Two Factor (free, WordPress.org contributors) | Authenticator-app login and backup codes for administrators and editors. Required by the theme (`inc/security-2fa.php`). | Secrets stay in the user's meta in the database. No external service.                                            | Limit Login Attempts / Wordfence: larger, accounts.   |

Not installed on purpose: page builders, security suites that call home, backup plugins (the server scripts do it), SMTP plugins (mail is theme code), analytics plugins (Tag Manager after consent), form plugins (forms are theme code), jQuery-based sliders.

## Adding or removing a plugin

1. Add the row above (reason, data, alternative). 2. `composer require wpackagist-plugin/<slug>` and `docs/install.md`. 3. Add the folder name to `$cn_allowed` in `bin/check-hardening.php`. 4. Remove unused plugins completely (deactivated plugins still carry vulnerabilities).
