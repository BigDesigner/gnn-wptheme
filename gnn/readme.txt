=== GNN ===
Contributors: bigdesigner
Tags: e-commerce, blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, full-width-template
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
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
