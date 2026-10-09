# Launch runbook (Epic 14)

Every action in this document is performed **by you** in the STRATO panel, over SSH or in WordPress. Nothing here is automated, and nothing goes live without your explicit "go" at each of the three gates:

| Gate | When                | What you approve                                                                           |
| ---- | ------------------- | ------------------------------------------------------------------------------------------ |
| 1    | End of phase B      | Test site signed off (`docs/qa-checklist.md` complete)                                     |
| 2    | Start of launch day | The switch of `chargenet.energy` to the new site (point of no easy return: see "Rollback") |
| 3    | Day 30              | End of the monitoring period                                                               |

**Decision (9 October 2026):** the new site is installed **directly on the main domain**, without a test subdomain. Consequence, accepted by you: from Gate 1 until Gate 2 the public sees a "we'll be back shortly" page (HTTP 503, which search engines treat as temporary) instead of the old site, while you and your testers see the new site. Plan for **2 to 4 hours**, start on a weekday morning, and do not start in the 48 hours around a mailing or newsletter drop. If the window runs over **6 hours**, or a blocking problem appears, roll back (below); the old site is untouched in its own folder and coming back takes minutes.

Confirmed by you: the domain records belong to the hosting package (**no DNS change, no TTL step**), you can change the target folder of a domain in the STRATO panel, and you can create a second MySQL database. Things only the panel itself can show are marked **VERIFY**.

**Never touch:** MX, SPF (TXT), DKIM, DMARC and any other mail records. Email runs on Microsoft 365 and must not notice the launch. Do not touch `portal.chargenet.energy` (separate app).

## People (fill in)

| Role                                 | Name  | Phone / chat | Available from – until |
| ------------------------------------ | ----- | ------------ | ---------------------- |
| Owner and operator (STRATO, domains) | Thijs |              |                        |
| Second pair of eyes (testing, phone) |       |              |                        |
| Marketing (mailings, campaign links) |       |              |                        |
| Developer on call (this repository)  |       |              |                        |

---

## Phase A — Prepare (T-14 days to T-7)

1. **VERIFY in the STRATO panel** (screenshot each answer into your notes):
   - The folder of the old site, and the **target folder** setting of `chargenet.energy` and `www.chargenet.energy`. Write the old folder name on the command sheet at the end of this file; **it is your rollback**.
   - PHP **8.4** is selectable for the new folder; SSH and WP-CLI (`wp --info`) work.
   - `REMOTE_ADDR` shows your real address: after phase B step 6 you can test it (the gate lets your IP through).
   - Downloads from wordpress.org work over SSH (try `wp core download` in a scratch folder). If not, download the plugin zips on your Mac and upload them; I list the exact versions from `composer.lock`.
   - Run `check-host.php` over SSH (`docs/performance.md`) and send me the output.
2. **Search Console and Bing:** the Search Console property for `chargenet.energy` already exists (you confirmed). Check that it is the **domain property** (or both `https://chargenet.energy/` and `https://www.chargenet.energy/` URL-prefix properties) and that your account is an Owner. No "change of address" is needed (same domain). Note the old site's last 3 months of clicks and indexed pages as the before-picture. Import the property into Bing Webmaster Tools if not done.
3. **Tag Manager:** the container `GTM-N73C2BJP` has the GA4 tags and the form triggers (`docs/tracking.md`), and Consent Mode works in preview.
4. **Legal items** in `docs/privacy.md` (flags 1–9) decided or accepted, and GA4 data retention set to 14 months or less.
5. **Printed and emailed links (confirmed in use):** the shortlinks `/rapport2027`, `/rapport2027/download`, `/rapport2027/<code>`, `/routecheck`, `/opbrengst` and the old page addresses appear in emails and **physical newsletters** (QR codes and printed URLs cannot be corrected). They are in `docs/redirects.csv` marked "never remove"; codes with letters, digits, hyphen and underscore are accepted. Send me three real printed examples (one with a code) and any other printed or emailed address that is not in `docs/redirects.csv`; I add them and the tests.
6. **Backups of the old site:** download a complete copy of the old folder to your Mac and note the panel's domain settings (screenshots). Keep this for at least 30 days.

## Phase B — Build the new site offline (T-7 to T-1, the old site is not affected)

WordPress can be installed completely over SSH with WP-CLI, **without the domain pointing at it**. So the new site is built in its own folder first; nobody sees it. You cannot open it in a browser yet; that happens in the window. All commands run in the new folder over SSH.

