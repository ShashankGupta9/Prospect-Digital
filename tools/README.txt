Prospect Digital — development tools
====================================

make_assets.py
  Regenerates the placeholder artwork used across the site: the logo set and
  favicons, the social share image (assets/images/og-default.jpg) and all hero
  images (assets/hero/*.webp).

      cd prospect-digital
      python3 tools/make_assets.py

  Requires Python 3 and Pillow:  pip install pillow

  This folder is NOT part of the website and is never requested by a browser.
  You can delete it — and the .pyc/pycache files Python may create — before
  uploading to your host. It exists so the client's team can rebuild the
  placeholders or create new ones in the same visual language.

  To replace the artwork with real photography or screenshots you do not need
  this tool at all: drop files into assets/hero/ using the names listed in
  assets/hero/README.txt and the site picks them up automatically.
