# Task Pipeline & Roadmap

## Verified release state — 2026-10-24

- [x] `9140a32`: 1.6.2 non-WooCommerce hardening, committed and pushed.
- [x] `fda9916`: Linux PHPCS and WooCommerce login-redirect test corrections, pushed.
- [x] [Verify theme](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40045204839): 119/119 integration/browser and 90/90 static/package checks.
- [x] [Manual Release](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40046157813): completed successfully; GitHub `v1.6.2` is published.
- [x] FAZ 1.34.2 live-site acceptance remains user-confirmed.

Only deferred user-site WooCommerce visual/checkout acceptance remains. Automated
WooCommerce CI passed; it does not replace that site acceptance. No production
installation of 1.6.2 is inferred from GitHub publication.

## Completed: Non-WooCommerce Follow-up — 1.6.2 (2026-10-24)

User authorized completing all six follow-up scopes. Evidence:
`.memory-bank/audits/remediation-1.6.2.md` and `theme-hardening-plan.md`.

- [x] Panel/handler security and bounded JSON validation before writes.
- [x] Real HTTP export/import, malformed/partial files and capability boundaries.
- [x] Legacy logo IDs/URLs, panel re-save, single-logo modes and explicit removal.
- [x] Composer-locked WPCS: zero errors/warnings; CI gate configured.
- [x] Local performance budgets and dark/light/mobile/RTL axe checks.
- [x] Turkish catalog, printf placeholders, RTL loading and keyboard navigation.
- [x] WordPress 7.1.3/PHP 8.2.33 and 6.0.17/PHP 7.4.33: 89/89 each, no WooCommerce.
- [x] Theme 1.6.2 package, synchronized metadata and 90/90 static checks.

Only the user-site WooCommerce visual/checkout acceptance remains in the active
backlog. Older PRD checklists are historical reference, as stated in those files.

## Completed: FAZ Cookie Manager Incident (2026-10-19)

- [x] Reproduce the loading freeze using the public homepage HTML.
- [x] Isolate the FAZ runtime and confirm official 1.34.2 fixes its insertion loop.
- [x] User enabled the update and confirmed live-site acceptance.

Evidence: `.memory-bank/audits/live-loading-2026-10-18.md`.
Production acceptance is user-confirmed. WooCommerce remains last.
The subsequent theme follow-up scopes are completed above in 1.6.2.

## Post-release Acceptance and Next Candidates (2026-10-18)

- [x] User confirmed the real update to 1.6.1 completed without problems.
- [x] GitHub verification and manual release workflows completed successfully.
- [ ] WooCommerce visual/checkout acceptance: deferred until the end at the user's
  request; WooCommerce is not installed on their current site.

The original audit backlog and all six subsequently authorized non-WooCommerce
follow-up scopes are complete. See the 1.6.2 evidence above.


## Completed: Audit Remediation (2026-10-18)

User authorized completing A01–A10. The active plan is
`audit-remediation-plan.md`; baseline evidence is under `audits/2026-10-18/`.

- [x] A01: Latest-posts homepage.
- [x] A02: Preserve Elementor library IDs and user edits.
- [x] A03–A05: Cron-safe updater, error handling and validated release assets.
- [x] A08: Source/release version consistency.
- [x] A06: WooCommerce asset detection.
- [x] A07: Accessible slider rotation and focus handling.
- [x] A09: Explicit reset scope.
- [x] A10: Lossless CSS packaging.
- [x] Verification, version 1.6.1, translations and normal ZIP.

Distribution: GitHub/private ZIP. WordPress.org publishing and the stale
`gnn-wporg.zip` are excluded by explicit user instruction.


