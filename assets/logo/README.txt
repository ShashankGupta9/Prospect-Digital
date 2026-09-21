Prospect Digital — logos and icons
==================================

prospect-digital-mark.png ....... square mark, 512 x 512, gradient background
prospect-digital-mark-white.png . mark for dark backgrounds (footer, dark bands)
prospect-digital-logo.png ....... horizontal lock-up, 1200 x 320, light background
prospect-digital-logo-white.png . horizontal lock-up for dark backgrounds
favicon.png ..................... 512 x 512 browser icon
apple-touch-icon.png ............ 180 x 180 iOS home-screen icon
(and /favicon.ico in the project root)

The logo shown in the navbar and footer is inline SVG (includes/logo-mark.php),
which stays sharp at any size and costs no extra request. To use your own mark:

  Option 1 — replace the <svg> body in includes/logo-mark.php (keep the viewBox
             attribute and the unique gradient id).
  Option 2 — replace the PNG files here and point the <img> in includes/navbar.php
             and includes/footer.php at your file.

Replace these PNGs with your final brand files before launch; the current ones
are generated placeholders matching the site's colours (#1B4DFF to #7C5CFF).
