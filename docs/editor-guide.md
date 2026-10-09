# Editor guide: maintaining the ChargeNet website

For the people who edit the site after launch. No code needed. If something here is unclear or does not match the screen, tell the site owner; the technical notes are in the other files in `docs/`.

**Address of the admin:** `https://chargenet.energy/wp-admin/`. Everyone logs in with their own account and an authenticator app (two-factor). Writers get the **Editor** role, the **Administrator** role is for updates and settings only (`docs/security.md`).

## The five rules

1. **Every page and post exists once per language.** English and Dutch are separate pages, linked by Polylang. Always make both.
2. **Pages are built from sections**, not from loose text. Start with a Hero (it holds the page's one main heading), then add sections.
3. **Images need alt text** (in the language of the page). Decorative backgrounds do not.
4. **Preview before publishing**, on a phone width too.
5. **Never edit the theme, plugins or settings** unless you are the administrator.

## Add a page from sections

1. **Pages → Add New.** In the sidebar choose the language first (Languages box: English or Nederlands).
2. Open the block inserter (+) → **Patterns → ChargeNet pages** and pick **Standard page** (or **Standaard pagina** for Dutch). You get a Hero and a few sections to adapt. (Or start empty and add blocks from the **ChargeNet sections** category: Hero, Feature grid, Feature columns, Statistics, Steps, Accordion, Team, Logo strip, Card slider, Post grid, Rich text, Rich text with image, Call-to-action band, Contact form, Trend report form, Locations list.)
3. Fill each section: eyebrow, heading, text, image, buttons. Titles are **sentence case**, short. The Hero heading is the page title for Google: say what the page is about in plain words.
4. In the **right sidebar** of each section open **Section Settings**: background (light, paper, dark), spacing, and **animation** (leave "none" unless asked; the approved presets are fade-rise, stagger, parallax, draw, counters, horizontal). Anchor ID is for links like `#contact`.
5. **Page settings (sidebar):** Permalink (short, lowercase; Dutch pages get Dutch words, for example `over-ons`); the **Rank Math** box at the bottom: set the SEO title (about 50–60 characters) and description (about 150 characters) in the page's language.
6. **Translate:** in the Languages box click the **+** next to the other language. Optionally use "Copy content to translation", then translate every text and **pick the Dutch copy of each image** (so the alt text is Dutch).
7. **Menus:** Appearance → Menus: add the page to the menu of **each** language (Primary, Footer) and save.
8. **Preview**, then **Publish**. After publishing open the page on the live site and click every button once.

Things that look wrong but are not: the editor shows forms as a preview you cannot click; counters show their final number in the editor.

## Publish a blog post (News)

1. **Posts → Add New**, choose the language in the Languages box.
2. **Title**: the headline. **Featured image**: landscape, with alt text. **Excerpt**: one or two sentences, used on cards and in search results.
3. Write the text with normal paragraphs and headings. Use **h2** for sections (at least three h2 headings produce the table of contents), never skip levels (no h3 directly under the title). Patterns for a pull quote, callout, key facts, image with caption and a call to action: inserter → **Patterns → ChargeNet news posts**.
4. **Category:** pick the one in the post's language. **Author reference** box: leave empty for ChargeNet, or type the organisation (for example `Connectr`). **Original source** box: paste the link of the LinkedIn post or article if there is one.
5. **Rank Math** box: SEO title and description (unique per post).
6. Create the translation with the **+** in the Languages box, translate, check the category of the translation is the Dutch one.
7. **Publish.** New posts appear on the News page and, if the Post Grid shows the newest posts, on the home page.

The editor shows yellow warnings for missing alt text, heading problems and similar mistakes. Fix them before publishing.

## Create a landing page for a campaign

1. **Pages → Add New** → pattern **Landing page** (or **Landingspagina**).
2. The **Hero** uses the **Campaign** variant: text, a background image and a cover image (for example the report cover) with its alt text, and the buttons.
3. Add the **Trend report form** or **Contact form** section where the visitor should act. The forms handle consent, spam protection and the email by themselves.
4. Give the page a clean address (for example `/nl/rapport2028/`). If a short link was used in print or mailings (like `/rapport2027`), ask the site owner/developer to add the redirect; do not create a second page with the same text.
5. **Campaign links:** add `?utm_source=...&utm_medium=...&utm_campaign=...` to the links you send out. The site remembers these for 30 days **if the visitor accepted statistics cookies** and stores them with a form submission, so you can see which mailing brought each request. Use the same spelling every time (lowercase, no spaces).
6. Test the page and its form once with your own email address and delete the test submission (**Form submissions** in the admin).

## Everyday tasks

- **Images:** upload to the Media Library; fill in alt text and, for the other language, "Create translation". Photos: JPEG or PNG about 2000 px wide, under 1.5 MB; the site makes the smaller sizes and the modern formats itself. The home page background can be replaced in the Hero (`docs/content.md`).
- **Form submissions:** admin → Form submissions. They are deleted after 12 months automatically. Privacy requests (export/erase): `docs/privacy.md`.
- **Report codes** for the trend report: admin → Form submissions → Report codes (CSV import).
- **Changing legal texts** (privacy, cookies, security): do not edit without the owner; they need a legal read.
- **After publishing something important** the page cache clears by itself. If a change does not show, wait 1 minute, then hard-refresh; the administrator can clear the cache in Settings → Cache Enabler.
- **Menus and footer text:** Appearance → Menus and the footer page in `docs/content.md`.

## When something breaks

| What you see                                         | First do                                                                          | Then call                                                  |
| ---------------------------------------------------- | --------------------------------------------------------------------------------- | ---------------------------------------------------------- |
| Site down, error page, certificate warning           | Refresh in a private window; check status.strato.de; do not change anything       | Site owner (thijs@chargenet.energy) and **STRATO support** |
| A page looks broken after you edited it              | In the editor open the history icon → **Revisions** → restore the previous one    | Site owner                                                 |
| Form does not send / no email arrives                | Send one test to yourself; check Form submissions for status "failed" or "retry"  | Site owner (mail setting, `docs/integrations.md`)          |
| Cannot log in, lost authenticator                    | Use a backup code. No backup code: ask the administrator to reset your two-factor | Site owner                                                 |
| Something strange, a hacked-looking page, spam links | Do not log in again from that device; note the page and time                      | Site owner **and** security@chargenet.energy               |
| Cookie banner or tracking questions                  | See `docs/tracking.md`                                                            | Site owner                                                 |
| Wrong text on the live site                          | Fix it in the editor and update; check both languages                             | —                                                          |

**Contacts (to be filled in by the owner before launch):**

| Role                               | Name  | Email / phone             |
| ---------------------------------- | ----- | ------------------------- |
| Site owner / administrator         | Thijs | thijs@chargenet.energy    |
| Security reports                   |       | security@chargenet.energy |
| Privacy questions                  |       | privacy@chargenet.energy  |
| Developer (code, theme, scripts)   |       |                           |
| STRATO support (hosting, domains)  |       |                           |
| Legal review (privacy and cookies) |       |                           |
