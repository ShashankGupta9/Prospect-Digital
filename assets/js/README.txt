Prospect Digital — JavaScript
=============================

main.js (vanilla JavaScript, no dependencies, loaded with defer)

  1. Sticky header state + scroll progress bar
  2. Mobile navigation drawer: focus trap, ESC to close, backdrop, body scroll
     lock, auto-reset when the viewport grows to the desktop layout
  3. Mega-menu toggles for touch and small screens
  4. Scroll-reveal animations with three safety layers so content can never be
     left invisible (immediate reveal for on-screen items, IntersectionObserver
     for the rest, and a 2.5 s failsafe that reveals everything)
  5. Back-to-top button
  6. Enquiry form UX: instant client-side checks, inline invalid states, double
     submit prevention. The server always re-validates.
  7. Marquee helper that clones the track so the loop has no visible gap

Everything degrades gracefully: with JavaScript disabled the site is still fully
navigable and readable, because navigation is built from real links, real
buttons and native <details> elements.
