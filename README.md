# Prospect Digital — website

A complete, responsive company website built with **raw PHP, HTML5, CSS3 and vanilla JavaScript** —
no Laravel, no WordPress, no React, no Bootstrap, no Tailwind. Every page is real, working code:
the navigation, the enquiry form with server-side validation, the XML sitemap and the 404 page all run.

**Build. Grow. Scale.** — software, websites, cloud and marketing from Bhopal, Madhya Pradesh, India.

---

## Contents

1. [What is included](#1-what-is-included)
2. [Requirements](#2-requirements)
3. [Folder structure](#3-folder-structure)
4. [Running the site locally](#4-running-the-site-locally)
5. [Configuration](#5-configuration-includesconfigphp)
6. [Contact form configuration](#6-contact-form-configuration)
7. [Replacing images, logos and artwork](#7-replacing-images-logos-and-artwork)
8. [Editing content](#8-editing-content)
9. [Adding a new service or product page](#9-adding-a-new-service-or-product-page)
10. [Security notes](#10-security-notes)
11. [SEO checklist](#11-seo-checklist)
12. [Deployment](#12-deployment)
13. [Troubleshooting](#13-troubleshooting)
14. [Known limitations / honest notes](#14-known-limitations--honest-notes)

---

## 1. What is included

**Pages**

| Page | File | What it does |
| --- | --- | --- |
| Home | `index.php` | Hero, capability strip, 8 services, business outcomes, 5 products, positioning, process, FAQ, CTA |
| About | `about.php` | Positioning, values, capabilities, service area, contact card |
| Services index | `services.php` | All 8 service lines plus engagement models and FAQ |
| Service pages (8) | `services/*.php` | Hero → quick answer → outcome → before/after → what we deliver → process → related services → FAQ → final CTA |
| Products index | `products.php` | Five platforms, comparison table, licensing FAQ |
| Product pages (5) | `products/*.php` | Hero → quick answer → modules → before/after → who it is for → FAQ → other products → demo CTA |
| Projects | `projects.php` | Product platforms and sector-level engagement snapshots |
| Guides | `guides.php` | Short buyer guides linking into the service pages |
| Contact | `contact.php` | Working enquiry form, direct channels, map, FAQ |
| Privacy / Terms | `privacy.php`, `terms.php` | Policies written to match what the site actually does |
| Sitemap | `sitemap.php`, `sitemap.xml(.php)` | Human sitemap plus the XML sitemap for search engines |
| 404 | `404.php` | Friendly not-found page with real destinations |

**Reusable PHP components** (`includes/`)

`config.php` (settings) · `data.php` (all copy) · `functions.php` (helpers, form processing) ·
`header.php` · `navbar.php` · `footer.php` · `cta.php` · `breadcrumbs.php` · `form.php` ·
`logo-mark.php` · `service-page.php` · `product-page.php`

**Navigation**

A centred navbar: logo on the left, `HOME · ABOUT · CONTACT · SERVICE · PRODUCT` in the middle
(optically centred on the page), and the `LET'S TALK` button on the right.
SERVICE and PRODUCT open rich mega menus; below 1024px everything collapses into an accessible
hamburger drawer with a real Service/Product accordion, phone, WhatsApp and e-mail shortcuts.

---

## 2. Requirements

* **PHP 8.1 or newer** (developed and tested on PHP 8.4). No extensions beyond the defaults are required —
  the code uses `mbstring`-safe fallbacks where available.
* **Apache** with `mod_rewrite` (optional — only needed for extensionless URLs) **or** PHP's built-in server.
* **MySQL is not required.** The site is static content plus a file-based enquiry log.

Check your PHP version:

```bash
php -v
```

---

## 3. Folder structure

```
prospect-digital/
├── index.php                  # homepage
├── about.php
├── contact.php                # enquiry form + server-side handling
├── services.php               # services index
├── products.php               # products index
├── projects.php
├── guides.php
├── privacy.php
├── terms.php
├── sitemap.php                # human-readable sitemap
├── sitemap.xml.php            # generates /sitemap.xml
├── 404.php
├── robots.txt
├── site.webmanifest
├── favicon.ico
├── router.php                 # dev-only router for `php -S`
├── .htaccess                  # Apache: pretty URLs, security headers, caching
├── README.md
├── tools/                     # development helper (not part of the website)
│   └── make_assets.py         # regenerates logo, favicon, OG image, hero art
│
├── services/
│   ├── software-development.php      # ┐
│   ├── website-development.php       # │ every file is ~10 lines: they set
│   ├── digital-marketing.php         # │ $service_slug and include the shared
│   ├── performance-marketing.php     # │ template in includes/service-page.php
│   ├── branding-creative.php         # │
│   ├── it-services-cloud.php         # │
│   ├── ai-automation.php             # │
│   └── growth-strategy.php           # ┘
│
├── products/
│   ├── routeflow.php                 # ┐ same pattern: set $product_slug and
│   ├── workora.php                   # │ include includes/product-page.php
│   ├── bizora.php                    # │
│   ├── medvora.php                   # │
│   └── schova.php                    # ┘
│
├── includes/
│   ├── config.php              # ← the only file you normally edit to configure the site
│   ├── data.php                # ← all services, products and page copy
│   ├── functions.php           # helpers: escaping, URLs, icons, form processing
│   ├── header.php              # <head>, SEO meta, Open Graph, structured data
│   ├── navbar.php              # centred navigation + mobile drawer
│   ├── footer.php              # footer, floating actions, script tag
│   ├── cta.php                 # pre-footer call to action
│   ├── breadcrumbs.php
│   ├── form.php                # enquiry form markup
│   ├── logo-mark.php           # inline SVG logo
│   ├── service-page.php        # shared service page layout
│   ├── product-page.php        # shared product page layout
│   ├── .htaccess               # blocks direct web access to these files
│   └── index.html              # stops directory listing if .htaccess is ignored
│
├── assets/
│   ├── css/
│   │   ├── style.css           # the whole design system (tokens → components → responsive)
│   │   └── print.css           # print-only stylesheet
│   ├── js/
│   │   └── main.js             # navbar, drawer, reveal animations, form UX (vanilla, no libraries)
│   ├── hero/                   # page hero artwork (replace these first)
│   ├── images/                 # og-default.jpg (social share image)
│   └── logo/                   # logo marks, favicon, touch icon
│
└── data/
    ├── enquiries/              # submitted enquiries, one JSON object per line
    ├── .htaccess               # blocks web access to stored enquiries
    └── README.txt
```

Nothing is hard-coded to a filesystem path. `includes/config.php` detects the folder the site is
installed in, so the same code works at `https://yoursite.com/` and at
`http://localhost/prospect-digital/`.

---

## 4. Running the site locally

### Option A — XAMPP / WAMP / MAMP (recommended, closest to real hosting)

1. Install XAMPP and start **Apache**.
2. Copy the whole `prospect-digital` folder into the web root:
   * Windows: `C:\xampp\htdocs\prospect-digital`
   * macOS: `/Applications/XAMPP/htdocs/prospect-digital`
3. Open <http://localhost/prospect-digital/> in your browser.

That is all — no database import, no build step.

### Option B — PHP's built-in server (no Apache needed)

```bash
cd prospect-digital
php -S localhost:8000 router.php
```

Then open <http://localhost:8000/>.

`router.php` is a development helper. It makes extensionless URLs
(`/services/software-development`) and `/sitemap.xml` behave the way Apache does through `.htaccess`.
**You do not need `router.php` on real hosting**, and you can delete it before uploading if you prefer.

### Option C — Apache virtual host

```apache
<VirtualHost *:80>
    ServerName prospectdigital.test
    DocumentRoot "/path/to/prospect-digital"
    <Directory "/path/to/prospect-digital">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add `127.0.0.1 prospectdigital.test` to your hosts file, then open <http://prospectdigital.test/>.

---

## 5. Configuration (`includes/config.php`)

Everything you are likely to change lives in this single file.

| Setting | What it controls |
| --- | --- |
| `DEBUG_MODE` | `true` while developing shows PHP errors. **Set to `false` before going live.** |
| `SITE_URL` | Canonical URLs, Open Graph URLs and the XML sitemap. Change to your real domain. |
| `ASSET_VERSION` | Fallback cache-busting value (normally the file's modification time is used). |
| `COMPANY_*` | Name, tagline, address lines, phone, e-mail, hours, map coordinates. Used by every page, the footer, structured data and the enquiry form. |
| `$CONTACT_CONFIG` | Contact-form recipients, mail driver, spam settings (see below). |
| `COMPANY_WHATSAPP_URL` | The WhatsApp link (`https://wa.me/917000127225?text=…`). |

Company details are defined once and reused everywhere — change the phone number in `config.php`
and it updates the navbar, every CTA, the footer, the contact page and the structured data.

---

## 6. Contact form configuration

The form is fully functional: it validates server-side, stores every enquiry, and shows the visitor a
reference number.

### Where enquiries are stored

`data/enquiries/enquiries-YYYY-MM.jsonl` — one JSON object per line:

```json
{"reference":"PD-260916-A1B2","received_at":"2026-09-16T14:32:08+05:30","name":"…","email":"…",
 "phone":"…","company":"…","service":"…","budget":"…","message":"…","source_page":"…",
 "ip_hash":"…","user_agent":"…","mail_sent":false}
```

The `data/` folder ships with an `.htaccess` that blocks web access. Keep it in place on Apache
hosting, and back the folder up with the rest of the site.

### Turning on e-mail

Open `includes/config.php` and find `$CONTACT_CONFIG['driver']`:

| Driver | Behaviour |
| --- | --- |
| `'log'` *(default)* | Validates and stores the enquiry. **No e-mail is sent**, and the visitor is told so honestly, with phone/WhatsApp alternatives offered. |
| `'mail'` | Also attempts PHP's `mail()`. Works only where the server has a configured MTA — many shared hosts block it, and messages often land in spam. |
| `'smtp'` | Reserved. Add a library such as PHPMailer (or Symfony Mailer) and wire it into `send_enquiry_mail()` in `includes/functions.php`. Nothing third-party is bundled, on purpose. |

**Recommended for production:** use `'mail'` with a real mailbox on your domain and an SMTP plugin at
the server level, or add PHPMailer for authenticated SMTP:

```bash
composer require phpmailer/phpmailer
```

Then in `send_enquiry_mail()` replace the `mail()` call with PHPMailer using credentials stored
**outside the public folder** (for example in an `.env` file loaded by `config.php`, or in an
environment variable). Never place SMTP passwords in a page, a template or anything that is served
as text.

> **Honesty note:** this project deliberately does **not** tell visitors "we have e-mailed you" while
> the mail driver is off. Until e-mail is configured, keep a habit of checking `data/enquiries/`.

### Spam protection

Four layers, all server-side:

1. **CSRF token** — one per session, compared with `hash_equals()`.
2. **Honeypot field** — a hidden `website_url` input that humans never see and bots fill in.
3. **Time trap** — submissions faster than 3 seconds are rejected (`min_seconds_on_form`).
4. **Session rate limit** — at most 5 submissions per 10 minutes (`max_per_session`, `session_window`).

Adjust those two values in `$CONTACT_CONFIG`. If you add a CAPTCHA later, add it to the
"Spam protection" block in `process_enquiry()` (`includes/functions.php`).

---

## 7. Replacing images, logos and artwork

Placeholders are brand-consistent, lightweight and generated locally — replace them with real
photography, screenshots or artwork whenever you are ready.

### Hero artwork — `assets/hero/`

One file per page, named after the page slug. **Drop in a file with the same name and it is used
automatically — no code change.** Format priority: `.webp` → `.jpg` → `.png` → `.svg`.

| File | Used on |
| --- | --- |
| `home-dashboard.webp` | homepage hero |
| `services-overview.webp` | services index |
| `products-overview.webp` | products index |
| `projects.webp`, `guides.webp`, `about-team.webp`, `contact-office.webp` | those pages |
| `software-development.webp`, `website-development.webp`, `digital-marketing.webp`, `performance-marketing.webp`, `branding-creative.webp`, `it-services-cloud.webp`, `ai-automation.webp`, `growth-strategy.webp` | the eight service heroes |
| `routeflow.webp`, `workora.webp`, `bizora.webp`, `medvora.webp`, `schova.webp` | the five product heroes |

Recommended: **1200 × 760 px**, WebP, under ~150 KB. `includes/functions.php → hero_image()` is the
function that resolves these files; it always falls back to the inline SVG mock-up, so the site never
shows a broken image.

### Logos and icons — `assets/logo/`

| File | Where it is used |
| --- | --- |
| `prospect-digital-mark.png` | square mark, 512 × 512 |
| `prospect-digital-mark-white.png` | mark for dark backgrounds (referenced in the brief for the footer) |
| `prospect-digital-logo.png` | horizontal lock-up for light backgrounds |
| `prospect-digital-logo-white.png` | horizontal lock-up for dark backgrounds |
| `favicon.png`, `apple-touch-icon.png` | browser and home-screen icons |
| `/favicon.ico` | classic favicon at the site root |

The logo drawn in the navbar and footer is **inline SVG** (`includes/logo-mark.php`), which keeps it
sharp on every screen and costs no extra request. To use your own mark, replace the `<svg>` body in
that file (keep the `viewBox`), or point the `<img>` in `navbar.php` / `footer.php` at your PNG.

### Social share image — `assets/images/og-default.jpg`

1200 × 630 px. Used for Open Graph and Twitter cards. Replace with a real branded image when ready.

### Rebuilding all placeholder assets

The generator used to create the current placeholders ships with the project:

```bash
cd prospect-digital
python3 tools/make_assets.py      # requires Python 3 and Pillow
```

The `tools/` folder is a development helper only — it is never requested by a browser and can be
deleted before uploading to your host.

Or simply delete the files you do not want: every image reference degrades to an inline SVG
placeholder rather than a broken image icon.

### Image best practice already in place

* Every `<img>` has descriptive `alt` text written for the page (decorative inline SVG is
  `aria-hidden`).
* Non-hero images use `loading="lazy"` and `decoding="async"`.
* Width and height attributes are set to prevent layout shift.
* WebP is used for hero artwork, with a fallback chain to JPEG/PNG.

---

## 8. Editing content

All copy lives in **`includes/data.php`**:

| Array | Contents |
| --- | --- |
| `$PD_NAV` | navigation items |
| `$PD_SERVICES` | the eight services: hero, quick answer, outcome, before/after, deliverables, process, FAQs, SEO titles |
| `$PD_PRODUCTS` | the five platforms: tagline, quick answer, features, before/after, audience, FAQs |
| `$PD_HOME` | homepage hero, stats, outcomes, positioning, process, FAQ |
| `$PD_ABOUT` | about page story, values, offices |
| `$PD_WORK` | projects page platforms and engagement snapshots |
| `$PD_GUIDES` | guides page entries |
| `$PD_LEGAL` | "last updated" date shown on the policies |

Edit the text, save, refresh — the change appears on every page that uses it. Page layout files
rarely need to change.

### Managing portfolio work

Named portfolio and case-study cards are managed from **Admin → Work & Case Studies**
(`admin/work.php`). The records are stored in the `pd_work_items` table and include the
client name, category, live URL, image, scope, outcome, related services, publish status,
featured status and display order. The table is created automatically when the configured
MySQL database is available; `data/work_schema.sql` is also provided for manual imports.

The managed portfolio table starts empty. The public Projects page only shows work that
has been added and published from the admin panel.

### Managing the team

The About page team section is managed from **Admin → Team** (`admin/team.php`).
Administrators can add, edit, publish, reorder and delete members, add an optional LinkedIn
profile, and upload JPG, PNG or WebP photos up to 5 MB. Photos are stored under
`assets/images/team/`, while member details are stored in the `pd_team_members` table.
The table is created automatically when MySQL is available; `data/content_schema.sql` is
provided as the combined manual import for both managed content tables. It does not remove
existing enquiries, admin users, products, orders or uploaded media.

> The before/after and engagement content is written at sector level. Add named client case studies
> only with written permission, and only with figures you can support.

---

## 9. Adding a new service or product page

1. Add an entry to `$PD_SERVICES` (or `$PD_PRODUCTS`) in `includes/data.php` — copy an existing
   block and edit it.
2. Copy any file in `services/` (or `products/`) and change the slug on the `$service_slug` /
   `$product_slug` line.
3. Done. The navigation, mega menu, footer, services index, products index, sitemap, homepage and
   cross-links pick it up automatically because they all iterate over the same arrays.

---

## 10. Security notes

| Area | Implementation |
| --- | --- |
| Output escaping | `e()` (`htmlspecialchars`, `ENT_QUOTES \| ENT_SUBSTITUTE`, UTF-8) is used for every dynamic value printed into HTML. |
| Input handling | `post_string()` trims, caps length and strips control characters; `clean_text()` strips tags; values used in mail headers pass through `header_safe()` to block header injection. |
| CSRF | A per-session 32-byte random token, compared in constant time with `hash_equals()`. |
| Spam | Honeypot, time trap and per-session rate limit (see section 6). |
| Sessions | `HttpOnly`, `SameSite=Lax`, `Secure` when HTTPS is detected; session cookie path is scoped to the install folder. |
| Directory access | `includes/.htaccess` and `data/.htaccess` deny direct requests; `index.html` files stop directory listing if `.htaccess` is ignored. |
| Headers | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` and HSTS (HTTPS only) are set in `.htaccess`. |
| Errors | With `DEBUG_MODE` off, errors are logged, never printed — no path or configuration leakage. |
| Secrets | Credentials belong in `config.php` or environment variables, never in a template. Nothing sensitive ships in the repository. |

### Recommended before launch

* Set `DEBUG_MODE` to `false`.
* Install an SSL certificate and uncomment the HTTPS redirect in `.htaccess`.
* Move the `data/` folder above the web root if your host supports it, and adjust `DATA_DIR`
  in `config.php`. Keeping it inside is safe with the supplied `.htaccess`, but "above web root" is
  stronger.
* Have a legal adviser review `privacy.php` and `terms.php` — they describe this site accurately but
  are not legal advice.

---

## 11. SEO checklist

Already implemented:

* Unique `<title>` and meta description per page, written for search intent (including local terms
  such as "Bhopal").
* Canonical URL on every page, built from `SITE_URL`.
* Open Graph and Twitter card metadata, with a 1200 × 630 social image.
* Structured data (JSON-LD): `ProfessionalService` (organisation, address, geo, opening hours),
  `WebSite`, `BreadcrumbList`, `Service` + `OfferCatalog`, `SoftwareApplication`, `FAQPage`,
  `ItemList`, `AboutPage`, `ContactPage`, `PrivacyPolicy`.
* One `<h1>` per page, ordered headings, descriptive alt text, internal linking between services,
  products and guides.
* `robots.txt` and a generated **`/sitemap.xml`**.
* Mobile-first responsive layout — the same content and links at every width.
* Fast delivery: no frameworks, no icon fonts, no external font requests, one CSS file, one JS file,
  long cache lifetimes with automatic cache-busting.

To do after deployment:

1. Set `SITE_URL` to the live domain in `config.php`, then check `robots.txt` points at the right
   sitemap URL.
2. Regenerate the static sitemap if your host does not run PHP for `/sitemap.xml`:
   `php sitemap.xml.php > sitemap.xml`
3. Submit the sitemap in Google Search Console and create a Google Business Profile for the Bhopal
   office.

---

## 12. Deployment

1. **Upload** the contents of `prospect-digital/` to your web root (`public_html`, `www`, `htdocs`)
   using FTP/SFTP, cPanel File Manager, or a deployment tool.
2. **Set permissions**: folders `755`, files `644`. The web server user must be able to **write to
   `data/enquiries/`** so enquiries can be saved:
   ```bash
   chmod 755 data data/enquiries
   ```
3. **Edit `includes/config.php`**:
   * `SITE_URL` → your domain
   * `DEBUG_MODE` → `false`
   * company details, if anything has changed
   * `$CONTACT_CONFIG['to_email']` → the mailbox that should receive enquiries
4. **Install an SSL certificate** (Let's Encrypt is free), then uncomment the HTTPS redirect block in
   `.htaccess`.
5. **Check `.htaccess`** is uploaded — some FTP clients hide dot files. If your host does not allow
   `.htaccess`, the site still works, but you lose pretty URLs and some security headers.
6. **Test after going live**:
   * every navigation item and footer link
   * the enquiry form (submit a real test enquiry and confirm the record appears in
     `data/enquiries/`)
   * call, WhatsApp and e-mail buttons on a real phone
   * `https://yourdomain.com/sitemap.xml` and `https://yourdomain.com/robots.txt`
   * a 404 page (`https://yourdomain.com/something-random`)

### Performance already handled

Compression (`mod_deflate`), one-year cache lifetimes for CSS/JS/images with file-based
cache-busting, no render-blocking fonts, lazy-loaded below-the-fold media, and print styles that are
only fetched when printing. The entire project is a few hundred kilobytes.

---

## 13. Troubleshooting

| Symptom | Cause and fix |
| --- | --- |
| Blank white page | Set `DEBUG_MODE` to `true` in `config.php` to see the error, then set it back to `false`. Usually a file permission or PHP version issue. |
| Enquiries are not saved | `data/enquiries/` is not writable. `chmod 755 data data/enquiries`, or make them writable by the web-server user. |
| "Your session expired" on submit | Cookies are blocked, or the session cookie path is wrong. Confirm your browser accepts cookies for the domain; if the site runs in a sub-folder, session path handling is automatic. |
| "That was a little too quick" | The time trap fired. Wait a few seconds, or lower `min_seconds_on_form` in `config.php`. |
| Form says it could not be submitted | The hidden honeypot field was filled — usually an autofill plugin. Fill the form manually. |
| Styles look broken | `assets/css/style.css` did not upload, or your host rewrites paths. Confirm the file loads directly in the browser. |
| 404 on `/services/software-development` | `mod_rewrite` is off or `.htaccess` is ignored. Use the `.php` URLs (they always work), or enable `AllowOverride All`. |
| Hero image not showing | Not a bug: the inline SVG placeholder is used when no file exists at `assets/hero/<slug>.webp`. Add your image with that exact name. |
| Sitemap shows the wrong domain | Update `SITE_URL` in `config.php`. |

---

## 14. Known limitations / honest notes

* **E-mail is not sent by default.** The form stores enquiries and tells the visitor exactly that.
  Configure a driver (section 6) before promising e-mail confirmations.
* **No analytics, no tracking cookies.** Only a session cookie is used, for CSRF protection. If you
  add analytics, update `privacy.php` (section 2 and 4) and add a consent banner where the law
  requires it.
* **The Google Map is an embed.** It loads only on the contact page, is lazy-loaded, and a text
  fallback with a Google Maps link is provided for slow connections and no-JavaScript visitors —
  useful because Google Maps is not always reachable from every network in India.
* **Hero artwork is placeholder art.** It is deliberately abstract so nothing false is implied.
  Replace it with real screenshots and photography of your team and work.
* **Case studies are sector-level.** Named clients, logos and performance numbers must be added only
  with written permission — and only with numbers you can defend.
* **Legal pages describe this website accurately** but are not legal advice. Have them reviewed.

---

## Verification performed

Before hand-over this build was tested end to end:

| Check | Result |
| --- | --- |
| PHP lint on every `.php` file | clean (PHP 8.4) |
| Every page loaded on all 24 public URLs | HTTP 200 (404 page correctly returns 404) |
| Internal link crawl across the whole site | 34 pages, 0 broken links, 0 missing anchors |
| Every referenced local asset (CSS, JS, images, sitemap, manifest) | all resolve |
| Contact form — valid submission | stored, reference ID shown, 303 redirect (no double submit) |
| Contact form — invalid submission | field-level messages, `aria-invalid`, input preserved |
| Contact form — missing CSRF token | rejected |
| Contact form — honeypot filled | rejected |
| Contact form — instant submit | rejected by the time trap |
| Contact form — repeated submits | session rate limit triggers |
| Responsive sweep — 24 pages × desktop / tablet / phone | no JS errors, no horizontal scrolling |
| JavaScript disabled | every page readable; all content visible; forms still work |
| Scroll-reveal failure modes (broken IntersectionObserver / rAF) | content still appears, nav and form unaffected |
| Keyboard navigation (mobile drawer) | focus enters the drawer, stays trapped (16/16 tabs), ESC restores focus |
| Keyboard navigation (desktop mega menus) | open on focus, all items reachable |
| Closed mobile drawer | removed from the tab order and accessibility tree |
| Colour contrast (14 key text pairs) | all pass WCAG AA |
| Heading structure, alt text, labels, duplicate IDs | clean on every page |
| Live external actions | `tel:`, `wa.me`, `mailto:` and Google Maps links verified |
| Homepage weight | ~90 KB over 5 requests, first contentful paint ~140 ms (local) |

---

## Credits

Design and build: Prospect Digital, M.P. Nagar, Bhopal — <hello@prospectdigital.in> · +91 7000-12-7225

Built with raw PHP, HTML5, CSS3 and vanilla JavaScript. No frameworks, no build step, no dependencies.
