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
