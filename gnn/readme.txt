=== GNN ===
Contributors: bigdesigner
Tags: e-commerce, blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, full-width-template
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.4.2
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

= 1.4.2 =
* GNN Panel restructured: Icons moved into the Typography tab, and Button
  Styles merged into the Colors tab (renamed "Styles") — fewer tabs,
  related settings grouped together.
* Fix: empty color fields showed blank instead of the theme's actual
  current color. Every color field now pre-fills with the theme's own
  default (or the effective inherited value) so the panel always shows
  what the site currently looks like; a "Clear" button reverts explicitly
  set colors back to that default.
* Add a separate Light-mode accent color (previously the accent was
  always identical in both modes) — Light automatically matches Dark
  until you set your own value.
* Fix: color-picker "Clear"/swatch buttons could overflow narrow Button
  Style cards; picker buttons now match the panel's monochrome style
  instead of WordPress core's default blue border/focus ring.
* "Default mode" (Dark/Light) now renders as two equal-width selectable
  cards instead of plain radio buttons.

= 1.4.1 =
* Color fields in GNN Panel (Colors + Button Styles tabs) now use
  WordPress core's own color picker (the same one behind Customizer
  color controls) instead of the browser's native OS color dialog —
  a collapsed swatch button that opens a proper palette/hex popover
  on click.

= 1.4.0 =
* Add Button Styles (GNN Panel → Button Styles): define up to 6 named
  button variants (background, text and border color), each generating a
  `.gnn-btn-style-N` class to apply from Gutenberg's or Elementor's
  "Additional CSS Class(es)" field. Unused slots cost nothing.
* Colors tab reorganized into grouped sections (Accent, Surfaces, Text &
  Borders, Top bar), each color now settable independently for Dark and
  Light mode — including a new "Text on accent" pair, fixing a latent
  contrast issue where button text color on the accent background was
  hardcoded regardless of a custom accent choice.
* Fix: "Show the dark/light toggle" only hid the toggle button — a
  visitor's stale remembered choice (localStorage) could still override
  the site's Default mode. Turning the toggle off now truly locks the
  whole site to a single style for every visitor.

= 1.3.15 =
* Fix: clicking the top-level "GNN" menu link landed on the Slider list
  instead of the Theme panel. WordPress registers the Slider CPT's
  submenu before our own menu callback runs, so it was claiming the slot
  WordPress uses as the top-level link's target. The Theme entry is now
  force-ordered first, regardless of registration order.

= 1.3.14 =
* Governance: GNN Tema's admin-menu-position registry slot renamed from
  the bare `59` to `59.100` so every product in the shared GNN family
  registry (see readme's constitution docs) uses the same 3-digit-suffix
  format. No visible change to the menu itself.

= 1.3.13 =
* Governance: established a shared admin-menu-position convention across
  the whole GNN product family (this theme plus sibling GNN plugins) so
  every GNN product clusters near the WordPress core menu its users
  already expect (themes near Appearance, plugins near Settings) instead
  of scattering across the sidebar. No visible change to this theme's
  own menu — it already sat in its correct reserved slot.

= 1.3.12 =
* Admin menu restructured: "GNN Theme" and "GNN Slider" used to be two
  separate top-level menu items. They're now one "GNN" parent with two
  submenus, "Theme" and "Slider".

= 1.3.11 =
* Add an optional Google Fonts typography system (GNN Panel → Typography):
  assign a font family and weight to each of H1-H6 and paragraph text
  independently. Off by default — the theme keeps using its self-hosted
  Space Grotesk / Manrope fonts with zero extra requests until enabled.
  When enabled, every configured font is merged into a single stylesheet
  request (never one per font), with a shared preconnect and font-display:
  swap; Turkish characters (ğ ı ş ö ü ç) are fetched automatically by
  Google Fonts/the browser only when the page needs them, no extra
  setting required.
* Custom Code tab: the CSS / JS / HTML fields now use WordPress core's
  own CodeMirror code editor (the same one behind Customizer → Additional
  CSS) for syntax highlighting — no third-party library added.

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
