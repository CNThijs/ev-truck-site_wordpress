# Tracking and consent log (Epic 11)

Code: `inc/consent.php` (banner texts), `inc/tracking.php` (loader, campaign cookie, consent log), `bin/setup-wpconsent.php` (plugin settings, cookie list).

## Tag Manager

Nothing loads from Google until the visitor accepts statistics. Set the container in `wp-config.php` (not in the repository):

```php
define( 'CHARGENET_GTM_ID', 'GTM-XXXXXXX' );
```

Without the constant nothing is added. The loader is printed as `type="text/plain"` with WPConsent attributes; WPConsent turns it into a real script after consent and sends Google Consent Mode v2 state (default denied). Build the GA4 tags inside GTM: page views, and triggers on the `form_start`, `form_submit` and `form_error` dataLayer events (`docs/integrations.md`). Turnstile (Cloudflare) is a security service on the contact forms only; it is not a tracking cookie.

## Campaign cookie

After statistics are accepted, the UTM parameters (`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`) of a landing URL are stored in the cookie `cn_campaign` for 30 days. The form script (`blocks/_shared/form.js`) fills the hidden `cn_utm_*` fields from the URL, else from this cookie, so a request sent after browsing is linked to the campaign. The server renders the fields empty, because the page may come from the page cache. `/rapport2027` and the DM shortlinks already redirect with UTM parameters (`inc/redirects.php`).

## Consent log

Every saved choice is posted to `POST /wp-json/chargenet/v1/consent` and stored in table `{prefix}chargenet_consent`: random visitor ID (kept in the browser as `cn_consent_id`), UTC time, statistics, marketing, language, banner text version (hash of the texts, changes when the wording changes). No IP address, no browser details. The endpoint accepts at most 60 choices an hour per address (hashed, not stored; `CHARGENET_CONSENT_PER_HOUR`) and only reuses a visitor ID that is already in the log. A daily cron deletes rows older than 36 months. The record holds no personal data, so there is no exporter or eraser.

## Check

- Banner shows in EN and NL; Reject and Accept equal in size; footer "Cookie settings" reopens it.
- Before accepting: no `googletagmanager.com` request, no `cn_campaign` cookie.
- After accepting on `/nl/?utm_source=x&utm_campaign=y`: GTM script present, cookie set, one row in the log.

## Field Core Web Vitals

`assets/src/js/vitals.js` pushes one `web_vitals` event per page view when the page is hidden, with `vitals_lcp` (ms), `vitals_cls` and `vitals_inp` (ms). Numbers only. The event sits in the dataLayer and Tag Manager only runs after statistics consent. **Owner step in GTM:** add a custom-event trigger `web_vitals` and a GA4 event tag that sends the three variables as event parameters (register them as custom metrics in GA4 to report on them).
