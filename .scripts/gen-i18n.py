# -*- coding: utf-8 -*-
"""Extract gettext strings from the theme, regenerate languages/gnn.pot,
emit languages/tr_TR.po from the TRANSLATIONS table and compile tr_TR.mo.

Usage: python scripts/gen-i18n.py [--list]
"""
import os
import re
import struct
import sys
import time

sys.stdout.reconfigure(encoding='utf-8', errors='replace')

HERE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(HERE, 'gnn')
LANG = os.path.join(THEME, 'languages')

with open(os.path.join(THEME, 'style.css'), encoding='utf-8') as _f:
    THEME_VERSION = re.search(r'^Version:\s*(\S+)', _f.read(), re.M).group(1)

SINGULAR = re.compile(
    r"(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'gnn'"
)
SINGULAR_DQ = re.compile(
    r'(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*"((?:[^"\\]|\\.)*)"\s*,\s*\'gnn\''
)
PLURAL = re.compile(
    r"_n\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'((?:[^'\\]|\\.)*)'\s*,[^,]+,\s*'gnn'"
)

entries = {}   # msgid -> {'plural': str|None, 'refs': [file:line]}

for root, dirs, files in os.walk(THEME):
    dirs[:] = [d for d in dirs if d not in ('fonts', 'demo')]
    for f in sorted(files):
        if not f.endswith('.php'):
            continue
        path = os.path.join(root, f)
        rel = os.path.relpath(path, THEME).replace(os.sep, '/')
        with open(path, encoding='utf-8') as fh:
            for lineno, line in enumerate(fh, 1):
                for match in SINGULAR.finditer(line):
                    mid = match.group(1).replace("\\'", "'")
                    entries.setdefault(mid, {'plural': None, 'refs': []})['refs'].append(f'{rel}:{lineno}')
                for match in SINGULAR_DQ.finditer(line):
                    mid = match.group(1).replace('\\"', '"')
                    entries.setdefault(mid, {'plural': None, 'refs': []})['refs'].append(f'{rel}:{lineno}')
                for match in PLURAL.finditer(line):
                    mid = match.group(1).replace("\\'", "'")
                    entry = entries.setdefault(mid, {'plural': None, 'refs': []})
                    entry['plural'] = match.group(2).replace("\\'", "'")
                    entry['refs'].append(f'{rel}:{lineno}')

if '--list' in sys.argv:
    for mid in sorted(entries):
        mark = ' [PLURAL: %s]' % entries[mid]['plural'] if entries[mid]['plural'] else ''
        print(repr(mid) + mark)
    print('TOTAL:', len(entries))
    sys.exit(0)


def po_escape(text):
    return text.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n')


NOW = time.strftime('%Y-%m-%d %H:%M+0000')
HEADER = '''msgid ""
msgstr ""
"Project-Id-Version: GNN {version}\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: {now}\\n"
"PO-Revision-Date: {now}\\n"
"Language-Team: \\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"X-Generator: gen-i18n.py\\n"
{extra}
'''

# --- POT ---
os.makedirs(LANG, exist_ok=True)
with open(os.path.join(LANG, 'gnn.pot'), 'w', encoding='utf-8', newline='\n') as f:
    f.write(HEADER.format(now=NOW, version=THEME_VERSION, extra='"Language: \\n"\n'))
    for mid in sorted(entries):
        e = entries[mid]
        f.write('\n')
        for ref in e['refs'][:3]:
            f.write(f'#: {ref}\n')
        f.write(f'msgid "{po_escape(mid)}"\n')
        if e['plural']:
            f.write(f'msgid_plural "{po_escape(e["plural"])}"\n')
            f.write('msgstr[0] ""\nmsgstr[1] ""\n')
        else:
            f.write('msgstr ""\n')
print('gnn.pot:', len(entries), 'strings')

# --- Turkish translations ---
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from tr_strings import TRANSLATIONS, PLURALS  # noqa: E402

missing = [m for m in entries if m not in TRANSLATIONS and not entries[m]['plural']]
missing += [m for m in entries if entries[m]['plural'] and m not in PLURALS]
if missing:
    print('MISSING TURKISH (%d):' % len(missing))
    for m in sorted(missing):
        print('  ', repr(m))

with open(os.path.join(LANG, 'tr_TR.po'), 'w', encoding='utf-8', newline='\n') as f:
    f.write(HEADER.format(
        now=NOW,
        version=THEME_VERSION,
        extra='"Language: tr_TR\\n"\n"Plural-Forms: nplurals=2; plural=(n > 1);\\n"\n'))
    for mid in sorted(entries):
        e = entries[mid]
        f.write('\n')
        f.write(f'msgid "{po_escape(mid)}"\n')
        if e['plural']:
            forms = PLURALS.get(mid, ('', ''))
            f.write(f'msgid_plural "{po_escape(e["plural"])}"\n')
            f.write(f'msgstr[0] "{po_escape(forms[0])}"\n')
            f.write(f'msgstr[1] "{po_escape(forms[1])}"\n')
        else:
            f.write(f'msgstr "{po_escape(TRANSLATIONS.get(mid, ""))}"\n')

# --- Compile .mo ---
catalog = {}
catalog[''] = ('Content-Type: text/plain; charset=UTF-8\n'
               'Plural-Forms: nplurals=2; plural=(n > 1);\n'
               'Language: tr_TR\n')
for mid, e in entries.items():
    if e['plural']:
        forms = PLURALS.get(mid)
        if forms and any(forms):
            catalog[mid + '\x00' + e['plural']] = forms[0] + '\x00' + forms[1]
    else:
        tr = TRANSLATIONS.get(mid, '')
        if tr:
            catalog[mid] = tr

keys = sorted(catalog)
offsets = []
ids = strs = b''
for key in keys:
    id_b = key.encode('utf-8')
    str_b = catalog[key].encode('utf-8')
    offsets.append((len(ids), len(id_b), len(strs), len(str_b)))
    ids += id_b + b'\x00'
    strs += str_b + b'\x00'

n = len(keys)
keystart = 28 + 16 * n
valuestart = keystart + len(ids)
koffsets, voffsets = [], []
for o1, l1, o2, l2 in offsets:
    koffsets += [l1, o1 + keystart]
    voffsets += [l2, o2 + valuestart]

mo = struct.pack('Iiiiiii', 0x950412de, 0, n, 28, 28 + n * 8, 0, keystart)
mo += struct.pack('i' * n * 2, *koffsets)
mo += struct.pack('i' * n * 2, *voffsets)
mo += ids + strs
with open(os.path.join(LANG, 'tr_TR.mo'), 'wb') as f:
    f.write(mo)
print('tr_TR.mo:', n, 'entries,', len(mo), 'bytes')
