# ADR 0011: Shared Page Renderer and Title Visibility

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Structural Topology
- **Supersedes**: None
- **Superseded By**: None

## Context

The classic theme has full-width, boxed, left-sidebar, and right-sidebar page
layouts. Duplicating the page loop and title rules across those templates would
make spacing and visibility changes inconsistent. Featured-image title overlays
also need to honor both native page metadata and Elementor's hide-title setting.
This record documents the existing implementation at commit
`0ac1688e937f07fe2ff637a8daf9e37bc510ca98` and complements ADR 0001.

## Decision

Use thin page templates that delegate to `gnn_render_page($layout)`. The shared
renderer validates the layout identifier, draws the featured image above the
content/sidebar grid, controls sidebar eligibility, and renders page content and
comments through shared template parts.

Keep Blank Canvas as an explicit exception with its own document skeleton and
WordPress head/body/footer hooks. It renders content without theme header/footer
chrome. Front-page and blog/archive templates remain separate rendering paths.

Centralize title policy in `inc/page-meta.php`. Explicit native or Elementor title
hiding takes precedence. A title overlay requires a usable featured image and
respects the per-entry tri-state override before the global panel option. Both
page and post thumbnail renderers use `gnn_title_overlay_html()` so the overlay
markup and visibility decision are shared.

## Consequences

- Layout spacing and sidebar changes have one main page-rendering location.
- A valid overlay suppresses the ordinary title; missing/protected images must not
  suppress a title that cannot be drawn by the image renderer.
- Changes to the visibility policy affect page and single-post presentation.
- Blank Canvas, front-page, and archive behavior require their own verification;
  they do not inherit every rule from `gnn_render_page()`.
- Audit A01 remains an open defect in the separate front-page path. This ADR does
  not claim that the latest-posts homepage is fixed or that Elementor Pro locations
  are honored by every page renderer.
- No prior ADR is replaced; this records a concrete pattern within ADR 0001.

## Evidence

Implementation follow-up (1.6.1): A01 is resolved by explicitly delegating
latest-posts mode from front-page.php to home.php. Browser checks cover latest
posts, pagination, static content and the empty state on both tested WP versions.

- [ADR 0001](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0001-wordpress-classic-theme-architecture.md).
- [Default page entry point](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/page.php:11).
- [Shared renderer](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/page-layouts.php:45).
- [Blank Canvas exception](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/page-templates/tpl-blank.php:1).
- [Explicit hide policy](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/page-meta.php:174).
- [Overlay eligibility and markup](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/page-meta.php:196).
- [Post thumbnail consumer](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/template-tags.php:108).
