"""Static/package verification. Real integration/UI tests: node .scripts/test-wordpress.mjs."""
from pathlib import Path
import gettext
import json
import re
import subprocess
import sys
import zipfile

ROOT = Path(__file__).resolve().parent.parent
VERSION = re.search(r'^Version:\s*(\S+)', (ROOT/'gnn/style.css').read_text('utf-8'), re.M).group(1)
OUT = ROOT / ('.memory-bank/audits/verification-' + VERSION)
OUT.mkdir(parents=True, exist_ok=True)
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
code, output = run([sys.executable,'.scripts/build-zip.py'])
check('Normal theme ZIP build', code == 0, output.splitlines()[-1] if output else '')
package = ROOT/'.build/gnn.zip'
if code == 0:
    with zipfile.ZipFile(package) as z:
        check('ZIP CRC integrity', z.testzip() is None)
        expected = sorted('gnn/' + p.relative_to(ROOT/'gnn').as_posix() for p in (ROOT/'gnn').rglob('*') if p.is_file())
        check('ZIP contains exactly the theme tree', sorted(z.namelist()) == expected)
        check('ZIP carries source version', ('Version: ' + VERSION).encode() in z.read('gnn/style.css'))
        for entry in z.namelist():
            if entry.endswith('.css'):
                check('Lossless packaged CSS: ' + entry, z.read(entry) == (ROOT/entry).read_bytes())
            if entry.endswith('.js'):
                p = subprocess.run(['node','--check','--input-type=commonjs'], input=z.read(entry), capture_output=True)
                check('Packaged JavaScript syntax: ' + entry, p.returncode == 0, p.stderr.decode('utf-8',errors='replace'))
for file in (ROOT/'gnn').rglob('*.json'):
    try:
        json.loads(file.read_text('utf-8')); valid=True
    except ValueError:
        valid=False
    check('JSON: ' + str(file.relative_to(ROOT)), valid)
with (ROOT/'gnn/languages/tr_TR.mo').open('rb') as file:
    catalog = gettext.GNUTranslations(file)
check('Turkish slider labels compiled', catalog.gettext('Start slide rotation') == 'Slayt geçişini başlat')
result = {'type':'Static / package checks (not WordPress integration)',
    'total':len(checks),'passed':sum(x['pass_'] for x in checks),
    'failed':sum(not x['pass_'] for x in checks),'checks':checks}
(OUT/'static-package.json').write_text(json.dumps(result,ensure_ascii=False,indent=2),encoding='utf-8')
print(json.dumps({k:v for k,v in result.items() if k!='checks'},ensure_ascii=False))
for item in checks:
    if not item['pass_']:
        print(item['name'] + ': ' + item['detail'])
sys.exit(bool(result['failed']))
