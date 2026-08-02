# Verified Worklog

## [2026-08-01] Sentinel Memory Bank Initialization
- Initialized Sentinel Agent Memory Bank version 1.1.0.
- Established `.memory-bank/`, `.specs/`, `.agents/`, `.tasks/` documentation system.
- Archived initial git commit state `a1e4f6f`.

## [2026-08-01] PRD Phase 2 (v1.1.0 - v1.2.0)
- Removed the Contact page template; per-page breadcrumb toggle covers its use case instead.
- Synced Elementor's native "Hide Title" / new "Hide Breadcrumb" Page Settings controls with the theme's own toggles.
- Featured image now renders full-width above the sidebar/content area, with separate height/fit/alignment controls for pages vs. blog posts.
- Added `content_top_padding` / `content_bottom_padding` options (GNN Panel → Pages Layout).
- Integrated Google Material Symbols as an optional icon font (GNN Panel → Icons).

## [2026-08-01 to 2026-08-02] GitHub self-updater + panel modernization (v1.1.0 - v1.3.9)
- Reviewed and fixed 6 real bugs in a user-authored `inc/updater.php`; rebuilt as `class-gnn-github-updater.php` + thin `updater.php` bootstrap, integrated into GNN Panel → Advanced. See [[0004-github-based-self-updater]].
- Modernized the entire GNN Panel admin UI: card-based header/stats/pill-tabs, hex-input + swatch color fields, centralized all theme colors into one Colors tab.
- Built all 14 Elementor Free Saved Templates mirroring the existing Gutenberg patterns (without touching the Gutenberg patterns themselves), plus a full multi-section "GNN Ana Sayfa" home page template showcasing all 5 GNN sub-brands.
- Fixed an Elementor hosting trap: External File CSS Print Method silently drops backgrounds/icons on hosts that restrict writes to `wp-content/uploads/elementor/css/`. Theme now defaults to Internal Embedding, self-healing on activation and `admin_init`. See [[0005-elementor-internal-css-default]].
- Reworked the mobile bottom dock: real SVG icons, fixed the shared `.theme-toggle` sizing collision with the header, fixed sun-icon visual weight, fixed scroll-to-top overlap, and centered the dock as a group to avoid third-party floating-widget collisions (cookie-consent icons).
- Added a Contact page picker (GNN Panel → Pages Layout) so the mobile dock's Contact link is admin-configurable.
- Established the semantic-versioning governance policy in `.specs/constitution.md`. See [[0006-semver-governance-policy]].
- Added `.github/workflows/release.yml` — real GitHub Releases via `workflow_dispatch`, reusing `.scripts/build-zip.py`. Verified via 5+ successful published releases (v1.3.4-v1.3.9).
- Full bug list for this phase: [[bug-list]].

## [2026-08-02] Governance cleanup + panel UI follow-up (v1.3.10)
- GNN Panel: fixed third-party admin notices (e.g. cache-clearing plugins) rendering inside the panel's title card, by adding the standard `wp-header-end` marker so WordPress core relocates them below the header instead.
- GNN Panel: replaced the purple/indigo accent with a monochrome black/white palette, and replaced every checkbox field with an on/off toggle switch. See [[0007-panel-monochrome-toggle-ui]].
- GNN Panel: consolidated the GitHub update check + "Update now" actions into a single dynamic button in the panel header (under the version badge) — shows "Check for updates" normally, becomes "Update to X.X.X" when a release is available. The Advanced tab keeps only the enable/disable setting.
- GNN Slider admin screen now documents that the slider only renders on the Gutenberg front page template, not when Elementor replaces front page content.
- Documentation gap cleanup: `.tasks/pipeline.md` and `.memory-bank/bugs/bug-list.md` brought up to date with all work completed since the Sentinel bootstrap; removed the unused duplicate root-level `gnn-demo-content.xml` (the real copy lives at `gnn/demo/gnn-demo-content.xml`).
- Added ADRs 0004-0007 documenting the GitHub updater, Elementor CSS default, semver policy, and panel UI conventions.

## [2026-08-02] Typography Google Fonts system + CodeMirror (v1.3.11)
- Added `inc/typography.php`: an opt-in, per-element (H1-H6, paragraph) Google Fonts system exposed in GNN Panel → Typography. Off by default — self-hosted Space Grotesk/Manrope tokens (`--font-heading`/`--font-body`) are untouched and no extra request happens until enabled.
- When enabled, every configured family+weight is merged into ONE `fonts.googleapis.com/css2` request (never one per font), gated behind a shared preconnect (`gnn_google_fonts_preconnect()`, generalized from the existing Material Symbols preconnect to avoid duplicate `<link>` tags), with `display=swap`. Each role falls back to the theme default via CSS `var(--font-h1, var(--font-heading))` etc., so an unconfigured role costs nothing.
- No explicit "Latin Extended" toggle was added: Google's css2 endpoint already serves per-subset `@font-face` blocks gated by `unicode-range`, so the browser lazy-fetches the Turkish-glyph (ğ ı ş ö ü ç) subset automatically only when the page's actual text needs it — matching how the theme's own self-hosted fonts already work.
- Custom Code tab (custom_css / custom_js_head / custom_js_footer / head_html / body_html) now uses `wp_enqueue_code_editor()` / `wp.codeEditor` — WordPress core's own bundled CodeMirror, the same one behind Customizer → Additional CSS — for syntax highlighting. No third-party library added; gracefully falls back to a plain textarea if the site owner disabled the code editor in their WP profile.
- Dependency audit performed on request: confirmed the theme has no composer/npm dependencies, no bundled third-party JS libraries, and only three deliberately opt-in external calls (GitHub Releases API for updates, Material Symbols CDN, and now Typography's Google Fonts CDN) — all gated behind explicit user-enabled options, consistent with the "WordPress core + Elementor core" governance direction.

## [2026-08-02] Admin menu restructure (v1.3.12)
- "GNN Theme" and "GNN Slider" were two separate top-level wp-admin menu entries. Consolidated into one "GNN" top-level parent with "Theme" and "Slider" as its two submenus: `gnn_panel_menu()` now also calls `add_submenu_page()` on the same 'gnn-panel' slug to rename the auto-generated first submenu item, and the `gnn_slide` CPT's `show_in_menu` changed from `true` to the parent slug string `'gnn-panel'`.
