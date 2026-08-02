# Task Pipeline & Roadmap

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