## Completed Features (PRD Phase 1: Items 1-19)
- [x] **Item 1:** Logo max height controls (Desktop & Mobile).
- [x] **Item 2:** Header & Footer menu alignment (Left / Right).
- [x] **Item 3:** Centered header layout & split menu (`header-left`, `header-right`).
- [x] **Item 4:** Dynamic Footer Brand/Logo area (Text, Image, Both, None).
- [x] **Item 5:** Header search button icon transformation (Magnifying glass SVG).
- [x] **Item 6:** Fully dynamic styling & hardcoded text cleanup.
- [x] **Item 7:** Scroll-in entrance animations (`scroll_anim`).
- [x] **Item 8:** CPT-based Hero Slider (`gnn_slide` CPT & full-height mode).
- [x] **Item 9:** Device visibility utilities (`.hide-on-mobile`, `.hide-on-desktop`, etc.).
- [x] **Item 10:** 9-Tab GNN Admin Panel with Export/Import JSON & Reset settings.
- [x] **Item 11:** Mobile bottom app dock (`mobile_dock`).
- [x] **Item 12:** Top announcement bar (`topbar_enable`).
- [x] **Item 13:** Scroll-to-top button (`scroll_top`).
- [x] **Item 14:** Unified badges for nav menus & WooCommerce (`.gnn-badge`).
- [x] **Item 15:** Built-in Maintenance / Coming Soon mode (`maintenance_mode`).
- [x] **Item 16:** Custom 404 page options & management.
- [x] **Item 17:** Preloader bar & full-screen loading overlay.
- [x] **Item 18:** Smooth scroll for anchor links.
- [x] **Item 19:** Elementor Builder & Canvas/Full-width compatibility.

---

## Completed Features (PRD Phase 2: Implementation Plan in `.tasks/IMPLEMENTATION-PLAN.md`)
- [x] **Task 1:** Remove Contact Page Template (`page-templates/page-contact.php`).
- [x] **Task 2:** Synchronize Breadcrumb & Title Hide options with Elementor page settings.
- [x] **Task 3:** Featured Image 0px header attachment, 250px max-height, Full-width / Boxed layout, & top placement above sidebar & content.
- [x] **Task 4:** Add `content_top_padding` option (default: 50px) to GNN Theme Panel.
- [x] **Task 5:** Add `content_bottom_padding` option (default: 64px) to GNN Theme Panel.
- [x] **Task 6:** Integrate Google Material Symbols & Icons (`fonts.google.com/icons`) into GNN Theme Panel & Frontend.

---

## Completed Features (PRD Phase 3: post-launch hardening, v1.1.0 - v1.3.10)
- [x] GitHub-based theme auto-updater (`class-gnn-github-updater.php`), surfaced in GNN Panel → Advanced with a single dynamic check/update button in the panel header.
- [x] Modernized GNN Panel admin UI (card-based header/stats/pill-tabs, hex-input color fields, on/off toggle switches instead of checkboxes, monochrome black/white palette).
- [x] Fixed third-party admin notices (e.g. cache plugins) rendering inside the panel's title card by adding the standard `wp-header-end` marker.
- [x] Contact page picker (GNN Panel → Pages Layout) driving the mobile dock's Contact link.
- [x] Elementor "Hide Breadcrumb" native Page Settings control (parallel to Hide Title).
- [x] Elementor CSS Print Method defaults to "Internal Embedding" (self-healing on activation + admin_init) to avoid a hosting trap where External File silently drops backgrounds/icons.
- [x] 14 Elementor Free Saved Templates mirroring the Gutenberg patterns, plus a full "GNN Ana Sayfa" multi-section home page template.
- [x] GitHub Actions `release.yml` for automated GitHub Releases via `workflow_dispatch`.
- [x] Mobile bottom dock: real SVG icons, centered layout with breathing room (avoids third-party floating widgets), scroll-to-top offset fix.
- [x] GNN Slider admin screen documents that the slider only renders on the Gutenberg front page, not under Elementor.
- [x] Semantic versioning governance policy (`.specs/constitution.md`): never bump MAJOR by default, version synced across style.css/functions.php/readme.txt/memory-bank on every behavior-changing commit.
