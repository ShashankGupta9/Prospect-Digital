#!/usr/bin/env python3
"""
Prospect Digital — asset generator (development tool, not part of the website)

Creates the logo set, favicon, social share image and hero artwork used by the
site. Run it only when you want to rebuild those placeholders:

    cd prospect-digital
    python3 tools/make_assets.py

Requires Python 3 and Pillow (`pip install pillow`).

Outputs into <project>/assets/logo, /assets/images, /assets/hero and favicon.ico
These are brand-consistent PLACEHOLDERS: replace the hero files and the logo PNGs
with your own art (keep the same file names) and the site picks them up with no
code changes.
"""

import math
import os
import random
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))   # project root
ASSETS = os.path.join(ROOT, "assets")
S = 2  # supersampling factor for smooth edges

# ---------------------------------------------------------------- brand palette
BRAND = (27, 77, 255)
BRAND_DARK = (14, 42, 153)
ACCENT = (124, 92, 255)
TEAL = (15, 185, 140)
INK = (11, 19, 48)
INK_SOFT = (74, 83, 117)
MUTED = (107, 115, 145)
LINE = (223, 227, 238)
WHITE = (255, 255, 255)
PAPER = (255, 255, 255)
PAPER_2 = (242, 246, 255)
NAVY = (7, 12, 34)
NAVY_2 = (12, 20, 54)

FONT_BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
FONT_REG = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"


def font(path, size):
    return ImageFont.truetype(path, size * S)


# ------------------------------------------------------------------- primitives
def lerp(c1, c2, t):
    return tuple(round(c1[i] + (c2[i] - c1[i]) * t) for i in range(3))


def gradient(size, c1, c2, diagonal=True):
    w, h = size
    img = Image.new("RGB", size)
    d = ImageDraw.Draw(img)
    steps = w + h if diagonal else h
    for i in range(steps):
        t = i / max(1, steps - 1)
        c = lerp(c1, c2, t)
        if diagonal:
            d.line([(i, 0), (0, i)], fill=c)
        else:
            d.line([(0, i), (w, i)], fill=c)
    return img


def soft_blob(img, center, radius, color, alpha=90, blur=110):
    """Radial glow blob composited onto an RGB image."""
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    x, y = center
    d.ellipse([x - radius, y - radius, x + radius, y + radius], fill=color + (alpha,))
    layer = layer.filter(ImageFilter.GaussianBlur(blur))
    img.alpha_composite(layer) if img.mode == "RGBA" else img.paste(
        Image.alpha_composite(img.convert("RGBA"), layer).convert("RGB"), (0, 0)
    )
    return img


def rr(d, box, r, fill=None, outline=None, width=1):
    d.rounded_rectangle([box[0] * S, box[1] * S, box[2] * S, box[3] * S],
                        radius=r * S, fill=fill, outline=outline,
                        width=int(width * S))


def line(d, pts, fill, width=1, joint="curve"):
    d.line([(x * S, y * S) for x, y in pts], fill=fill, width=int(width * S), joint=joint)


def ellipse(d, box, fill=None, outline=None, width=1):
    d.ellipse([box[0] * S, box[1] * S, box[2] * S, box[3] * S], fill=fill,
              outline=outline, width=int(width * S))


def polygon(d, pts, fill):
    d.polygon([(x * S, y * S) for x, y in pts], fill=fill)


def text_ls(d, xy, txt, fnt, fill, spacing=0, anchor_left=True):
    """Draw text with letter spacing."""
    x, y = xy
    for ch in txt:
        d.text((x * S, y * S), ch, font=fnt, fill=fill)
        w = d.textlength(ch, font=fnt) / S
        x += w + spacing
    return x


def fit(img, size, bg=None):
    if img.mode == "RGBA" and bg is not None:
        flat = Image.new("RGB", img.size, bg)
        flat.paste(img, mask=img.split()[3])
        img = flat
    return img.resize(size, Image.LANCZOS)


