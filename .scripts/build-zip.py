# -*- coding: utf-8 -*-
"""One-shot packager: generates gnn/screenshot.png (if missing) and gnn.zip.

Usage:  python build-zip.py   (run from this folder)
"""
import os
import struct
import zipfile
import zlib

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
THEME = os.path.join(ROOT, 'gnn')
BUILD = os.path.join(ROOT, '.build')
os.makedirs(BUILD, exist_ok=True)


def make_screenshot(path):
    W, H = 1200, 900
    BG, BG2 = (10, 10, 11), (18, 18, 20)
    LINE, FG, FG2 = (35, 35, 40), (245, 245, 244), (157, 157, 164)
    ACCENT, INK = (52, 211, 153), (6, 36, 22)
    px = [[BG] * W for _ in range(H)]

    def rect(x, y, w, h, c):
        for yy in range(max(0, y), min(H, y + h)):
            row = px[yy]
            for xx in range(max(0, x), min(W, x + w)):
                row[xx] = c

    def outline(x, y, w, h, c, t=2):
        rect(x, y, w, t, c); rect(x, y + h - t, w, t, c)
        rect(x, y, t, h, c); rect(x + w - t, y, t, h, c)

    rect(0, 78, W, 2, LINE)                    # header hairline
    rect(40, 26, 96, 30, FG); rect(140, 44, 12, 12, ACCENT)   # logo + dot
    for i in range(6):                          # nav pills
        rect(220 + i * 130, 34, 90, 14, FG2)
    rect(520, 240, 160, 12, ACCENT)             # kicker
    rect(220, 290, 760, 44, FG); rect(320, 350, 560, 44, FG)  # title
    rect(350, 430, 500, 12, FG2); rect(400, 452, 400, 12, FG2)
    rect(480, 520, 240, 56, ACCENT); rect(530, 542, 140, 12, INK)  # CTA
    outline(750, 520, 200, 56, LINE)            # ghost button
    rect(560, 620, 28, 10, ACCENT); rect(600, 620, 10, 10, FG2); rect(622, 620, 10, 10, FG2)
    vals_w = [110, 70, 80, 100]
    for i in range(4):                          # stat cards
        x = 40 + i * 290
        rect(x, 700, 270, 160, BG2); outline(x, 700, 270, 160, LINE)
        rect(x + 24, 730, vals_w[i], 30, ACCENT)
        rect(x + 24, 785, 180, 12, FG2); rect(x + 24, 805, 120, 12, FG2)

    def chunk(tag, data):
        return (struct.pack('>I', len(data)) + tag + data +
                struct.pack('>I', zlib.crc32(tag + data) & 0xffffffff))

    raw = b''.join(b'\x00' + bytes(v for p in row for v in p) for row in px)
    png = (b'\x89PNG\r\n\x1a\n'
           + chunk(b'IHDR', struct.pack('>IIBBBBB', W, H, 8, 2, 0, 0, 0))
           + chunk(b'IDAT', zlib.compress(raw, 9))
           + chunk(b'IEND', b''))
    with open(path, 'wb') as f:
        f.write(png)
    print('screenshot.png written:', len(png), 'bytes')


shot = os.path.join(THEME, 'screenshot.png')
if not os.path.exists(shot):
    make_screenshot(shot)

import shutil
import sys
WPORG = '--wporg' in sys.argv
if not WPORG and os.path.exists(os.path.join(ROOT, 'gnn-demo-content.xml')):
    shutil.copyfile(os.path.join(ROOT, 'gnn-demo-content.xml'),
                    os.path.join(THEME, 'demo', 'gnn-demo-content.xml'))

import re


def minify_css(text):
    """Safe CSS minify: strip comments, collapse whitespace."""
    text = re.sub(r'/\*.*?\*/', '', text, flags=re.S)
    text = re.sub(r'\s+', ' ', text)
    text = re.sub(r'\s*([{};:,>])\s*', r'\1', text)
    return text.replace(';}', '}').strip()


def minify_js(text):
    """Conservative JS minify: block comments, whole-line // comments,
    indentation and blank lines only — never touches code lines."""
    text = re.sub(r'/\*.*?\*/', '', text, flags=re.S)
    lines = []
    for line in text.splitlines():
        stripped = line.strip()
        if not stripped or stripped.startswith('//'):
            continue
        lines.append(stripped)
    return '\n'.join(lines)


zip_name = 'gnn-wporg.zip' if WPORG else 'gnn.zip'
zip_path = os.path.join(BUILD, zip_name)
with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk(THEME):
        if WPORG:
            dirs[:] = [d for d in dirs if d != 'demo']
        for f in sorted(files):
            p = os.path.join(root, f)
            arc = os.path.relpath(p, ROOT).replace(os.sep, '/')
            if f.endswith('.css') and f != 'style.css':
                with open(p, encoding='utf-8') as fh:
                    z.writestr(arc, minify_css(fh.read()))
            elif f.endswith('.js'):
                with open(p, encoding='utf-8') as fh:
                    z.writestr(arc, minify_js(fh.read()))
            else:
                z.write(p, arc)
            print('  +', arc)
print(f'{zip_name}: {os.path.getsize(zip_path)} bytes')
