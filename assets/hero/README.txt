Prospect Digital — hero artwork
==============================

One image per page, named after the page slug. Drop a file in with the SAME NAME
and the site uses it automatically — no code changes needed.

Format priority: .webp  ->  .jpg  ->  .png  ->  .svg

Homepage ................ home-dashboard.webp
Services index .......... services-overview.webp
Products index .......... products-overview.webp
Projects ................ projects.webp
Guides .................. guides.webp
About ................... about-team.webp
Contact ................. contact-office.webp

Service pages ........... software-development.webp, website-development.webp,
                          digital-marketing.webp, performance-marketing.webp,
                          branding-creative.webp, it-services-cloud.webp,
                          ai-automation.webp, growth-strategy.webp

Product pages ........... routeflow.webp, workora.webp, bizora.webp,
                          medvora.webp, schova.webp

Recommended: 1200 x 760 px, WebP, under about 150 KB.

If a file is missing the page falls back to an inline SVG placeholder, so the
site never shows a broken image. Resolution order lives in
includes/functions.php -> hero_image().
