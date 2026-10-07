# Forms, emails and integrations

Three forms: the **contact form**, the **trend report form** and the **newsletter sign-up** (`docs/blog.md`). Every submission is stored in WordPress and the visitor gets an email. Free plugins only: all of this is theme code (`inc/forms/`, `inc/locations.php`, the blocks `contact-form`, `trend-report-form` and `locations-list`). The old site sent its mail from the browser through EmailJS with hard-coded IDs; none of that was reused (revoke the old EmailJS key when the new site is live).

```
visitor ──► form (HTML, works without JavaScript; view.js sends it with fetch)
        ──► admin-post.php?action=chargenet_form        inc/forms/handler.php
              nonce → honeypot → minimum time → Turnstile (if on) → rate limit → validation → report code
        ──► submission stored (private post type)        inc/forms/storage.php
        ──► email sent through wp_mail (Microsoft 365)   inc/forms/mail.php
              failure → status "retry", cron retries up to 5 times
```

## Where things are

| What                         | Where                                                                                                                |
| ---------------------------- | -------------------------------------------------------------------------------------------------------------------- |
| Form sections (blocks)       | `blocks/contact-form`, `blocks/trend-report-form`; markup in `inc/forms/render.php`; script `blocks/_shared/form.js` |
| Pages with the forms         | Contact (`content/pages/contact.php`), Trend report (`content/pages/rapport2027.php`)                                |
| Submissions                  | WordPress admin → **Form submissions**                                                                               |
| Report codes (CSV import)    | **Form submissions → Report codes**                                                                                  |
| Email texts, addresses, spam | **Form submissions → Settings and emails**                                                                           |
| Locations                    | WordPress admin → **Locations**; shown by the **Locations list** section                                             |
| Checks                       | `npm run test:forms` (`bin/test-forms.php`), `bin/test-mail.php`                                                     |

## Submissions (storage)

A **private custom post type** (`chargenet_submission`) was chosen over a custom table: the admin list, search, bulk delete and the WordPress privacy tools come with it, and the volume is small. It has no public URL, is not in the REST API, is excluded from search and sitemaps, and every capability needs `manage_options` (administrators only). Nobody can add one by hand.

