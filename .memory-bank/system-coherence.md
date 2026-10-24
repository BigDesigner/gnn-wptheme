# System Coherence & Architecture Matrix

## Project Identity
- **Project Name:** GNN WordPress Theme (`gnn-wptheme`)
- **Repository URL:** https://github.com/BigDesigner/gnn-wptheme.git
- **Type:** WordPress Classic PHP Theme (Dark-by-default, corporate IT / cybersecurity focus)
- **Version:** 1.6.2
- **Ecosystem:** PHP 7.4+, WordPress 6.0+, Elementor Free API, WooCommerce

---

## Architectural Principles
1. **Zero External Framework Dependency in Admin:** The GNN Theme Panel is built strictly using pure WordPress Settings API (`register_setting()`, `add_menu_page()`, `settings_fields()`).
2. **Dynamic Styling & Toggle Governance:** Every theme feature, animation, top bar, dock, and preloader can be toggled on/off and configured via GNN Theme Panel.
3. **Backwards Compatibility:** Legacy logo theme mods (attachment IDs or HTTP(S) URLs) remain fallbacks; explicit panel values, including removal, take precedence. Shared accent/mode/legal values remain theme_mods used by both interfaces.
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
    ├── settings-transfer.php      # Bounded JSON schema validation, portable export and partial-import merge
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

1.6.2 adds Composer-locked WPCS and complete Turkish-catalog checks to verification.
Real WordPress tests cover panel HTTP uploads/capabilities, legacy logo handling,
RTL, axe WCAG automation and measured local asset/layout budgets. Evidence and
limits are in `.memory-bank/audits/remediation-1.6.2.md`. Only the deferred
user-site WooCommerce acceptance remains in the active backlog.

## Verified Release and Session State — 2026-10-24

- Current source and GitHub release: **1.6.2**, `main` / tag `v1.6.2` at `fda9916`.
- [Verify theme](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40045204839): passed 119/119 WordPress/Chromium checks with Elementor/WooCommerce, and 90/90 static/package checks; WPCS clean.
- [Manual Release](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40046157813): success; published asset `gnn-1.6.2.zip`, 271454 bytes. Digest and URLs: `audits/verification-1.6.2/github-release.json`.
- Local matrices without optional plugins each pass 89/89 (WP 7.1.3/PHP 8.2.33; WP 6.0.17/PHP 7.4.33).
- FAZ 1.34.2 live acceptance is closed. Only user-site WooCommerce visual/checkout acceptance remains deferred.
- No production installation of 1.6.2 is inferred. Start future sessions with `state-of-the-union.md`; historical plans/worklog entries are snapshots.
