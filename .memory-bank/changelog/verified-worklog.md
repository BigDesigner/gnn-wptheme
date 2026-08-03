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

## [2026-08-02] Button Styles + Colors regroup + dark/light lock fix (v1.4.0)
- Added `inc/button-styles.php`: 6 fixed, panel-defined button style slots (label + background/text/border colors), each generating a `.gnn-btn-style-N` class an editor applies via Gutenberg's or Elementor's "Additional CSS Class(es)" field. Modeled after a competing theme's ("Akansu") Button Styles screen, per the user's request — kept to 6 fixed slots (confirmed via AskUserQuestion) rather than a dynamic add/remove repeater, avoiding new JS infrastructure.
- Colors tab reorganized into grouped `<h3>` sections (Accent, Surfaces, Text & Borders, Top bar) with a new `gnn_field_color_pair()` helper rendering Dark/Light side by side per token — inspired by the same reference's grouped color screen, deliberately leaner (one shared token set + 2 modes, not per-section duplication). Exposes the theme's existing `--bg/--bg2/--bg3/--fg/--fg2/--line/--accent-ink` tokens for the first time; all empty by default (zero visual change until set). Adding `accent_ink` as an editable pair also fixes a latent bug: text-on-accent color was hardcoded regardless of a custom accent choice, risking poor contrast.
- Bug #37 fixed: "Show the dark/light toggle" only hid the toggle button, not a true lock — a visitor's stale localStorage value could still override the site's Default mode. `gnn_head_bootstrap()` now only reads localStorage while the toggle is actually visible.
- Verified end-to-end in WordPress Playground (`.claude/launch.json`'s `wp-playground` config): filled in a button style and a color pair through the real panel form, submitted, confirmed both persisted through `gnn_options_sanitize()` and rendered correctly in the front-end's inline `<style>` block (`.gnn-btn-style-1{...}`, `:root{--bg:...}[data-theme="light"]{--bg:...}`). Also fixed two unrelated local dev issues found along the way: MSYS/Git-Bash path-conversion was corrupting the Playground CLI's `--mount` argument (worked around with `MSYS_NO_PATHCONV=1`), and `.temp/blueprint.json` (gitignored, local-only) still pointed at the root-level demo XML removed earlier this session — repointed at `gnn/demo/gnn-demo-content.xml`.

## [2026-08-02] Typography Google Fonts system + CodeMirror (v1.3.11)
- Added `inc/typography.php`: an opt-in, per-element (H1-H6, paragraph) Google Fonts system exposed in GNN Panel → Typography. Off by default — self-hosted Space Grotesk/Manrope tokens (`--font-heading`/`--font-body`) are untouched and no extra request happens until enabled.
- When enabled, every configured family+weight is merged into ONE `fonts.googleapis.com/css2` request (never one per font), gated behind a shared preconnect (`gnn_google_fonts_preconnect()`, generalized from the existing Material Symbols preconnect to avoid duplicate `<link>` tags), with `display=swap`. Each role falls back to the theme default via CSS `var(--font-h1, var(--font-heading))` etc., so an unconfigured role costs nothing.
- No explicit "Latin Extended" toggle was added: Google's css2 endpoint already serves per-subset `@font-face` blocks gated by `unicode-range`, so the browser lazy-fetches the Turkish-glyph (ğ ı ş ö ü ç) subset automatically only when the page's actual text needs it — matching how the theme's own self-hosted fonts already work.
- Custom Code tab (custom_css / custom_js_head / custom_js_footer / head_html / body_html) now uses `wp_enqueue_code_editor()` / `wp.codeEditor` — WordPress core's own bundled CodeMirror, the same one behind Customizer → Additional CSS — for syntax highlighting. No third-party library added; gracefully falls back to a plain textarea if the site owner disabled the code editor in their WP profile.
- Dependency audit performed on request: confirmed the theme has no composer/npm dependencies, no bundled third-party JS libraries, and only three deliberately opt-in external calls (GitHub Releases API for updates, Material Symbols CDN, and now Typography's Google Fonts CDN) — all gated behind explicit user-enabled options, consistent with the "WordPress core + Elementor core" governance direction.

## [2026-08-02] Admin menu restructure (v1.3.12)
- "GNN Theme" and "GNN Slider" were two separate top-level wp-admin menu entries. Consolidated into one "GNN" top-level parent with "Theme" and "Slider" as its two submenus: `gnn_panel_menu()` now also calls `add_submenu_page()` on the same 'gnn-panel' slug to rename the auto-generated first submenu item, and the `gnn_slide` CPT's `show_in_menu` changed from `true` to the parent slug string `'gnn-panel'`.

## [2026-08-02] GNN product family menu-position registry (v1.3.13)
- Established a cross-product admin-menu-position convention for the whole GNN family (this theme plus sibling plugins like GNN SMTPMail, GNN Shortner): themes claim the `'58.xyz'`–`'59.xyz'` band (next to Appearance), plugins claim `'78.xyz'`–`'79.xyz'` (next to Settings), position values are always quoted strings (never bare floats, which can silently lose trailing zeros at parse time and collide). See [[0009-gnn-product-family-menu-position-registry]] and `.specs/constitution.md` rule 7.
- This theme's own `add_menu_page()` call updated to use its registry slot `'59'` as an explicit string (previously an unquoted int — functionally identical today, but now consistent with the documented convention for every future GNN product to follow).

## [2026-08-02] Menu-position registry format consistency (v1.3.14)
- Renamed GNN Tema's registry slot from the bare `'59'` to `'59.100'` so every entry in the registry (this theme's anchor slot included) shares the same 3-digit-suffix format — avoids a mix of bare-integer and decimal entries as more GNN products are added. Updated in code (`gnn_panel_menu()`), `.specs/constitution.md` rule 7, and ADR 0009.

## [2026-08-02] Fix wrong top-level menu landing page (v1.3.15)
- User-reported bug: clicking the top-level "GNN" menu link landed on the Slider list, not the Theme panel. Root cause: WordPress registers CPT submenus in `wp-admin/menu.php` before the `admin_menu` action fires, so the Slider CPT's submenu claimed array index 0 under `gnn-panel` ahead of our own `gnn_panel_menu()` callback, and WordPress uses that first submenu entry as the top-level link's target. Fixed with `gnn_panel_menu_reorder()`, a late (`priority 999`) `admin_menu` hook that force-sorts `$submenu['gnn-panel']` so the Theme entry is always first, independent of core's registration order. See bug #36.
