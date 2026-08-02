# Project Constitution

## Core Governance
1. **Backward Compatibility:**
   - Existing Theme Mods (`logo_light`, `logo_light_2x`, `logo_dark`, `logo_dark_2x`, `gnn_accent_color`, `gnn_default_theme`, `gnn_footer_legal`) MUST never be removed or broken.
2. **Panel Control Principle:**
   - Every theme feature, effect, badge, topbar, app dock, and preloader MUST be controllable (toggleable on/off and customizable) via GNN Theme Panel.
3. **No Hardcoded Content:**
   - Templates MUST NOT contain hardcoded brand strings or untranslatable texts.
4. **Performance & Light Weight:**
   - Maintain zero external admin framework policy (Pure WP Settings API).
   - Bundle self-hosted WOFF2 fonts locally.
5. **Code Style & WordPress Standards:**
   - Follow WordPress Coding Standards (PHPCS WP rules).
   - Use strict comparisons (`===`, `!==`) and type checking.
6. **Semantic Versioning (every shippable commit):**
   - Every commit that changes theme behavior MUST bump the version, kept in sync in both `gnn/style.css` (`Version:` header) and the `GNN_VERSION` constant in `gnn/functions.php`.
   - Versions follow `MAJOR.MINOR.PATCH` (semver) — NEVER bump MAJOR by default. MAJOR only increments for breaking changes (removing/renaming a Theme Mod or panel option, dropping backward compatibility per Rule 1).
     - **PATCH** (`1.0.0` → `1.0.1`): bug fixes, copy/i18n tweaks, CSS/visual polish, no new options.
     - **MINOR** (`1.0.1` → `1.1.0`): new backward-compatible features (new panel option, new template, new integration).
     - **MAJOR** (`1.1.0` → `2.0.0`): breaking changes only, as defined above.
   - Docs-only or non-shipping commits (this constitution file, memory-bank notes, task plans) do not require a version bump.
   - **Every location that carries the version number MUST be updated together, in the same commit:**
     - `gnn/style.css` — `Version:` header (source of truth).
     - `gnn/functions.php` — `GNN_VERSION` constant.
     - `gnn/readme.txt` — `Stable tag:` field AND a new `== Changelog ==` entry (`= X.Y.Z =` with bullet points of what changed).
     - `.memory-bank/system-coherence.md` — `**Version:**` line.
     - `gnn/languages/gnn.pot` / `tr_TR.po` — regenerated automatically by `.scripts/gen-i18n.py`, which reads `Project-Id-Version` from `gnn/style.css` (do not hardcode it there).
7. **GNN Product Family Admin Menu Position Registry:**
   - Applies to every GNN-branded theme and plugin (this repo and all sibling products), not just this theme.
   - `add_menu_page()`'s `$position` argument is always passed as a **quoted string** (`'58.101'`), never a bare number — WordPress uses `"$position"` as the array key, and a bare float literal like `58.010` silently loses its trailing zero at parse time (becomes `58.01`), which can collide with a different product's slot.
   - Two position bands, chosen by product type so each family sits next to the WordPress core menu users already associate it with:
     - **Themes** → `'58.xyz'`–`'59.xyz'` (next to Appearance/Görünüm). `'59.100'` is reserved for GNN Tema (this theme).
     - **Plugins** → `'78.xyz'`–`'79.xyz'` (next to Settings/Ayarlar).
   - A product with more than one admin screen registers ONE top-level menu and nests the rest as `add_submenu_page()` entries under it — never multiple top-level menus for one product (see `gnn_panel_menu()` in `gnn/inc/admin-panel.php`, which nests the Slider CPT this way).
   - The full slot registry lives in [ADR 0009](../.memory-bank/adr/0009-gnn-product-family-menu-position-registry.md) — check it before assigning a new position, and add a row there for every new GNN product.
