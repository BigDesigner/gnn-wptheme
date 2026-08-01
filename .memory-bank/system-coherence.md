# System Coherence & Architecture Matrix

## Project Identity
- **Project Name:** GNN WordPress Theme (`gnn-wptheme`)
- **Type:** WordPress Classic PHP Theme (Dark-by-default, corporate IT / cybersecurity focus)
- **Version:** 1.0.0
- **Ecosystem:** PHP 7.4+, WordPress 6.0+, Elementor Free API, WooCommerce

---

## Architectural Principles
1. **Zero External Framework Dependency in Admin:** The GNN Theme Panel is built strictly using pure WordPress Settings API (`register_setting()`, `add_menu_page()`, `settings_fields()`).
2. **Dynamic Styling & Toggle Governance:** Every theme feature, animation, top bar, dock, and preloader can be toggled on/off and configured via GNN Theme Panel.
3. **Backwards Compatibility:** Legacy theme mods (`logo_light`, `logo_light_2x`, `logo_dark`, `logo_dark_2x`, `gnn_accent_color`) are preserved and synchronized between Customizer and GNN Panel.
4. **Performance & Light Footprint:** Assets are loaded conditionally (e.g. WooCommerce scripts load only on shop pages when scoped). Self-hosted WOFF2 fonts (`Space Grotesk`, `Manrope`) are bundled locally without external CDN requests (except optional Google Material Symbols font link when explicitly enabled).
5. **No Hardcoded Texts:** All user-facing strings are translatable via WordPress i18n (`gnn` textdomain) or configurable via theme settings.

---

## Component Architecture

```
gnn/
├── functions.php          # Core theme setup, enqueues, menu & sidebar registrations
├── header.php             # HTML head, theme-mode bootstrap, topbar, site header
├── footer.php             # Site footer, widget columns, brand logo, mobile dock, scroll-to-top
├── index.php / page.php   # Page layout callers (gnn_render_page)
├── template-parts/        # Modular page, search, archive & hero slider template parts
├── assets/
│   ├── css/               # main.css, admin.css, editor.css, woocommerce.css
│   ├── js/                # theme.js, admin.js, features.js, slider.js
│   └── fonts/             # Space Grotesk & Manrope variable WOFF2 fonts
└── inc/
    ├── admin-panel.php    # GNN Theme Panel 9-tab settings interface
    ├── options.php        # Option defaults, getters (gnn_option), logo helpers
    ├── frontend.php       # Topbar, dock, scroll-to-top, preloader, badge hooks
    ├── page-layouts.php   # Page layout renderer (full, boxed, right, left)
    ├── page-meta.php      # Per-page display options metabox (_gnn_hide_title, _gnn_hide_breadcrumb)
    ├── slider.php         # Custom Post Type (gnn_slide) hero slider manager
    ├── patterns.php       # Gutenberg Block Patterns registration (14 pattern parts)
    ├── elementor.php      # Elementor theme locations registration
    └── woocommerce.php    # WooCommerce support hooks and styling bridges
```

---

## Build Pipeline
Packaging is handled by Python build tooling:
- **Build Command:** `python .scripts/build-zip.py`
- **Output:** `.build/gnn.zip`