Stored per submission: form (contact / trend report), language, name, email, company, message, **code used** (or none: the trend report without a code), consent (yes, the exact sentence shown, the time), campaign parameters (`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, used in Epic 11), email status (`pending`, `sent`, `retry`, `failed`), number of attempts, last error, date.

Admin list: columns, filters (form, email status), search over name, email, company, code and message, **Export CSV** (follows the filters; cells that start with `=`, `+`, `-` or `@` are prefixed so spreadsheets do not run them), delete (single and bulk), and **Send the email again** on the detail screen.

The IP address is **not** stored. Rate limiting uses a hashed address in a transient that expires after at most an hour.

## Report codes

- A private table `wp_chargenet_codes` (code, label, imported at, use count, last use). The list is checked **on the server only** and never sent to the browser.
- **Import:** Form submissions → Report codes → choose a CSV. One code per line, or a header row with a column `code` (and optionally `label`, `company` or `name`). Comma, semicolon and tab are understood. _Add_ keeps existing codes and their counts; _Replace_ also removes codes that are not in the file (counts of the codes that stay are kept). **Download the list with use counts** from the same screen.
- **Matching:** case and anything that is not a letter or digit are ignored (`qg-bR uD` = `QGBRUD`). 3 to 32 characters. Both the 6-character and the 5-character codes work.
- **Reusable:** a use is recorded (the code on the submission, `use_count`, `last_used_at`) and the code stays valid.
- **Guessing:** five wrong codes from one visitor lock code attempts for 15 minutes (the visitor can still request the report without a code). Six successful requests per hour per visitor and form is the other limit. Both are per hashed address, so one person cannot lock out others. The code space is millions of times larger than the list, so these limits make guessing impractical.
- The 406 codes of the old site were readable by anyone in its JavaScript. Use a fresh list if you want the codes to stay private.

## The forms

- **Contact:** name (2+), email, message (10+), consent. One email goes to the team address, the visitor is in BCC.
- **Trend report:** code + email + consent (primary button), or, behind **I do not have a code** (a `<details>` element that works without JavaScript), name + company + email + consent. Both paths send the same email with the link to the report.
- **Without JavaScript:** a normal POST; the handler redirects back to the page with the result (kept 10 minutes, shown next to the fields, the entered values kept). **With JavaScript:** `view.js` sends the form with fetch, shows the server's errors next to the fields (`aria-invalid`, `aria-describedby`, a live alert region), moves focus to the first invalid field and announces success. Validation is always done on the server.
- **Spam protection:** nonce, a hidden honeypot field, a signed timestamp (a form sent within 3 seconds is refused), the rate limits above, and **Cloudflare Turnstile** when switched on (below). A bot that fills the honeypot is told it worked; nothing is stored.
- **Turnstile:** create a free site in Cloudflare, enter the site key under Settings and emails, and add the secret to `wp-config.php`: `define( 'CHARGENET_TURNSTILE_SECRET', '…' );`. Turnstile needs JavaScript and loads a script from Cloudflare: add it to the cookie consent plugin when that is installed (Epic 11). Without both keys it is off.
- **Report link:** `https://chargenet.energy/downloads/ChargeNet-TR2027.pdf`, the same address as on the old site. The file is **not in git** (10 MB): keep it in `content/downloads/` (the seeder copies it) or upload it to `wp-content/uploads/chargenet-downloads/ChargeNet-TR2027.pdf`; the theme serves it at the fixed address (`inc/forms/download.php`). The address can be changed under Settings and emails.
- **Newsletter:** email + consent, stored as a submission of the form "Newsletter" (kept until the person unsubscribes, not deleted by the retention period), one confirmation email from noreply@ (no team copy), a repeat sign-up stores and sends nothing.
- **Consent:** a required checkbox with the sentence "I agree that ChargeNet processes my details to handle this request, as described in the privacy policy" and a link to the privacy policy. No marketing opt-in: no marketing is planned. The sentence is stored with the submission.

## Emails

One template per form and language (four in all), in **Settings and emails**: subject and body with placeholders. An empty field means the default text. The body is HTML (edited in the visual editor); the **plain-text version is made from the same text** and sent as the second part of the message. Placeholders: `{name}` `{email}` `{company}` `{message}` `{code}` `{report_url}` `{site_name}` `{date}` `{greeting}` (`Hello Ann,` or `Hello,`; Dutch `Beste Ann,` or `Goedendag,`). Values are escaped. **Send a test email** sends a template with sample data to one address.

| Email        | To                    | From                                        | Reply-To                   | BCC                   |
| ------------ | --------------------- | ------------------------------------------- | -------------------------- | --------------------- |
| Contact      | info@chargenet.energy | visitor's **name** at info@chargenet.energy | visitor (name and address) | the visitor           |
| Trend report | the visitor           | ChargeNet <noreply@chargenet.energy>        | info@chargenet.energy      | info@chargenet.energy |

The addresses are settings (defaults as in the table).

**Why the contact email is not sent "from" the visitor's address.** SPF, DKIM and DMARC let a receiving server check that a message really comes from the domain in its From address. A message that says it is from `anna@gmail.com` but is sent by our server fails these checks for Gmail's domain: Gmail, Outlook and others then reject it or put it in spam, and your own DMARC policy (`p=quarantine`) would treat any mail we send as `@chargenet.energy` from another server the same way. So the From address is always an authenticated chargenet.energy mailbox; the visitor's name is the display name and their address is in **Reply-To**, so "Reply" in the team's mailbox answers the visitor. (The visitor's own BCC copy shows the same message; replying to it writes to the team address.)

**BCC is never a visible header.** With SMTP the BCC address only exists in the delivery envelope. Without SMTP configured (local development, or a host that sends with PHP's own mail) PHP would write a `Bcc:` header into the message, so then the team copy (or the visitor copy) is sent as a **second, identical message** instead. `bin/test-forms.php` checks both cases.

**Delivery status and retries.** The email is sent when the form is submitted. The visitor always sees the confirmation (the submission is stored). If sending fails, the submission shows **Failed, will retry** and a WordPress cron job retries after 5 minutes, 30 minutes, 2 hours and 12 hours (five attempts in all); then it shows **Failed**. The last error is on the submission. Use **Send the email again** after fixing the cause. WordPress cron runs when the site gets visits; on low traffic, call `wp-cron.php` from the hosting's cron if retries must be punctual.

## Sending through Microsoft 365

The domain's mail is on Microsoft 365 and its SPF record allows only Microsoft (`v=spf1 include:spf.protection.outlook.com -all`), so the site sends through Microsoft 365 too: then SPF and DKIM pass with the DNS records you already have. (Mail sent from the STRATO web server would fail SPF with `-all` and be quarantined by DMARC.) The theme uses PHPMailer's SMTP with **OAuth2 (client credentials, XOAUTH2)**: no password in the site, and no plugin. `inc/forms/class-chargenet-mail-oauth.php` fetches and caches the token.

**I could not test this against your tenant.** Test it with the steps below before you rely on it.

Setup, once, by a Microsoft 365 administrator:

1. **Mailboxes.** A mailbox the site logs in as (`CHARGENET_SMTP_USER`), for example `info@chargenet.energy` itself, with permission to **send as** `info@` and `noreply@`. If `noreply@chargenet.energy` does not exist, create it as a shared mailbox (free) and give the login mailbox _Send As_ on it.
2. **App registration** (Microsoft Entra admin center → App registrations → New): single tenant. Note the _Application (client) ID_ and _Directory (tenant) ID_. Under _Certificates & secrets_ create a client secret (note its expiry date; calendar a reminder).
3. **API permission:** _APIs my organization uses_ → **Office 365 Exchange Online** → _Application permissions_ → **SMTP.SendAsApp** → grant admin consent.
4. **Register the app in Exchange** (PowerShell, `ExchangeOnlineManagement`): `New-ServicePrincipal -AppId <client id> -ObjectId <enterprise application object id>`, then give it access to the mailbox: `Add-MailboxPermission -Identity info@chargenet.energy -User <service principal id> -AccessRights FullAccess`. The _object id_ is the one of the **Enterprise application**, not of the app registration.
5. **SMTP AUTH** must be allowed for the login mailbox: `Set-CASMailbox -Identity info@chargenet.energy -SmtpClientAuthenticationDisabled $false`.
6. In `wp-config.php` on the server (never in git):

```php
define( 'CHARGENET_SMTP_MODE', 'oauth' );
define( 'CHARGENET_SMTP_USER', 'info@chargenet.energy' );
define( 'CHARGENET_MS_TENANT_ID', '…' );
define( 'CHARGENET_MS_CLIENT_ID', '…' );
define( 'CHARGENET_MS_CLIENT_SECRET', '…' );
// Optional: CHARGENET_SMTP_HOST (default smtp.office365.com), CHARGENET_SMTP_PORT (default 587).
```

If your tenant still allows a mailbox password for SMTP (Microsoft is phasing this out), `define( 'CHARGENET_SMTP_MODE', 'basic' ); define( 'CHARGENET_SMTP_PASS', '…' );` works instead of the three `CHARGENET_MS_*` constants.

If the login mailbox may not send as one of the From addresses, Microsoft rejects the message ("SendAsDenied"); the error is on the submission.

## DNS: SPF, DKIM, DMARC

Read from the live DNS on 5 October 2026. With Microsoft 365 as the only sender **no new records are needed**:

| Record | Now                                                                                            | Action                                                                                                                                            |
| ------ | ---------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| SPF    | `chargenet.energy TXT "v=spf1 include:spf.protection.outlook.com -all"`                        | Keep. Do **not** add STRATO or other senders: the site sends through Microsoft.                                                                   |
| DKIM   | `selector1._domainkey` and `selector2._domainkey` are CNAMEs to `…chargeahead.onmicrosoft.com` | Keep. In the Microsoft 365 Defender portal (Email & collaboration → Policies → DKIM) check that DKIM signing is **enabled** for chargenet.energy. |
| DMARC  | `_dmarc TXT "v=DMARC1; p=quarantine; rua=mailto:support@chargenet.energy; pct=100"`            | Keep (reports to support@, as you asked). After the tests pass, `p=reject` is the next step, not needed now.                                      |

A second MX record points to Mailgun (`mxa.eu.mailgun.org`). If another system also sends mail as chargenet.energy through Mailgun, SPF would need `include:mailgun.org`; the site itself does not use it.

### Delivery test (Gmail and Outlook)

1. Over SSH on the live site: `wp eval-file bin/test-mail.php your-address@gmail.com`, then the same with an Outlook.com address. It sends the contact and the trend report email in English and Dutch (without the team copy) and prints sent/failed.
2. Or use **Settings and emails → Send a test email**.
3. For each inbox check: arrived in the inbox (not spam); the sender shows the right name and `chargenet.energy`; the Dutch and English texts and the report link; the plain-text version (Gmail: "Show original"; Outlook: view source).
4. In Gmail choose **⋮ → Show original**: SPF, DKIM and DMARC must all say **PASS**. In Outlook open the message headers: `Authentication-Results` must show `spf=pass`, `dkim=pass`, `dmarc=pass`.
5. Submit the real forms once on the live site and check the BCC copy in the info@ mailbox, and that the visitor message shows **no** `Bcc:` header (Show original).
6. Optional: send a test to mail-tester.com for a spam score.

## dataLayer events (for Epic 11)

`view.js` pushes to `window.dataLayer` (nothing else is sent, no personal data):

| Event         | When                         | Fields                                                                                 |
| ------------- | ---------------------------- | -------------------------------------------------------------------------------------- |
| `form_start`  | first focus in a form        | `form_id` (`contact`, `trend_report`), `form_mode` (`code`, `nocode`), `form_language` |
| `form_submit` | the server accepted the form | same                                                                                   |
| `form_error`  | the server refused the form  | same, plus `error_fields` (field names only, for example `code,consent`)               |

Campaign parameters (`utm_*`) are carried in hidden fields and stored on the submission; Epic 11 can also read them from the URL.

## Privacy

Personal data stored per submission: name, email address, company, message, the report code used, the consent sentence and its time, campaign parameters, language, the email delivery status and error. Nothing else; no IP address, no user agent. Short-lived (at most 10 minutes) copies of the entered values exist in WordPress transients only when a form is sent without JavaScript and refused.

- **Exporter and eraser:** registered with WordPress (Tools → Export / Erase Personal Data), matching on the email address. The code's use count stays (it holds no personal data).
- **Retention:** submissions older than the retention period (default **12 months**, setting) are deleted every day by a cron job. This matches the 365 days in the privacy policy.
- **Third parties:** Microsoft 365 (mail) and, when switched on, Cloudflare Turnstile.
- **Privacy policy:** now covers the website forms (details of processing, sharing with Microsoft 365, 365 days retention) in English and Dutch, dated 6 October 2026. It is a legal text: have it checked. When Cloudflare Turnstile is switched on, add it to the sharing table and to the section on transfers outside the EEA (Cloudflare is a US company).

## Locations

**Locations** in the admin menu: title, street, postal code, city, access (private, semi-public, public) and charge points as text; ordered by the "Order" field and then by title. Each language has its own entries (Polylang translates the type: use the language links in the list). The **Locations list** section (`chargenet/locations-list`) prints them as an accessible list (headings, `<address>`, a description list) and prints nothing while there are none. There is no live feed; add a map only after you approve one (then load it after cookie consent). To show it, add the section to a page in the editor (for example on Locations).

## Adding a new form

1. Add the fields and rules to `chargenet_form_validate()` and the form id to `chargenet_form_process()` in `inc/forms/handler.php` (one branch per form), and the form id to `chargenet_form_label()` in `storage.php`.
2. Write the markup with the helpers in `inc/forms/render.php` (`chargenet_form_open`, `chargenet_form_field`, `chargenet_form_consent`, `chargenet_form_close`) and a function like `chargenet_render_contact_form()`.
3. Add a section block (`npm run make:section <name> --view`), copy `blocks/contact-form` and call the function from `render.php`; `view.js` only needs `enhanceForms(document)`.
4. Add a template (`chargenet_mail_default_template()`), the recipients (`chargenet_mail_envelope()`) and the template fields on the settings screen.
5. Add checks to `bin/test-forms.php`, texts to the Dutch `.po` file, and the new personal data to the list above.

## Checks

`npm run test:forms` (needs `ddev start`) runs `bin/test-forms.php` against the local database: code parsing and matching, the handler (honeypot, minimum time, validation, consent, rate limits, both forms and both trend paths, Dutch messages), templates (no placeholder left, escaping, header injection, HTML and plain text), the real MIME message (no `Bcc:` header with SMTP, plain-text part), no-SMTP behaviour, failure and retry, the privacy exporter and eraser, retention and the CSV formula guard. No email leaves the machine.

## Open questions

- **Microsoft 365 sending is untested** against your tenant (see above); run the delivery test after setting it up.
- **Privacy policy:** the new form text is a draft for legal review. The data location of Microsoft 365 is stated as "EU" (confirmed by you).
- **Report PDF:** is `ChargeNet-TR2027.pdf` (copied from the old site, dated 25 September) still the file to send?
- **Locations data:** no locations were added; add them in the admin when you have them, or send a list and I will import it.
- **Turnstile:** switch it on when a Cloudflare site and keys exist, and add it to the cookie consent list.
