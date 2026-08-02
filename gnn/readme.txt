=== GNN ===
Contributors: bigdesigner
Tags: e-commerce, blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, full-width-template
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.10
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

= 1.3.10 =
* GNN Panel: replaced the purple/indigo accent with a monochrome black/white
  palette, and every checkbox setting is now an on/off toggle switch.
* Fix: third-party admin notices (e.g. cache-clearing plugins) were rendering
  inside the GNN Panel's title card instead of below it, because the panel
  was missing the standard `wp-header-end` marker WordPress core uses to
  relocate them.
* GNN Panel: the GitHub update check + "Update now" actions are now a single
  dynamic button under the version number at the top of the panel — it
  reads "Check for updates" normally and becomes "Update to X.X.X" once a
  release is available. The Advanced tab keeps only the enable/disable
  setting.
* GNN Slider admin screen now notes that the slider only renders on the
  Gutenberg front page template, not when Elementor replaces the front
  page content.
* Docs: brought internal task/bug tracking up to date and removed an unused
  duplicate copy of the bundled demo content file.

= 1.3.9 =
* Fix: the mobile dock's theme-toggle button shared the header's round
  icon-button styling (fixed 34x34px, border-radius, border, background)
  via the .theme-toggle class, sitting visibly higher/smaller than its
  plain-link siblings — reset inside the dock so all 4 items match.
* The dock's 4 items are now centered as a group with breathing room on
  both sides instead of stretching edge-to-edge, so they no longer sit
  flush in the bottom corners where third-party floating widgets
  (cookie-consent icons, chat bubbles) conventionally live.

= 1.3.8 =
* Fix: the mobile dock's sun icon had much less visual weight than its
  siblings (a thin 4px-radius outline vs. bold shapes elsewhere) — its
  core is now solid-filled at 5px radius to match.
* Add a "Contact page" picker (GNN Panel → Pages Layout) so the mobile
  dock's Contact link (and any future "get in touch" link) can point at
  any page instead of a hardcoded /contact/ guess. Auto-detects a page
  slugged "contact" or "iletisim" when left on default.

= 1.3.7 =
* Default Elementor to printing CSS inline ("Internal Embedding") instead
  of to an external file, on both theme activation and admin_init (heals
  existing installs too). Many hosts restrict writes to
  wp-content/uploads/elementor/css/, which silently drops backgrounds,
  icons and borders while widget text content keeps rendering — this
  sidesteps that entire class of hosting issue by default.

= 1.3.6 =
* Add a "Hide Breadcrumb" toggle to Elementor's own Page Settings panel
  (new "GNN Theme" section, next to the native Hide Title control), so
  breadcrumb visibility can be set without leaving the Elementor editor —
  matching how Hide Title already syncs. gnn_hide_breadcrumb() now reads
  it from `_elementor_page_settings` the same way gnn_hide_title() does.

= 1.3.5 =
* Fix: mobile bottom dock used wrong/mismatched Unicode glyphs for its
  icons (a random symbol for Search, a cart emoji even for the "Contact"
  fallback item). Replaced with proper outline SVG icons matching the
  header's own icon style — home, search, sun/moon toggle, cart or mail.
* On mobile, the header's search and dark/light toggle buttons are now
  hidden when the bottom dock is active (the dock already has both),
  removing the duplicate controls.
* Fix: the scroll-to-top button was hidden behind the fixed mobile dock
  bar — it now sits above it.

= 1.3.4 =
* Fix: the stat strip and closing CTA heading now render as plain
  centered HTML (inline styles) instead of Elementor's heading widget,
  so centering can never lose a CSS specificity fight with any theme
  style applied inside .entry-content, regardless of the page/sidebar
  context the template is embedded in.

= 1.3.3 =
* Fix: GNN Ana Sayfa's zigzag sections had no gap between their two
  columns (the shared section helper defaults to "no gap", intended for
  edge-to-edge image/text patterns elsewhere, but never overridden here).
  All 7 sections now use Elementor's "wide" column gap.

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
