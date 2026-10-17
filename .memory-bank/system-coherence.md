# System Coherence & Architecture Matrix

## Project Identity
- **Project Name:** GNN WordPress Theme (`gnn-wptheme`)
- **Repository URL:** https://github.com/BigDesigner/gnn-wptheme.git
- **Type:** WordPress Classic PHP Theme (Dark-by-default, corporate IT / cybersecurity focus)
- **Version:** 1.6.1
- **Ecosystem:** PHP 7.4+, WordPress 6.0+, Elementor Free API, WooCommerce

---

## Architectural Principles
1. **Zero External Framework Dependency in Admin:** The GNN Theme Panel is built strictly using pure WordPress Settings API (`register_setting()`, `add_menu_page()`, `settings_fields()`).
2. **Dynamic Styling & Toggle Governance:** Every theme feature, animation, top bar, dock, and preloader can be toggled on/off and configured via GNN Theme Panel.
3. **Backwards Compatibility:** Legacy theme mods (`logo_light`, `logo_light_2x`, `logo_dark`, `logo_dark_2x`, `gnn_accent_color`) are preserved and synchronized between Customizer and GNN Panel.
4. **Performance & Light Footprint:** Assets are loaded conditionally; WooCommerce content, dynamic builder/widget content and shared header cart fragments retain the assets they need. Space Grotesk/Manrope WOFF2 fonts are self-hosted. Google Fonts typography is opt-in; Material Icons is independently toggleable and enabled by default in the current option defaults.
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
    ├── admin-panel.php            # GNN Theme Panel 9-tab settings interface (CodeMirror-enhanced Custom Code tab)
    ├── options.php                # Option defaults, getters (gnn_option), logo helpers, shared Google Fonts preconnect
    ├── typography.php              # Optional per-element (H1-H6, paragraph) Google Fonts — off by default, self-hosted fonts otherwise
    ├── button-styles.php            # 6 fixed, panel-defined button style slots (.gnn-btn-style-N classes)
    ├── frontend.php                # Topbar, dock, scroll-to-top, preloader, badge hooks
    ├── page-layouts.php            # Page layout renderer (full, boxed, right, left)
    ├── page-meta.php                # Per-page display options metabox (_gnn_hide_title, _gnn_hide_breadcrumb)
    ├── slider.php                    # Custom Post Type (gnn_slide) hero slider manager
    ├── patterns.php                  # Gutenberg Block Patterns registration (14 pattern parts)
    ├── elementor.php                  # Elementor theme locations + Page Settings controls (hide title/breadcrumb, CSS print method default)
    ├── elementor-templates.php        # Independently versioned library; stable IDs and edit-preserving refresh
    ├── updater.php                     # GitHub self-updater bootstrap (admin + cron/CLI)
    ├── class-gnn-github-updater.php    # GitHub Releases API client, WP upgrader integration
    └── woocommerce.php                 # WooCommerce support hooks and styling bridges
```

---

## Build Pipeline
Packaging is handled by Python build tooling:
- **Build Command:** `python .scripts/build-zip.py`
- **Output:** `.build/gnn.zip`

Distribution is GitHub/private ZIP only. WordPress.org packaging is retired.
CSS is copied byte-for-byte. `.scripts/check-release.py` validates synchronized
version declarations before packaging; release overrides must match the source.
`.github/workflows/verify.yml` provides static/package and real WordPress/browser
checks; the manual release workflow depends on it. See ADR 0014–0016 for updater,
Elementor persistence and release topology decisions.