1. In the panel: create the **MySQL database** for the new site and an **empty folder** `chargenet-new` for the same hosting package, set PHP 8.4 for it. Do **not** point any domain at it yet.
2. Build and upload from your Mac: `npm run package` (theme zip) and `npm run package:setup` (setup scripts and content, 10 MB) → `dist/`. Upload both and the report PDF (`ChargeNet-TR2027.pdf`) next to the folder; unzip the setup zip to `chargenet-setup`.
3. Install WordPress for the final address:
   ```
   wp core download --locale=en_US
   wp config create --dbname=... --dbuser=... --dbpass=... --dbhost=... --skip-check
   wp core install --url=https://chargenet.energy --title=ChargeNet --admin_user=<your own name, not "admin"> --admin_email=thijs@chargenet.energy
   wp config set WP_ENVIRONMENT_TYPE staging --type=constant      # noindex everywhere until go-live
   wp config set DISALLOW_FILE_EDIT true --raw --type=constant
   wp config shuffle-salts
   ```
   Add the mail OAuth constants and (later, at go-live) `CHARGENET_GTM_ID` as described in `docs/integrations.md` and `docs/tracking.md`. Put `ChargeNet-TR2027.pdf` in `wp-content/uploads/chargenet-downloads/`.
4. Plugins and theme: `wp plugin install polylang seo-by-rank-math wpconsent-cookies-banner-privacy-suite two-factor --activate` (Cache Enabler comes at go-live), `wp theme install ../chargenet-0.1.0.zip --activate`. Then, in this order: `wp eval-file ../chargenet-setup/bin/setup-polylang.php`, `.../seed-content.php`, `.../setup-rankmath.php`, `.../setup-wpconsent.php`; `wp rewrite flush --hard`.
5. Accounts: create your editor account, set up Two Factor for both at first login (in the window), remove any default user. Run `wp eval-file ../chargenet-setup/bin/check-hardening.php` (production rules will warn about the environment; that is expected).
6. **The gate and the `.htaccess`.** On your Mac (replace the secret with the output of `openssl rand -hex 16` and use your public IP address from `curl -s ifconfig.me`; add a second `--ip` for another place you test from):
   ```
   node bin/build-maintenance.mjs --secret <random> --ip <your IP> --host chargenet.energy
   node bin/build-redirects-htaccess.mjs > dist/maintenance/redirects.txt
   ```
   Upload `dist/maintenance/maintenance.html` into the new folder. Build the new folder's `.htaccess` in this order: **(a)** the gate block (`dist/maintenance/htaccess-gate.txt`), **(b)** `redirects.txt`, **(c)** the protection block from `docs/htaccess.md`, **(d)** the security-header block (`wp eval 'echo chargenet_security_headers_htaccess();'`), **(e)** the performance block from `docs/htaccess.md`, **(f)** WordPress's own block (`wp rewrite flush --hard` writes it; keep it last). Do not add `CHARGENET_HEADERS_AT_SERVER` yet.
   You can test the gate and the redirect rules on your Mac first: `npm run test:maintenance-gate`, `npm run test:redirect-rules`.
7. Backups ready: set up `bin/backup.sh` and `bin/backup-pull.sh` (`docs/backups.md`), run one backup and one restore test.
8. Walk through `docs/qa-checklist.md` once locally (`ddev start`; the automated suite and the visual checks already pass there) so that the window is spent on server-only things (mail, certificates, caches, tracking, phones).

**GATE 1 — start of the maintenance window.** Tell me "gate 1 approved" and the time you start. Only then do the steps in phase C. If anything in phases A and B is unfinished, do not start.

## Phase C — The maintenance window (old site offline for visitors, 2–4 hours)

C1. **T0w — point the domain.** In the STRATO panel set the target folder of `chargenet.energy` **and** `www.chargenet.energy` to `chargenet-new`. Visitors now get the maintenance page. Write down the time. No DNS record changes (confirmed). Leave MX/TXT alone.

C2. **First checks (you).** `https://chargenet.energy/` from your own network shows the new site (your IP is allowed) with a valid certificate; from your phone **on mobile data** you see the maintenance page until you open `https://chargenet.energy/?cn_preview=<secret>` once. If the certificate or the folder is wrong: Rollback, trigger 1.

