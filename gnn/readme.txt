=== GNN ===
Contributors: bigdesigner
Tags: e-commerce, blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, full-width-template
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dark-by-default corporate IT / cybersecurity theme with light mode, full-screen
hero slider, mega menu, WooCommerce support and Elementor compatibility.

== Description ==

GNN is a classic (PHP-template) WordPress theme:

* Dark by default with a light mode — visitors toggle it in the header; their
  choice persists in the browser. Default mode is a Customizer setting.
* Full-screen 3-slide hero slider on the front page (Customizer-managed:
  image, kicker, title, text, CTA per slide; autoplay toggle).
* Accent color Customizer setting (suggested: #34d399, #22d3ee, #fb923c, #a78bfa).
* Mega menu: give a top-level menu item the CSS class `mega-menu-parent`
  (Appearance → Menus → Screen Options → CSS Classes). Its second-level items
  become columns; a second-level item with class `mega-featured` renders as a
  featured card (Title Attribute = kicker, Description = card text).
* WooCommerce: shop grid, product cards, cart, checkout and My Account are
  fully styled; header cart badge updates via AJAX.
* Elementor: theme locations registered; "Full Width" page template is
  canvas-friendly.
* Widget areas: Sidebar (archives + pages), Footer Columns 1–3.
* Menu locations: Primary, Mobile (falls back to Primary), Footer Columns 1–3.

== Setup after install ==

1. Appearance → Menus: create a menu, assign to "Primary Menu".
   For the mega menu, add the `mega-menu-parent` class to a parent item.
2. Settings → Reading: set a static Front page and a Posts page ("Blog").
3. Appearance → Customize → GNN Theme Options: accent color, default mode,
   hero slides.
4. Appearance → Widgets: add Search / Categories / Recent Posts / Tag Cloud
   to "Sidebar" to reproduce the designed archive sidebar.
5. Optional: import the bundled demo content (gnn-demo-content.xml) via
   Tools → Import → WordPress.
6. The front page shows designed default sections until you give the front
   page its own content (e.g. with Elementor) — then your content replaces them.

== Fonts ==

Space Grotesk and Manrope are bundled as self-hosted variable WOFF2 files
(assets/fonts/), licensed under the SIL Open Font License 1.1. No external
font CDN is contacted.

== Changelog ==

= 1.3.2 =
* Fix: GNN Ana Sayfa sections now use Elementor's "boxed" row layout
  (content capped to the theme's 1280px content width, centered) instead
  of "full_width" — backgrounds still bleed edge-to-edge, but text/columns
  no longer stretch across the full browser width on wide screens.

= 1.3.1 =
* Sub-brand tag styling matched to the exact reference design: solid
  black pill (no border), SF Pro Display, 24px, neutral gray dot/suffix
  instead of per-section accent colors.

= 1.3.0 =
* Rebuilt "GNN Ana Sayfa" template: full zigzag layout showcasing all 5
  GNN sub-brands (Creative, Cyber, Logix, Labs, Advisory), each with its
  own accent color, brand tag and a bespoke visual mockup (design system
  preview, SIEM/EDR terminal, vulnerability report card). Refreshed hero
  and closing CTA copy to match the ecosystem positioning.

= 1.2.3 =
* Fix: the Elementor template auto-heal now also detects when templates
  were deleted manually (not just a version mismatch) — the "installed"
  flag alone survived post deletion, so a manually-cleared template
  library previously stayed empty until the manual "Rebuild" click.

= 1.2.2 =
* Fix: the Elementor template library now re-syncs automatically whenever
  the theme is updated (previously only ran on first-ever install), so
  templates added in an update no longer require a manual "Rebuild"
  click in GNN Panel → Advanced.

= 1.2.1 =
* Add "GNN Ana Sayfa" Elementor Saved Template — a full multi-section home
  page design (hero, stats, sub-brand tags, services, differentiators, CTA).
* Add .gnn-subbrand-tag utility component to main.css.

= 1.2.0 =
* Featured image height, fit (cover/contain/fill) and alignment
  (top/center/bottom) are now configurable per content type — separate
  settings for pages and blog posts (GNN Panel → Pages Layout).

= 1.1.0 =
* GitHub-based automatic theme updates (GNN Panel → Advanced).
* Google Material Symbols icon integration (GNN Panel → Icons).
* Configurable content top/bottom spacing (GNN Panel → Pages Layout).
* Featured image now displays full-width above the sidebar/content area.
* Elementor "Hide Title" setting now syncs with the theme's own toggle.
* Removed the Contact page template (use the default template + per-page
  breadcrumb toggle instead).
* Modernized the GNN Theme Panel admin UI.

= 1.0.0 =
* Initial release.
