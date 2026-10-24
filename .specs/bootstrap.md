# Technical Bootstrap Specification

## Environment Requirements
- **PHP:** 7.4 or higher (tested up to PHP 8.2)
- **WordPress:** 6.0 or higher
- **Build Tool:** Python 3.8+ (for running `.scripts/build-zip.py`)

## Recommended Development Setup
1. Local WordPress environment (e.g. LocalWP, XAMPP, Docker, DDEV).
2. Theme directory linked or placed in `wp-content/themes/gnn/`.
3. Activated plugins for full testing: WooCommerce, Elementor (Free).

## Build & Package Command
```bash
python .scripts/build-zip.py
```
This produces `.build/gnn.zip` containing the complete, installable theme package.

Distribution is GitHub/private ZIP only. WordPress.org packaging is retired by
the user's 2026-10-18 instruction. The ignored old `gnn-wporg.zip` is not maintained.

## Verification

Static syntax, synchronized versions and package byte/integrity checks:

```bash
python .scripts/verify.py
```

Real isolated WordPress/SQLite plus Chromium UI checks (development-only tools):

```bash
npm install --prefix .temp/qa-tools --ignore-scripts --no-audit --no-fund @wp-playground/cli@3.1.58 playwright@1.64.1 axe-core@4.11.0
node .scripts/test-wordpress.mjs
```

The Playground package requires Node 24.18.0+ and npm 11.16.0+. On Windows the
runner uses an installed Chrome/Edge, or `GNN_QA_BROWSER` if set. On Linux install
Chromium with `.temp/qa-tools/node_modules/.bin/playwright install --with-deps chromium`.
Results are written under `.memory-bank/audits/verification-<theme-version>/`.
The test environment is disposable and does not use development/production data.
PHP and browser coverage is unmeasured unless separately instrumented.

WordPress coding standards use the committed development-only Composer lock:

```bash
composer install --no-interaction --prefer-dist
composer lint
```

On Windows with the bundled PHP, enable its `mbstring` extension for PHPCS.
No Composer/vendor files ship in the theme ZIP. CI runs WPCS on all theme PHP.

To run without optional plugins in PowerShell:

```powershell
$env:GNN_QA_MINIMUM = '1'
$env:GNN_QA_WP = '7.1.3'
node .scripts/test-wordpress.mjs
```

This still runs the panel HTTP upload/authorization checks, Arabic/Turkish
language checks, axe WCAG scans and local navigation/layout measurements.
The test installs real WordPress language packs; outbound translation requests
therefore need network access. Local measurements are not production Web Vitals.
