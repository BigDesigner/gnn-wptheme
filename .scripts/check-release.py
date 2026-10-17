"""Validate the single release version before packaging or publication."""
import argparse
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parent.parent

def release_version(override=''):
    def read(relative):
        return (ROOT / relative).read_text(encoding='utf-8')
    def match(relative, pattern):
        found = re.search(pattern, read(relative), re.M)
        if not found:
            raise ValueError(f'Missing version declaration: {relative}')
        return found.group(1)
    version = match('gnn/style.css', r'^Version:\s*(\S+)')
    if not re.fullmatch(r'\d+\.\d+\.\d+', version):
        raise ValueError('Theme version must use MAJOR.MINOR.PATCH')
    if override and override != version:
        raise ValueError(f'Release override {override!r} differs from source {version}')
    declarations = {
        'gnn/functions.php': r"define\(\s*'GNN_VERSION',\s*'([^']+)'",
        'gnn/readme.txt': r'^Stable tag:\s*(\S+)',
        'gnn/languages/gnn.pot': r'Project-Id-Version: GNN ([0-9.]+)',
        'gnn/languages/tr_TR.po': r'Project-Id-Version: GNN ([0-9.]+)',
        '.memory-bank/system-coherence.md': r'\*\*Version:\*\*\s*(\S+)',
    }
    for relative, pattern in declarations.items():
        if match(relative, pattern) != version:
            raise ValueError(f'Version mismatch in {relative}; expected {version}')
    if not re.search(r'^= ' + re.escape(version) + r' =$', read('gnn/readme.txt'), re.M):
        raise ValueError('Current version has no readme changelog entry')
    return version

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--version-override', default='')
    args = parser.parse_args()
    try:
        print(release_version(args.version_override))
    except ValueError as error:
        print(f'Release validation failed: {error}', file=sys.stderr)
        sys.exit(1)
