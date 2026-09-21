Prospect Digital — stylesheets
==============================

style.css
  The whole design system in one file, written mobile-first:
   1. Design tokens (colours, type scale, spacing, radii, shadows, motion)
   2. Reset and base styles
   3. Typography
   4. Layout utilities
   5. Buttons, icons, pills
   6. Header, navigation and mobile drawer
   7. Hero sections
   8. Sections, headings, cards
   9. Before/after comparison
  10. Process and timelines
  11. FAQ (native <details>)
  12. Products
  13. Forms and alerts
  14. Contact and map
  15. Legal / long-form prose
  16. Footer, CTA band, floating actions
  17. Animation and reduced-motion support
  18. Responsive refinements (600px, 900px, 1024px, 1200px)

  To re-brand the site, change the custom properties in section 1
  (:root) — for example --brand, --accent and the font stack. Nearly every
  colour and size in the site derives from those tokens.

print.css
  Print-only styles, loaded with media="print" so it is never fetched for
  normal browsing. Hides navigation and floating buttons, expands link URLs and
  forces black-on-white text for clean printed documents.

No CSS framework is used, and there are no @import rules or external font
requests.
