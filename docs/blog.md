# News (blog)

18 posts from the old site, in English and Dutch (36 posts), imported by `content/blog.php` (run by the seeder, see `docs/content.md`). Comments are off. SEO and structured data come from Rank Math (free, no Rank Math account).

## URLs

| What                | URL                                                         |
| ------------------- | ----------------------------------------------------------- |
| News (index)        | `/en/blog/` and `/nl/nieuws/`                               |
| Post                | `/en/blog/<slug>/` and `/nl/blog/<slug>-nl/`                |
| Category            | `/en/blog/category/<slug>/` and `/nl/blog/category/<slug>/` |
| Search (whole site) | `/en/?s=…` and `/nl/?s=…`                                   |
| Feed                | `/en/feed/` and `/nl/feed/`                                 |
| Sitemap             | `/sitemap_index.xml` (Rank Math)                            |

The language prefix stays (decision in `CLAUDE.md`). The old address `/blog/<slug>` answers with **one 301** to `/en/blog/<slug>/` (`inc/redirects.php`). `npm run check:blog-urls` checks all 18 old addresses (one 301, then 200), and that each post has a canonical, Open Graph tags and BlogPosting data, and that the Dutch post answers 200.

## Categories

Six, each in both languages (the old seven were mixed: "Accelerate" held partnerships, funding, a new location and a media article). The mapping of posts to categories is `content/blog/_categories.php`.

| English              | Dutch                      | Posts |
| -------------------- | -------------------------- | ----- |
| Partnerships         | Partnerschappen            | 4     |
| Funding & programmes | Financiering & programma’s | 4     |
| Events               | Evenementen                | 5     |
| Product & network    | Product & netwerk          | 2     |
| Market & media       | Markt & media              | 2     |
| Company              | Bedrijfsnieuws             | 1     |

Editors add or rename categories in Posts → Categories (one per language, linked as translations in Polylang). The News filter lists the categories that have posts.

## Authors

Each post has an **Author reference** (box in the post sidebar): a name such as `Connectr`, `SIRA` or `ING Research`. Empty means **ChargeNet**. The authors are organisations, not WordPress users, so there are no author pages and no author box with a photo; the name is shown under the title ("By ChargeNet") and is the `author` (an Organization) in the structured data. The importer takes the old label and drops the "External: " prefix.

## Templates

| Template                            | What                                                                                                                                         |
| ----------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `home.php` + the News page content  | Title band, **News filter** (category links, search box), Post Grid (featured first post, 9 per page, paging)                                |
| `template-parts/content-single.php` | Title band (category and date), byline with reading time, featured image, text, source link, share, newsletter, related news, call to action |
| `category.php`                      | Title band with the category and its description, filter, posts with paging                                                                  |
| `search.php`                        | Results for the whole site (news and pages in the page's language) as cards                                                                  |
| `template-parts/post-card.php`      | One card (used by the Post Grid and the search)                                                                                              |

Single post: **table of contents** (from three h2 headings, headings get ids), **reading time** (200 words a minute), **share** (LinkedIn, email, and "Copy link": a button that a few lines of inline script reveal, so without JavaScript only the two links show), **newsletter** (Newsletter sign-up section), **related news** (three posts of the same category, or the newest when there are fewer than three), **call to action** (CTA band with a button to the Contact page). The newsletter, related news and CTA are sections of the section library, so they follow the design tokens.

## Writing posts: patterns and styles

Posts use the normal editor. **Patterns** (Add block → Patterns → "ChargeNet news posts", each in English and Dutch): pull quote, callout, key facts, image with caption, call to action. WordPress caches the list of pattern files per theme version: after uploading a theme with new patterns, raise `Version` in `style.css` (or run `wp eval 'wp_get_theme()->delete_pattern_cache();'`). They use core blocks with **block styles** (`is-style-pull-quote`, `is-style-callout`, `is-style-key-facts`, `is-style-post-cta`), so editors can also apply the styles to their own quote and group blocks. The look is in `assets/src/scss/components/_post.scss`.

## Search engines and sharing

Rank Math prints the title, description, canonical, Open Graph and Twitter tags and the JSON-LD (BlogPosting, with the author replaced by the post's author reference, `inc/blog.php`). Settings: `bin/setup-rankmath.php` (run by `bin/setup-wp.sh`): organisation, BlogPosting for posts, large Twitter card, no author or date archives, only the sitemap and structured data modules on. The importer put the old SEO title and description in Rank Math's fields (a description or excerpt that two old posts shared, copied from another post, was replaced by the post's first paragraph). Open Graph images are the featured images; LinkedIn shows them best at about 1200 × 630, so use landscape featured images for new posts.

The RSS feed has the full text with the featured image first; the comments feeds are off.

## Images and speed

Featured images and images in the text carry `width`, `height` and `srcset` (no layout shift); only the first image is eager. The only JavaScript on a post is the newsletter form's module and a 300-byte inline script for "Copy link". While an editor writes a post or page, the block editor shows warnings at the top (images without alt text, a Heading 1, a skipped heading level, link texts like "read more"); they update as the text changes (`inc/editor-checks.php`; PHP admin notices are hidden in the block editor). `bin/test-blog.php` checks the same rules in PHP.

## Checks

`npm run test:blog` (author default, categories, comments off, descriptions and excerpts unique, table of contents, share links, structured data author, alt text check), `npm run check:blog-urls`, and the form checks `npm run test:forms` (newsletter included).

## Newsletter

Subscriptions are stored in WordPress (Form submissions, form "Newsletter") until a newsletter platform is chosen. A subscriber gets one confirmation email (editable under Form submissions → Settings and emails; the reply address is info@). Subscribing twice stores and sends nothing again. Subscriptions are **not** deleted by the 12-month retention: they stay until the person unsubscribes (reply to the email, or delete the entry), or is erased with the WordPress privacy tools. Export the list with **Export CSV** (filter: Newsletter).
