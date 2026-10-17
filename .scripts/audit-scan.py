"""Compatibility entry point for current static/package verification.
The original 1.6.0 observations remain in .tasks/audits/2026-10-18/evidence.json.
Real WordPress/browser tests: node .scripts/test-wordpress.mjs
"""
from pathlib import Path
import runpy
runpy.run_path(str(Path(__file__).with_name('verify.py')), run_name='__main__')
