"""Static/package verification. Real integration/UI tests: node .scripts/test-wordpress.mjs."""
from pathlib import Path
import argparse
import gettext
import fnmatch
import json
import os
import re
import subprocess
import sys
import zipfile

parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--package', action='store_true', help='Explicitly build and verify ZIPs; disabled by default.')
parser.add_argument('--out', default=os.environ.get('GNN_VERIFY_OUT', ''),
    help='Directory for the JSON result. Default: .temp/verification-<version>; not written in GitHub Actions unless set.')
args = parser.parse_args()

ROOT = Path(__file__).resolve().parent.parent
VERSION = re.search(r'^Version:\s*(\S+)', (ROOT/'gnn/style.css').read_text('utf-8'), re.M).group(1)
if args.out:
    OUT = Path(args.out) if Path(args.out).is_absolute() else ROOT / args.out
elif os.environ.get('GITHUB_ACTIONS') == 'true':
    OUT = None  # CI logs carry the summary; keep the checkout clean.
else:
    OUT = ROOT / ('.temp/verification-' + VERSION)
# Mirrors build-zip.py's exclude list for junk/dev files.
EXCLUDE_DIRS = {'node_modules', '__pycache__'}
EXCLUDE_FILES = ('.DS_Store', 'Thumbs.db', 'desktop.ini', '*.swp', '*~', '.git*', '*.map', '*.pyc')
def shipped(path, base):
    parts = path.relative_to(base).parts
    if any(d in EXCLUDE_DIRS or d.startswith('.git') for d in parts[:-1]):
        return False
    return not any(fnmatch.fnmatch(parts[-1], pattern) for pattern in EXCLUDE_FILES)
checks = []
def run(args):
    p = subprocess.run(args, cwd=ROOT, capture_output=True, text=True, encoding='utf-8', errors='replace')
    return p.returncode, (p.stdout + p.stderr).strip()
def check(name, passed, detail=''):
    checks.append(dict(name=name, pass_=bool(passed), detail=detail))

for kind, files, command in [
    ('PHP syntax', list((ROOT/'gnn').rglob('*.php')), ['php','-l']),
    ('JavaScript syntax', list((ROOT/'gnn/assets/js').glob('*.js')), ['node','--check'])]:
    for file in files:
        code, output = run(command + [str(file)])
        check(f'{kind}: {file.relative_to(ROOT)}', code == 0, output if code else '')
for override in ['', VERSION, '999.0.0', '$(echo invalid)', VERSION + ';echo invalid']:
    code, output = run([sys.executable,'.scripts/check-release.py','--version-override',override])
    check(f'Release validation: {override or "source"}', (code == 0) == (override in ['', VERSION]), output)
if args.package:
    code, output = run([sys.executable,'.scripts/build-zip.py'])
    check('Normal theme ZIP build', code == 0, output.splitlines()[-1] if output else '')
    package = ROOT/'.build/gnn.zip'
    if code == 0:
        with zipfile.ZipFile(package) as z:
            check('ZIP CRC integrity', z.testzip() is None)
            expected = sorted('gnn/' + p.relative_to(ROOT/'gnn').as_posix() for p in (ROOT/'gnn').rglob('*') if p.is_file() and shipped(p, ROOT/'gnn'))
            check('ZIP contains exactly the theme tree', sorted(z.namelist()) == expected)
            check('ZIP carries source version', ('Version: ' + VERSION).encode() in z.read('gnn/style.css'))
            for entry in z.namelist():
                if entry.endswith('.css'):
                    check('Lossless packaged CSS: ' + entry, z.read(entry) == (ROOT/entry).read_bytes())
                if entry.endswith('.js'):
                    p = subprocess.run(['node','--check','--input-type=commonjs'], input=z.read(entry), capture_output=True)
                    check('Packaged JavaScript syntax: ' + entry, p.returncode == 0, p.stderr.decode('utf-8',errors='replace'))
        versioned = ROOT/('.build/gnn-' + VERSION + '.zip')
        check('Versioned theme ZIP matches gnn.zip', versioned.is_file() and versioned.read_bytes() == package.read_bytes())
        for slug, header in (('gnn-child', 'style.css'), ('gnn-content', 'gnn-content.php')):
            archive = ROOT/('.build/' + slug + '-' + VERSION + '.zip')
            if not archive.is_file():
                check(slug + ' ZIP built', False, str(archive.relative_to(ROOT)))
                continue
            with zipfile.ZipFile(archive) as z:
                source = ROOT/'packages'/slug
                expected = sorted(slug + '/' + p.relative_to(source).as_posix() for p in source.rglob('*') if p.is_file() and shipped(p, source))
                check(slug + ' ZIP CRC integrity', z.testzip() is None)
                check(slug + ' ZIP contains exactly the package tree', sorted(z.namelist()) == expected)
                check(slug + ' ZIP carries source version', re.search(r'Version:\s*' + re.escape(VERSION) + r'\s', z.read(slug + '/' + header).decode('utf-8')) is not None)
        manifest = ROOT/('.build/gnn-' + VERSION + '-manifest.json')
        check('Release manifest version', manifest.is_file() and json.loads(manifest.read_text('utf-8')).get('version') == VERSION)
for file in (ROOT/'gnn').rglob('*.json'):
    try:
        json.loads(file.read_text('utf-8')); valid=True
    except ValueError:
        valid=False
    check('JSON: ' + str(file.relative_to(ROOT)), valid)
with (ROOT/'gnn/languages/tr_TR.mo').open('rb') as file:
    catalog = gettext.GNUTranslations(file)
check('Turkish slider labels compiled', catalog.gettext('Next slide') != 'Next slide')
code, output = run([sys.executable, '.scripts/gen-i18n.py', '--check'])
check('Complete Turkish catalog and printf placeholders', code == 0, output)
result = {'type':('Static / package checks' if args.package else 'Static checks') + ' (not WordPress integration)',
    'total':len(checks),'passed':sum(x['pass_'] for x in checks),
    'failed':sum(not x['pass_'] for x in checks),'checks':checks}
if OUT is not None:
    OUT.mkdir(parents=True, exist_ok=True)
    (OUT/('static-package.json' if args.package else 'static.json')).write_text(json.dumps(result,ensure_ascii=False,indent=2),encoding='utf-8')
print(json.dumps({k:v for k,v in result.items() if k!='checks'},ensure_ascii=False))
for item in checks:
    if not item['pass_']:
        print(item['name'] + ': ' + item['detail'])
sys.exit(bool(result['failed']))