C3. **Log in, Two Factor.** Log in at `/wp-admin/`, set up your authenticator app and backup codes, create your editor account and set up its Two Factor too.

C4. **Automated checks** (from the Mac whose IP is allowed; the environment is still `staging`, so the SEO check expects noindex):

```
npm run check:seo -- https://chargenet.energy --staging
npm run check:hreflang -- https://chargenet.energy
npm run check:redirects -- https://chargenet.energy
npm run check:a11y -- https://chargenet.energy
npm run check:security -- https://chargenet.energy
E2E_BASE_URL=https://chargenet.energy npm run test:e2e
npm run perf -- https://chargenet.energy
```

(`check:redirects` and `check:security` will warn about http/www and HSTS until go-live; that is expected. The form test does not submit on a non-local site.) Send me every failure; I fix it in the repository and you upload a new theme zip (`wp theme install ... --force`).

C5. **Manual checks on the real server** (`docs/qa-checklist.md`, sections 2 to 6): form delivery to real mailboxes and authentication headers, the report PDF at `/downloads/ChargeNet-TR2027.pdf`, `/rapport2027`, `/rapport2027/download`, a real printed code link, `/routecheck`, `/opbrengst`, one old blog address, images, animation and forms on your own phones (open the secret link first), the cookie banner, and Tag Assistant (set `CHARGENET_GTM_ID` for this session; GA4 DebugView keeps the test hits out of the reports; delete the test day afterwards or filter it).

C6. **Decide.** Fill in the sign-off table in `docs/qa-checklist.md`. If something is wrong and cannot be fixed within the window, roll back.

**GATE 2 — go live.** Tell me "gate 2 approved". Then:

C7. **Open the site (T-live).**

```
wp config set WP_ENVIRONMENT_TYPE production --type=constant
wp config set CHARGENET_GTM_ID GTM-N73C2BJP --type=constant     # if not set yet
wp config set CHARGENET_HEADERS_AT_SERVER true --raw --type=constant
wp config set WP_CACHE true --raw --type=constant
wp option update blog_public 1
wp plugin install cache-enabler --activate && wp eval-file ../chargenet-setup/bin/setup-cache.php
wp cache flush
```

Then **delete the maintenance-gate block** from `.htaccess` and the file `maintenance.html`. From this second on the public sees the new site.

C8. **Checks, now against production rules** (you; a second person helps):

```
npm run check:redirects -- https://chargenet.energy      # includes http:// and www. variants
npm run check:seo -- https://chargenet.energy --production
npm run check:hreflang -- https://chargenet.energy
npm run check:security -- https://chargenet.energy --production
```

In a private window: the lock icon is valid, no mixed content, `http://` and `www.` give one 301 to `https://chargenet.energy/…`, `/` gives a 301 to `/en/`, view source has no `noindex`, `robots.txt` has no `Disallow: /` and names `sitemap_index.xml`. Smoke tests again (home EN/NL, a project, a post, the report form with the test code, the contact form to your own address, accept cookies once and see `page_view` in GA4 DebugView).

C9. **Warm up and register.** Run `node bin/monitor.mjs https://chargenet.energy` twice (it requests every sitemap page, which fills the page cache, and reports problems). Search Console: submit `https://chargenet.energy/sitemap_index.xml`, URL Inspection → Request indexing for the home page, one Dutch page and one post. Bing: submit the sitemap. Rich Results Test on the home page. Update Google Business Profile and the LinkedIn company page links.

C10. **T+60 decision window.** If a rollback trigger (below) is true and cannot be fixed in 15 minutes, roll back; otherwise write down the time, tell the people in the table, and start the monitoring.

---

## Rollback (timed)

**Triggers:** (1) certificate error or the site does not load over https; (2) the home page or forms return errors (5xx) that a restart of caching/`.htaccess` does not fix; (3) form mail is not delivered; (4) the campaign links (`/rapport2027`, `/routecheck`, the code links) do not land correctly; (5) tracking or consent is broken in a way that affects privacy (Google loads before consent); (6) anything the owner judges unacceptable.