# --------------------------------------------------------------- page artwork
def window_panel(d, w=1200, h=760, pad=64):
    """Light surface panel for hero artwork."""
    x0, y0 = pad, pad + 18
    x1, y1 = w - pad, h - pad
    rr(d, (x0 + 6, y0 + 14, x1 + 6, y1 + 14), 22, fill=(226, 232, 248))       # soft shadow
    rr(d, (x0, y0, x1, y1), 22, fill=WHITE, outline=LINE, width=1)
    line(d, [(x0, y0 + 46), (x1, y0 + 46)], LINE, 1)
    for i, c in enumerate([(255, 95, 87), (254, 188, 46), (40, 200, 64)]):
        ellipse(d, (x0 + 22 + i * 20, y0 + 17, x0 + 34 + i * 20, y0 + 29), fill=c)
    rr(d, (x0 + 110, y0 + 13, x0 + 360, y0 + 33), 10, fill=(238, 242, 251))
    return (x0, y0, x1, y1)


def ph_title(d, x, y, w=190, h=13, color=(150, 160, 190)):
    rr(d, (x, y, x + w, y + h), h // 2, fill=color)


def card(d, x, y, w, h, fill=(247, 249, 254), outline=LINE):
    rr(d, (x, y, x + w, y + h), 14, fill=fill, outline=outline, width=1)


def art_dashboard(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    ph_title(d, px, py, 210, 14, (120, 132, 168))
    ph_title(d, px, py + 24, 140, 9, (206, 213, 230))
    for i in range(4):
        cw = 168
        cx = px + i * (cw + 18)
        card(d, cx, py + 52, cw, 74)
        rr(d, (cx + 16, py + 66, cx + 16 + 46, py + 74), 4, fill=(206, 213, 230))
        rr(d, (cx + 16, py + 84, cx + 16 + 92 - i * 10, py + 96), 6,
           fill=[BRAND, ACCENT, TEAL, (245, 165, 36)][i])
    # chart area
    card(d, px, py + 146, 470, 356)
    for gy in (256, 340, 424):
        line(d, [(px + 26, py + gy), (px + 446, py + gy)], (234, 238, 247), 1)
    pts = [(px + 30, py + 452), (px + 120, py + 392), (px + 210, py + 412),
           (px + 300, py + 322), (px + 380, py + 344), (px + 446, py + 286)]
    line(d, pts, (214, 222, 246), 9)
    line(d, pts, BRAND, 5)
    for p in pts[1:-1]:
        ellipse(d, (p[0] - 7, p[1] - 7, p[0] + 7, p[1] + 7), fill=WHITE, outline=BRAND, width=3)
    # right column
    rx = px + 496
    for i in range(3):
        card(d, rx, py + 146 + i * 104, 210, 92)
        rr(d, (rx + 18, py + 166 + i * 104, rx + 60, py + 174 + i * 104), 4, fill=(206, 213, 230))
        rr(d, (rx + 18, py + 188 + i * 104, rx + 150, py + 200 + i * 104), 5,
           fill=[ACCENT, TEAL, BRAND][i])
        rr(d, (rx + 18, py + 210 + i * 104, rx + 116, py + 218 + i * 104), 4, fill=(226, 232, 246))
    rr(d, (rx, py + 462, rx + 210, py + 520), 14, fill=BRAND)
    rr(d, (rx + 22, py + 484, rx + 128, py + 500), 8, fill=(255, 255, 255, 200))


def art_website(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    ph_title(d, px, py, 330, 22, (150, 160, 190))
    ph_title(d, px, py + 38, 420, 12, (206, 213, 230))
    ph_title(d, px, py + 60, 360, 12, (206, 213, 230))
    rr(d, (px, py + 100, px + 190, py + 152), 26, fill=BRAND)
    rr(d, (px + 208, py + 100, px + 350, py + 152), 26, fill=(240, 243, 250))
    card(d, px + 470, py + 10, 240, 190, fill=PAPER_2, outline=(224, 231, 250))
    ellipse(d, (px + 540, py + 60, px + 640, py + 160), fill=ACCENT)
    rr(d, (px + 500, py + 132, px + 680, py + 148), 8, fill=(198, 208, 240))
    for i in range(3):
        cx = px + i * 240
        card(d, cx, py + 200, 220, 200)
        rr(d, (cx + 20, py + 222, cx + 68, py + 264), 10, fill=[BRAND, ACCENT, TEAL][i])
        rr(d, (cx + 20, py + 284, cx + 150, py + 296), 6, fill=(206, 213, 230))
        rr(d, (cx + 20, py + 308, cx + 180, py + 318), 5, fill=(226, 232, 246))
        rr(d, (cx + 20, py + 328, cx + 130, py + 338), 5, fill=(226, 232, 246))
    line(d, [(px, py + 428), (px + 706, py + 428)], LINE, 1)


def art_table(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    ph_title(d, px, py, 240, 16, (120, 132, 168))
    rr(d, (px + 500, py - 6, px + 706, py + 34), 20, fill=BRAND)
    rr(d, (px + 528, py + 6, px + 660, py + 22), 8, fill=(255, 255, 255)) if False else None
    rr(d, (px, py + 54, px + 706, py + 100), 12, fill=(238, 242, 251))
    for c, xw in zip(range(4), [0, 180, 360, 540]):
        rr(d, (px + 24 + xw, py + 70, px + 24 + xw + [110, 96, 96, 76][c], py + 84), 5,
           fill=(150, 162, 196))
    for r in range(7):
        y = py + 112 + r * 52
        rr(d, (px, y, px + 706, y + 42), 10, fill=(250, 251, 254) if r % 2 == 0 else WHITE,
           outline=(238, 241, 248), width=1)
        rr(d, (px + 24, y + 16, px + 24 + 118, y + 28), 6, fill=(196, 205, 226))
        rr(d, (px + 204, y + 16, px + 204 + 92, y + 28), 6, fill=(216, 223, 240))
        rr(d, (px + 384, y + 16, px + 384 + 72, y + 28), 6, fill=(216, 223, 240))
        badge = [TEAL, BRAND, ACCENT, TEAL, (245, 165, 36), BRAND, TEAL][r]
        rr(d, (px + 564, y + 12, px + 564 + 88, y + 32), 10, fill=badge)
    rr(d, (px, py + 490, px + 706, py + 500), 5, fill=(238, 242, 251))


def art_map(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    card(d, px, py, 1004, 470, fill=(248, 250, 255), outline=(230, 235, 246))
    for i in range(1, 14):
        line(d, [(px + i * 74, py + 20), (px + i * 74, py + 450)], (238, 242, 250), 1)
    for i in range(1, 9):
        line(d, [(px + 20, py + i * 56), (px + 984, py + i * 56)], (238, 242, 250), 1)
    route = [(px + 70, py + 404), (px + 250, py + 330), (px + 330, py + 190),
             (px + 520, py + 236), (px + 670, py + 130), (px + 930, py + 84)]
    line(d, route, BRAND, 9)
    line(d, route, WHITE, 2)
    for i, p in enumerate(route):
        if i in (0, len(route) - 1):
            ellipse(d, (p[0] - 16, p[1] - 16, p[0] + 16, p[1] + 16), fill=BRAND)
            ellipse(d, (p[0] - 6, p[1] - 6, p[0] + 6, p[1] + 6), fill=WHITE)
        else:
            ellipse(d, (p[0] - 12, p[1] - 12, p[0] + 12, p[1] + 12), fill=WHITE, outline=BRAND, width=4)
    card(d, px + 640, py + 268, 340, 170, fill=WHITE, outline=(226, 232, 244))
    rr(d, (px + 668, py + 296, px + 800, py + 312), 7, fill=(158, 168, 198))
    rr(d, (px + 668, py + 330, px + 950, py + 342), 6, fill=(214, 221, 238))
    rr(d, (px + 668, py + 352, px + 900, py + 364), 6, fill=(214, 221, 238))
    rr(d, (px + 668, py + 352, px + 780, py + 364), 6, fill=(214, 221, 238))
    rr(d, (px + 668, py + 386, px + 790, py + 414), 14, fill=TEAL)
    ellipse(d, (px + 60, py + 70, px + 96, py + 106), fill=WHITE, outline=BRAND, width=4)
    rr(d, (px + 116, py + 78, px + 240, py + 92), 7, fill=(206, 213, 230))


def art_kanban(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    colw = 222
    heads = ["Backlog", "In progress", "Done"]
    for c in range(3):
        cx = px + c * (colw + 20)
        card(d, cx, py, colw, 470, fill=(248, 250, 255), outline=(232, 237, 248))
        rr(d, (cx + 18, py + 20, cx + 18 + 92, py + 34), 7, fill=(158, 168, 198))
        rr(d, (cx + colw - 44, py + 18, cx + colw - 18, py + 36), 9, fill=(232, 237, 248))
        rows = 3 if c != 1 else 2
        for r in range(rows):
            cy = py + 56 + r * 118
            card(d, cx + 14, cy, colw - 28, 102, fill=WHITE,
                 outline=(228, 234, 246) if c != 1 else (198, 210, 245))
            if c == 1 and r == 0:
                rr(d, (cx + 14, cy, cx + colw - 14, cy + 6), 3, fill=ACCENT)
            rr(d, (cx + 30, cy + 22, cx + 30 + 118, cy + 34), 6, fill=(202, 211, 232))
            rr(d, (cx + 30, cy + 48, cx + 30 + 150, cy + 58), 5, fill=(228, 234, 246))
            rr(d, (cx + 30, cy + 68, cx + 30 + 92, cy + 78), 5, fill=(228, 234, 246))
            ellipse(d, (cx + colw - 62, cy + 22, cx + colw - 34, cy + 50),
                    fill=[BRAND, ACCENT, TEAL][(c + r) % 3])


def art_calendar(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    card(d, px, py, 452, 470, fill=(249, 251, 255), outline=(232, 237, 248))
    rr(d, (px + 22, py + 22, px + 22 + 138, py + 36), 7, fill=(158, 168, 198))
    cw, ch = 92, 82
    for r in range(4):
        for c in range(4):
            cx = px + 22 + c * (cw + 14)
            cy = py + 58 + r * (ch + 14)
            fill = WHITE
            outline = (232, 237, 248)
            if (r, c) == (1, 2):
                fill, outline = (236, 240, 255), (180, 196, 246)
            if (r, c) == (2, 1):
                fill, outline = (232, 250, 244), (168, 226, 208)
            rr(d, (cx, cy, cx + cw, cy + ch), 12, fill=fill, outline=outline, width=1)
            rr(d, (cx + 14, cy + 16, cx + 14 + 34, cy + 26), 5, fill=(206, 213, 230))
            if (r, c) in ((1, 2), (2, 1), (0, 0)):
                rr(d, (cx + 14, cy + 44, cx + 14 + 58, cy + 54), 5,
                   fill=ACCENT if (r, c) == (1, 2) else (TEAL if (r, c) == (2, 1) else (216, 223, 240)))
    rx = px + 486
    for i in range(3):
        card(d, rx, py + i * 112, 220, 94)
        rr(d, (rx + 18, py + 18 + i * 112, rx + 18 + 92, py + 30 + i * 112), 6, fill=(196, 205, 226))
        rr(d, (rx + 18, py + 42 + i * 112, rx + 18 + 150, py + 52 + i * 112), 5, fill=(222, 229, 244))
        rr(d, (rx + 18, py + 62 + i * 112, rx + 18 + 108, py + 70 + i * 112), 4, fill=(232, 237, 248))
    rr(d, (rx, py + 356, rx + 220, py + 414), 14, fill=BRAND)


def art_ai(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    card(d, px, py, 1004, 470, fill=(249, 250, 255), outline=(232, 236, 248))
    hub = (px + 420, py + 236)
    nodes = [(px + 730, py + 92), (px + 900, py + 268), (px + 700, py + 424),
             (px + 170, py + 430), (px + 92, py + 118), (px + 940, py + 96)]
    for n in nodes:
        line(d, [hub, n], (206, 214, 236), 2)
    ellipse(d, (hub[0] - 58, hub[1] - 58, hub[0] + 58, hub[1] + 58), fill=BRAND)
    ellipse(d, (hub[0] - 30, hub[1] - 30, hub[0] + 30, hub[1] + 30), fill=WHITE)
    for i, n in enumerate(nodes):
        ellipse(d, (n[0] - 26, n[1] - 26, n[0] + 26, n[1] + 26), fill=WHITE,
                outline=[ACCENT, TEAL, BRAND, ACCENT, TEAL, BRAND][i], width=5)
        ellipse(d, (n[0] - 9, n[1] - 9, n[0] + 9, n[1] + 9),
                fill=[ACCENT, TEAL, BRAND, ACCENT, TEAL, BRAND][i])
    card(d, px + 30, py + 30, 210, 78, fill=WHITE)
    rr(d, (px + 54, py + 54, px + 186, py + 68), 7, fill=(206, 213, 230))
    rr(d, (px + 54, py + 82, px + 150, py + 94), 6, fill=(226, 232, 246))
    card(d, px + 40, py + 340, 200, 86, fill=WHITE)
    rr(d, (px + 62, py + 364, px + 172, py + 376), 6, fill=(206, 213, 230))
    rr(d, (px + 62, py + 390, px + 140, py + 402), 6, fill=(226, 232, 246))
    rr(d, (px + 620, py + 190, px + 830, py + 250), 16, fill=BRAND)
    rr(d, (px + 648, py + 212, px + 790, py + 228), 8, fill=(255, 255, 255, 200))


def art_cloud(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    # cloud shape from overlapping circles
    for cx, cy, r in [(px + 300, py + 120, 84), (px + 400, py + 130, 62),
                      (px + 210, py + 140, 62), (px + 300, py + 170, 96)]:
        ellipse(d, (cx - r, cy - r, cx + r, cy + r), fill=(231, 236, 255))
    ellipse(d, (px + 330, py + 148, px + 350, py + 168), fill=BRAND)
    for i in range(3):
        cx = px + 60 + i * 222
        card(d, cx, py + 250, 190, 170)
        rr(d, (cx + 20, py + 272, cx + 96, py + 286), 7, fill=(196, 205, 226))
        rr(d, (cx + 20, py + 300, cx + 152, py + 310), 5, fill=(222, 229, 244))
        rr(d, (cx + 20, py + 318, cx + 128, py + 328), 5, fill=(232, 237, 248))
        ellipse(d, (cx + 20, py + 352, cx + 44, py + 376), fill=[TEAL, TEAL, (245, 165, 36)][i])
        rr(d, (cx + 56, py + 358, cx + 130, py + 370), 5, fill=(222, 229, 244))
    line(d, [(px + 210, py + 246), (px + 210, py + 210)], (214, 222, 242), 2)
    line(d, [(px + 340, py + 246), (px + 340, py + 214)], (214, 222, 242), 2)
    line(d, [(px + 470, py + 246), (px + 470, py + 210)], (214, 222, 242), 2)
    rr(d, (px + 20, py + 440, px + 686, py + 452), 6, fill=(238, 242, 251))


def art_funnel(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    polygon(d, [(px + 40, py + 30), (px + 640, py + 30), (px + 500, py + 150), (px + 180, py + 150)], BRAND)
    polygon(d, [(px + 196, py + 176), (px + 484, py + 176), (px + 404, py + 280), (px + 276, py + 280)], ACCENT)
    polygon(d, [(px + 292, py + 306), (px + 388, py + 306), (px + 356, py + 386), (px + 324, py + 386)], TEAL)
    rr(d, (px + 90, py + 58, px + 300, py + 72), 7, fill=(255, 255, 255))
    rr(d, (px + 236, py + 200, px + 400, py + 212), 6, fill=(255, 255, 255))
    rr(d, (px + 314, py + 326, px + 366, py + 336), 5, fill=(255, 255, 255))
    for i, (lbl, col) in enumerate([(BRAND, BRAND), (ACCENT, ACCENT), (TEAL, TEAL)]):
        card(d, px + 470, py + 176 + i * 74, 236, 60)
        ellipse(d, (px + 490, py + 192 + i * 74, px + 518, py + 220 + i * 74), fill=col)
        rr(d, (px + 532, py + 196 + i * 74, px + 532 + 132, py + 208 + i * 74), 6, fill=(196, 205, 226))
        rr(d, (px + 532, py + 216 + i * 74, px + 532 + 92, py + 226 + i * 74), 5, fill=(226, 232, 246))


def art_brand(d, box):
    x0, y0, x1, y1 = box
    px, py = x0 + 34, y0 + 74
    rr(d, (px, py, px + 230, py + 230), 24, fill=BRAND)
    ellipse(d, (px + 76, py + 76, px + 156, py + 156), fill=(255, 255, 255))
    ellipse(d, (px + 108, py + 108, px + 156, py + 156), fill=BRAND)
    rr(d, (px + 30, py + 176, px + 140, py + 196), 10, fill=(255, 255, 255))
    card(d, px + 252, py, 210, 108)
    for i, c in enumerate([BRAND, ACCENT, TEAL, INK]):
        ellipse(d, (px + 274 + i * 44, py + 22, px + 308 + i * 44, py + 56), fill=c)
    rr(d, (px + 274, py + 72, px + 420, py + 84), 6, fill=(206, 213, 230))
    card(d, px + 252, py + 122, 210, 108)
    rr(d, (px + 274, py + 146, px + 274 + 40, py + 190), 6, fill=(196, 205, 226))
    rr(d, (px + 330, py + 150, px + 330 + 110, py + 166), 8, fill=(158, 168, 198))
    rr(d, (px + 330, py + 178, px + 330 + 80, py + 188), 5, fill=(214, 221, 238))
    card(d, px + 486, py, 220, 230, fill=(245, 247, 253))
    rr(d, (px + 512, py + 30, px + 664, py + 176), 10, fill=WHITE, outline=(226, 232, 244))
    rr(d, (px + 528, py + 52, px + 648, py + 64), 6, fill=(206, 213, 230))
    rr(d, (px + 528, py + 82, px + 620, py + 92), 5, fill=(226, 232, 246))
    rr(d, (px + 528, py + 118, px + 560, py + 144), 8, fill=ACCENT)
    rr(d, (px + 512, py + 194, px + 664, py + 210), 8, fill=(206, 213, 230))
    card(d, px, py + 252, 706, 218, fill=(248, 250, 255), outline=(232, 237, 248))
    for i in range(4):
        rr(d, (px + 26 + i * 172, py + 280 + (i % 2) * 10, px + 26 + 150 + i * 172, py + 360 + (i % 2) * 10),
           12, fill=[BRAND, ACCENT, TEAL, (245, 165, 36)][i])
    rr(d, (px + 26, py + 392, px + 420, py + 404), 6, fill=(206, 213, 230))
    rr(d, (px + 26, py + 418, px + 300, py + 428), 5, fill=(226, 232, 246))


ART = {
    "dashboard": art_dashboard,
    "analytics": art_dashboard,
    "website": art_website,
    "table": art_table,
    "map": art_map,
    "kanban": art_kanban,
    "calendar": art_calendar,
    "ai": art_ai,
    "cloud": art_cloud,
    "funnel": art_funnel,
    "brand": art_brand,
}

HERO_MAP = {
    "home-dashboard": "dashboard",
    "services-overview": "analytics",
    "products-overview": "table",
    "projects": "map",
    "guides": "website",
    "about-team": "kanban",
    "contact-office": "calendar",
    "software-development": "table",
    "website-development": "website",
    "digital-marketing": "analytics",
    "performance-marketing": "funnel",
    "branding-creative": "brand",
    "it-services-cloud": "cloud",
    "ai-automation": "ai",
    "growth-strategy": "analytics",
    "routeflow": "map",
    "workora": "kanban",
    "bizora": "table",
    "medvora": "calendar",
    "schova": "calendar",
}


def make_hero(variant, filename, w=1200, h=760):
    W, H = w * S, h * S
    img = gradient((W, H), (255, 255, 255), (240, 245, 255))
    img = img.convert("RGBA")
    img = soft_blob(img, (W * 0.86, H * 0.10), 420 * S, BRAND, 60, 150 * S)
    img = soft_blob(img, (W * 0.06, H * 0.92), 380 * S, ACCENT, 55, 150 * S)
    img = soft_blob(img, (W * 0.55, H * 1.02), 300 * S, TEAL, 34, 140 * S)

    d = ImageDraw.Draw(img, "RGBA")
    box = window_panel(d, w, h)
    ART[variant](d, box)

    out = fit(img, (w, h))
    out = out.convert("RGB")
    path = os.path.join(ASSETS, "hero", filename)
    out.save(path, "WEBP", quality=88, method=6)
    return path


# ------------------------------------------------------------------- logo mark
def logo_mark(size=512, white=False, radius_ratio=0.27):
    W = size * S
    img = Image.new("RGBA", (W, W), (0, 0, 0, 0))
    d = ImageDraw.Draw(img, "RGBA")
    r = int(W * radius_ratio)
    if not white:
        grad = gradient((W, W), BRAND, ACCENT, diagonal=True).convert("RGBA")
        mask = Image.new("L", (W, W), 0)
        ImageDraw.Draw(mask).rounded_rectangle([0, 0, W - 1, W - 1], radius=r, fill=255)
        img.paste(grad, (0, 0), mask)
    else:
        d.rounded_rectangle([0, 0, W - 1, W - 1], radius=r,
                            fill=(255, 255, 255, 0), outline=(255, 255, 255, 90), width=int(2 * S))

    fg = (255, 255, 255, 255)
    f = font(FONT_BOLD, int(size * 0.60))
    tw = d.textlength("P", font=f) / S
    d.text(((size - tw) / 2 * S, size * 0.17 * S), "P", font=f, fill=fg)
    # ascending chevron accent
    cx, cy = size * 0.72, size * 0.66
    line(d, [(cx - size * 0.09, cy + size * 0.05), (cx, cy - size * 0.04),
             (cx + size * 0.09, cy + size * 0.05)], fg, 5)
    ellipse(d, (cx + size * 0.06, cy + size * 0.11, cx + size * 0.13, cy + size * 0.18), fill=fg)
    return img.resize((size, size), Image.LANCZOS)


def logo_lockup(width=1200, height=320, white=False):
    W, H = width * S, height * S
    img = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    d = ImageDraw.Draw(img, "RGBA")
    mark_size = int(height * 0.62)
    mark = logo_mark(mark_size, white=white)
    img.alpha_composite(mark, (int(10 * S), int((height - mark_size) / 2 * S)))

    x = 10 + mark_size + 34
    f1 = font(FONT_BOLD, int(height * 0.30))
    f2 = font(FONT_REG, int(height * 0.155))
    ink = (255, 255, 255, 255) if white else INK + (255,)
    sub = (255, 255, 255, 210) if white else MUTED + (255,)
    d.text((x * S, height * 0.20 * S), "Prospect", font=f1, fill=ink)
    text_ls(d, (x + 2, height * 0.575), "DIGITAL", f2, sub, height * 0.10)
    return img.resize((width, height), Image.LANCZOS)


def og_image(path, w=1200, h=630):
    img = gradient((w * S, h * S), NAVY, NAVY_2).convert("RGBA")
    img = soft_blob(img, (w * S * 0.86, h * S * 0.18), 330 * S, BRAND, 130, 120 * S)
    img = soft_blob(img, (w * S * 0.12, h * S * 0.92), 300 * S, ACCENT, 110, 120 * S)
    d = ImageDraw.Draw(img, "RGBA")

    mark_size = int(h * 0.20)
    img.alpha_composite(logo_mark(mark_size), (int(w * 0.085 * S), int(h * 0.16 * S)))

    f_eyebrow = font(FONT_REG, 17)
    f_title = font(FONT_BOLD, 62)
    f_sub = font(FONT_REG, 26)
    f_small = font(FONT_REG, 21)

    text_ls(d, (w * 0.085, h * 0.40), "BUILD. GROW. SCALE.", f_eyebrow, (150, 168, 255, 255), 5)
    d.text((w * 0.085 * S, h * 0.455 * S), "Prospect Digital", font=f_title, fill=(255, 255, 255, 255))
    d.text((w * 0.085 * S, h * 0.63 * S), "Digital solutions that deliver business growth.", font=f_sub, fill=(206, 214, 240, 255))
    d.text((w * 0.085 * S, h * 0.72 * S),
           "Software  ·  Websites  ·  Marketing  ·  Cloud  ·  AI  ·  Growth",
           font=f_small, fill=(170, 182, 220, 255))
    rr(d, (w * 0.085, h * 0.815, w * 0.085 + 250, h * 0.815 + 56), 28, fill=BRAND)
    d.text((w * 0.085 * S + 40 * S, h * 0.845 * S), "prospectdigital.in", font=f_small, fill=(255, 255, 255, 255))

    fit(img, (w, h)).convert("RGB").save(path, "JPEG", quality=90, optimize=True, progressive=True)


def main():
    for sub in ("logo", "images", "hero"):
        os.makedirs(os.path.join(ASSETS, sub), exist_ok=True)

    # ---- logos -------------------------------------------------------------
    logo_mark(512).save(os.path.join(ASSETS, "logo", "prospect-digital-mark.png"), "PNG", optimize=True)
    logo_lockup(1200, 320).save(os.path.join(ASSETS, "logo", "prospect-digital-logo.png"), "PNG", optimize=True)
    logo_lockup(1200, 320, white=True).save(os.path.join(ASSETS, "logo", "prospect-digital-logo-white.png"), "PNG", optimize=True)

    # White mark for dark backgrounds (used by the footer): white glyph + ring
    wm = Image.new("RGBA", (512 * S, 512 * S), (0, 0, 0, 0))
    wd = ImageDraw.Draw(wm, "RGBA")
    wd.rounded_rectangle([0, 0, 512 * S - 1, 512 * S - 1], radius=int(512 * S * 0.27),
                         fill=(255, 255, 255, 16), outline=(255, 255, 255, 96), width=int(3 * S))
    f = font(FONT_BOLD, int(512 * 0.60))
    tw = wd.textlength("P", font=f) / S
    wd.text(((512 - tw) / 2 * S, 512 * 0.17 * S), "P", font=f, fill=(255, 255, 255, 255))
    wd.line([(512 * 0.63 * S, 512 * 0.71 * S), (512 * 0.72 * S, 512 * 0.62 * S),
             (512 * 0.81 * S, 512 * 0.71 * S)], fill=(255, 255, 255, 235), width=int(5 * S), joint="curve")
    wd.ellipse([512 * 0.78 * S, 512 * 0.77 * S, 512 * 0.85 * S, 512 * 0.84 * S], fill=(255, 255, 255, 235))
    wm.resize((512, 512), Image.LANCZOS).save(
        os.path.join(ASSETS, "logo", "prospect-digital-mark-white.png"), "PNG", optimize=True)

    # ---- favicons ----------------------------------------------------------
    fav = logo_mark(512)
    fav.save(os.path.join(ASSETS, "logo", "favicon.png"), "PNG", optimize=True)
    ico = logo_mark(256).convert("RGBA")
    ico.save(os.path.join(ROOT, "favicon.ico"), sizes=[(16, 16), (32, 32), (48, 48), (64, 64)])
    apple = Image.new("RGB", (180 * S, 180 * S), (255, 255, 255))
    m = logo_mark(180)
    apple.paste(m, (0, 0), m)
    apple.resize((180, 180), Image.LANCZOS).save(
        os.path.join(ASSETS, "logo", "apple-touch-icon.png"), "PNG", optimize=True)

    # ---- social share image ------------------------------------------------
    og_image(os.path.join(ASSETS, "images", "og-default.jpg"))

    # ---- hero artwork ------------------------------------------------------
    for slug, variant in HERO_MAP.items():
        make_hero(variant, slug + ".webp")
        print("hero:", slug + ".webp", "->", variant)

    print("logos, favicons and social image written.")


if __name__ == "__main__":
    main()