| Minute | Action                                                                                                                                                                                        | Who   |
| ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----- |
| 0      | Decision "roll back" is spoken and written down with the time.                                                                                                                                | Owner |
| 1–3    | STRATO panel: set the target folder of `chargenet.energy` and `www.chargenet.energy` back to the **old folder**.                                                                              | Owner |
| 3–5    | Open `https://chargenet.energy` in a private window: the old site shows. Check one old address and the certificate.                                                                           | Owner |
| 5–8    | Tell the people in the table and stop campaigns that were started. Do not delete the new folder or its database.                                                                              | Owner |
| 8–10   | Put `WP_ENVIRONMENT_TYPE` back to `staging` in the new site's `wp-config.php` (so a stray hit cannot get it indexed), and note what failed.                                                   | Owner |
| After  | I analyse the log and the failed check; fix; repeat from phase C. Request removal of any URLs that were indexed (Search Console → Removals) only if the new URLs got indexed in the meantime. | Dev   |

**Point of no easy return:** the old site does not collect form data, so rolling back loses only submissions made on the new site in the meantime (export them first: Form submissions in the admin, `wp eval-file bin/monitor-server.php` for the numbers) and the consent log rows. After about 24 hours, or once search engines have started to show the new URLs, rolling back also costs search visibility, so use it only for serious problems. The old folder stays untouched for at least 30 days.

---

## Monitoring for 30 days

**Gate 3 is at day 30.**

Daily (one command on your Mac; schedule it with launchd as in `docs/backups.md`, 07:30):

```
node bin/monitor.mjs https://chargenet.energy --psi > ~/chargenet-monitoring/$(date +%F).txt
ssh <user@host> "cd <site folder> && wp eval-file monitor-server.php" >> ~/chargenet-monitoring/$(date +%F).txt
```

(`monitor-server.php` is copied next to `wp-config.php` once; it prints no personal data.) The report lists:

- every sitemap page: status, indexable, title; pages slower than 2 s
- every old address in `docs/redirects.csv`: one hop to the right page
- `robots.txt` and the sitemap
- HSTS and CSP (still report-only is a warning until you enforce it)
- Core Web Vitals field data and lab score for three pages (`--psi`; field data appears only when Chrome has enough visits, so expect "n/a" for the first weeks)
- form submissions per form (24 hours, 7 days, 30 days) and submissions with a mail problem
- the most requested 404 addresses of the last 24 hours (Rank Math 404 monitor): add real old addresses to `docs/redirects.csv` and tell me, I add the rule
- cookie choices and accepted statistics, cron health, environment type, indexing allowed, newest backup

Read the file each morning for the first week, then twice a week. `FAIL` lines mean act today.

### Search Console — weekly list (every Monday for 4 weeks)

1. **Indexing → Pages:** the number of indexed pages grows towards ~80 (sitemap). Open "Why pages aren't indexed": "Not found (404)" lists old addresses without redirect (add them), "Page with redirect" is normal for old addresses, "Crawled – currently not indexed" and "Duplicate without user-selected canonical" should be near zero.
2. **Sitemaps:** status "Success", discovered URLs ≈ sitemap size.
3. **Experience → Core Web Vitals and Page experience:** wait for data (`docs/performance.md`); any "Poor" URL group is a task.
4. **Performance:** queries and pages; compare with the old site's week before (Search Console keeps history). Expect a dip for 1–3 weeks as Google follows the redirects.
5. **Settings → Crawl stats:** 5xx or 4xx spikes.
6. **Links / Manual actions / Security issues:** must be empty.
7. Bing Webmaster Tools: sitemap status and crawl errors.
8. GA4: sessions per channel and the `form_submit` events against the submissions in WordPress (they should match within a few percent).

### End of monitoring — Gate 3

Day 30: you and I review the 30 reports, the Search Console lists and the open items. At **gate 3** you approve: removing the old site's folder from the server after a final download (keep that download), removing the redirect-row exceptions you no longer need, lowering monitoring to the monthly routine (`docs/backups.md`, `docs/plugins.md`), and enforcing the CSP (`docs/security.md`) if not done yet.

## Launch-day command sheet (copy, fill in, print)

```
SSH:        ssh <user@host>
SITE:       <path of chargenet-new>
OLD FOLDER: <path of the old React site>   <- ROLLBACK TARGET
NEW FOLDER: <path of chargenet-new>
GATE:       secret <random>, allowed IPs <list>   (never commit or email this)
THEME ZIP:  dist/chargenet-<version>.zip
OFFSITE:    ~/chargenet-backups-offsite
GA4 / GTM:  GTM-N73C2BJP
CONTACTS:   STRATO support <number>; owner <number>; developer <number>
```
