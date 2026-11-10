=== GNN ===
Contributors: bigdesigner
Tags: e-commerce, blog, one-column, two-columns, right-sidebar, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, full-width-template
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Configurable corporate theme with shared design controls, shared blocks,
role-based settings, draft starters and optional commerce integrations.

== Description ==

GNN is a classic (PHP-template) WordPress theme:

* Dark by default with a light mode — visitors toggle it in the header; their
  choice persists in the browser. Default mode is a Customizer setting.
* Hero slider on a static front page, managed in GNN → Slider: unlimited
  slides, images, title, copy and CTA. Rotation and sizing live in the panel.
* Accent color Customizer setting (suggested: #34d399, #22d3ee, #fb923c, #a78bfa).
* Mega menu: choose Menu layout in Appearance → Menus, or give a top-level
  menu item the CSS class `mega-menu-parent`
  (Appearance → Menus → Screen Options → CSS Classes). Its second-level items
  become columns; a second-level item with class `mega-featured` renders as a
  featured card (Title Attribute = kicker, Description = card text).
* WooCommerce: shop grid, product cards, cart, checkout and My Account are
  fully styled; header cart badge updates via AJAX.
* Elementor: theme locations registered; "Full Width" page template is
  canvas-friendly.
* Widget areas: Sidebar (archives + pages), Footer Columns 1–4.
* Menu locations: Primary, Mobile (falls back to Primary), Footer Columns 1–4.

== Setup after install ==

1. Appearance → Menus: create a menu, assign to "Primary Menu".
   Choose a mega-menu layout on the parent item; existing CSS classes also work.
2. Settings → Reading: set a static Front page and a Posts page ("Blog").
3. GNN → Theme: branding, colors, default mode and slider behavior.
   GNN → Slider: create and order the slides.
4. Appearance → Widgets: add Search / Categories / Recent Posts / Tag Cloud
   to "Sidebar" to reproduce the designed archive sidebar.
5. Optional: import demo/gnn-demo-content.xml from the theme package via
   Tools → Import → WordPress.
6. Add static front-page content with blocks or Elementor. Latest-posts mode
   uses the blog listing. Designed sections are available as block patterns
   and Elementor Saved Templates; no demo sections are inserted into pages automatically.

== Setup and shared content ==

GNN > Setup wizard offers Ready-made site and Start from scratch.
Ready-made sites use Elementor Free. Choose GNNpro, GNNcommerce, GNNdesign
or GNNarchitecture. Review dependencies, then import only that design's draft
pages. Existing content, homepage and menus are preserved. GNNcommerce needs
WooCommerce and its published shop/cart/checkout/account pages.

Start from scratch applies selected header/footer, fonts and accent settings
without creating sample content. GNN > Plugins provides installation/activation
links. GNN > Page templates lists imported pages and their preview/edit links.

Reusable blocks are single shared records edited with Elementor Free or native
blocks. Select them under Page and archive layouts, or use the GNN Shared Block
widget / [gnn_block id="123"]. Published, non-password-protected blocks render
where assigned; recursive references stop automatically. Used in lists direct
consumers and global assignments. Legacy usage indexing continues in batches.

Layout resolution is page override, specific content/archive context, then site.
Inherit, Theme default and Hide are distinct choices. Existing layout IDs and
native-block records are preserved. Collection geometry is configured in Elementor and module controls.

Design system includes responsive type roles, named color variables
--gnn-global-1 through --gnn-global-6, button states, form controls and additional
image sizes. Additional image sizes apply to future uploads; original files are
never deleted by a settings save.

GNN > Maintenance and recovery contains operational controls, settings snapshots
and Wipe. Wipe permanently deletes this installation's content, orders, uploaded
files, other accounts and plugin settings. It retains the initiating administrator,
login access, active theme and plugin/theme files; plugins are deactivated.
Back up the database and files separately before confirming. Reset runs in short
batches and can resume from the same administrator's session. Visitors receive
503 during an unfinished reset. Shared/multisite databases, custom user tables,
persistent cache drop-ins and remote/symlinked uploads are unsupported and
prevent reset. Local media folders inside wp-content are supported, excluding application directories. Core files and database connection configuration are preserved.

Optional GNN Client, GNN Designer and GNN Support roles can be assigned in Users.
Client users edit assigned public brand/contact fields. Designers also manage design
and reusable layouts. Support receives a masked diagnostic summary. Import, reset,
updates and operational settings remain administrator operations; restoring snapshots
with custom code also requires unfiltered_html. Roles are never assigned automatically.

A stale settings form cannot overwrite a newer configuration. Reload and reapply
edits after a conflict. The last ten local configuration snapshots can be restored;
this only restores theme settings, owned brand mods and the site icon, not pages,
orders or the full database. Use off-site database/files backups before upgrading.
Administrators can explicitly delete local recovery history when handing over a site.

Design-profile export excludes customer identity, selected media/page IDs, tracking
and executable custom code. Full site-configuration exports are private. Preview
imports first, resolve foreign references explicitly, then confirm. Imported options
merge with existing settings; this is not a full content/media/menu backup tool.

== Consent and optional integrations ==

Native blocks work without a page builder. Elementor Free handles content;
Elementor Pro theme locations are an optional fallback behind explicit native layouts.
The theme supports optional Contact Form 7, Polylang and Yoast SEO integrations.
Configure and test mail delivery, spam controls and translated content per site.
No form submission is sent or configured by a theme starter.

GA4/GTM identifiers alone do not load tags before consent. Configure your CMP to
provide WP Consent API statistics consent, call GNNConsent.setAnalyticsConsent(true
or false), or dispatch the gnn:consent document event with detail.analytics true/false.
Without an affirmative signal the theme leaves analytics disabled. When both IDs
are present GTM wins, preventing a second theme-managed GA loader. Tag/container
contents and third-party tracking plugins require their own consent configuration.
Choose external delivery when a separate integration owns tracking. Turning off the
theme loader does not remove tags installed outside the theme.

== Architecture starter ==

Choose GNNarchitecture under Setup wizard > Ready-made site and select Install
demo site. Delete and apply permanently removes existing pages, posts, projects,
albums, slides, menus, comments, orders and media files before installing the demo.
Active theme, plugin activation/settings and user accounts remain in place.
The job runs in short batches; Continue installation resumes an interrupted job.
Seven pages, three projects with galleries, an independent album, three slides,
three journal posts and a shared footer are published with bundled photography.
The homepage, blog, menus and design are applied. Other profiles currently offer
Add draft pages, which does not clear existing content.
Manage real projects and their categories under Portfolio projects. Published
projects appear automatically in portfolio/category queries and project navigation.
Project galleries reference Media Library images; independent collections live
under Gallery albums. Shared sliders remain under Slider. Edit imported page
layout in Elementor; edit the underlying projects/albums/slides in their own lists.
Existing native-block starter pages remain editable without conversion.

== Blog layouts ==

GNN > Theme settings > Blog separates listing layout, listing images, single-post details
and single featured images. For 1000px text with a 1200px image, set Single post
content width to 1000, choose Independent width and set image width to 1200.
Choose Full viewport width for an edge-to-edge image. Original proportions shows
the complete image; ratio and fixed-height modes allow cropping or contain fit.
The listing has independent grid/list, columns, image sizing and excerpt controls.
Post editor > Site layout overrides can override the single-post geometry, or
inherit site defaults. Widths shrink to the available mobile viewport. A builder's
own Single/Archive templates control their content geometry instead.

== Updating and client customizations ==

Install the parent theme normally; the optional gnn-child ZIP is the supported place
for client PHP/CSS overrides. Activate it after the parent. Existing child settings
are preserved, with missing parent brand and menu choices copied on first activation.
Do not edit the parent theme directly. The optional GNN Content Continuity plugin
keeps slides and reusable layout records editable after changing themes; it does not
make another theme render GNN layouts. Neither package is required for a normal install.

Test an upgrade on a staging copy with the same PHP, plugins and cache settings.
Back up both files and database, clear page/CDN caches after updating and verify
navigation, forms, consent, mobile views and orders where applicable. Roll back the
parent ZIP and restore the matching database backup if needed. Configuration snapshots
alone are not a theme/database rollback. PHP 7.4 is a legacy compatibility floor;
use a maintained PHP version for production.

== Fonts ==

Space Grotesk and Manrope are bundled as self-hosted variable WOFF2 files
(assets/fonts/), licensed under the SIL Open Font License 1.1. No external
font CDN is needed for these bundled fonts. Optional Material Icons and Google
Fonts typography settings can enable Google font requests.

== Distribution ==

Install the gnn-VERSION.zip asset from a GitHub release, or build gnn.zip locally.
This theme is distributed through GitHub/private ZIP, not WordPress.org.

== Changelog ==

= 2.6.2 =
* Split the primary menu around the centered logo, keeping submenus together and links aligned toward the logo.
* Fixed header layout selection being overridden by an imported site preset.

= 2.6.1 =
* Fixed centered header logo alignment and balanced split menus around optional header actions.

= 2.6.0 =
* Added selected-section imports into Elementor saved templates with visual catalog cards.
* Added responsive collection appearance controls, project ordering, exclusions and related projects.
* Added shared-block roles, compatible region choices and direct editing links.
* Added visual header selection and on-demand connection, media and editor checks in Maintenance.
* New Architecture imports use native grid controls where supported; edited existing pages are retained.

= 2.5.3 =
* Fixed authorized Elementor previews for shared blocks without rendering assigned regions twice.
* Removed saved grid layouts and Overview; moved System status into Maintenance and simplified menu labels/order.
* Improved contrast of the update action in the settings header.
* Removed duplicate in-content Architecture navigation; existing demo navigation is hidden without rewriting edited pages.
* Removed the visible slide rotation toggle while preserving slide navigation and focus/hover motion handling.
* Removed heading-level width restrictions from editable Architecture Elementor headings.
* Preserved starter text alignment and wrapping button-container layout during Elementor import.
* Corrected Architecture widget placement and nested image wrappers.

= 2.5.2 =
* Paced demo installation and reset requests to avoid request bursts.
* Added bounded demo retries with backoff and Retry-After support for temporary connection failures.

= 2.5.1 =
* Replaced the demo review flow with one explicit Delete and apply confirmation, content cleanup and resumable installation.
* Preserved active theme, plugins, plugin settings and user accounts during demo replacement.
* Removed the blanket must-use plugin restriction from WordPress reset and validated local custom media directories.

= 2.5.0 =
* Added an explicitly confirmed complete Architecture demo with published Elementor pages, portfolio projects, galleries, slides, journal posts and local images.
* Connected demo homepage, menus, blog and shared footer; repeated installation preserves customer edits and avoids duplicate records.
* Simplified administration navigation around the WordPress sidebar and aligned management page headers and setting rows across screen sizes.
* Improved accessible control labels and localized save notifications.

= 2.4.1 =
* Modernized administration panel layout with section dividers, two-column setting rows, visual diagram option cards, and dual range sliders.
* Updated panel styling with sticky header actions, deep-slate sidebar navigation, and toast notifications.

= 2.4.0 =
* Added setup paths, template catalog, plugin management and system status.
* Added shared Elementor blocks, layout assignments, usage tracking and grid layouts.
* Added responsive typography, global colors, button states and field controls.
* Added a confirmed, resumable WordPress reset with administrator and file protection.
* Simplified panel copy and preserved existing content and option identifiers.
* Fixed color-picker initialization inside collapsed setting groups.

= 2.3.2 =
* Rename the site workspace to Setup wizard and add a native WordPress Elementor install/activation shortcut.
* Add original local concept preview artwork for GNNpro, GNNcommerce and GNNdesign, matching the existing Architecture profile card.


= 2.3.1 =
* Cleanup explains protection reasons and empty inventories, prevents empty selection, and returns empty requests to the workspace with a notice.
* Reference checks paginate beyond 100 content records while preserving linked, edited, published and unverified pages.


= 2.3.0 =
* Four ready-made Elementor sites: GNNpro, GNNcommerce, GNNdesign and GNNarchitecture.
* Central project categories, automatic portfolio and project navigation, independent albums and project galleries; Free Elementor query widgets.
* Explicit presentation reset and reviewed cleanup of unchanged imported drafts; customer content and integrations preserved.
* Starter imports create only selected page documents; automatic legacy Elementor library population disabled.


= 2.2.0 =
* Add Architecture design profile and ten connected native-block draft pages for studios and construction businesses.
* Include a two-column portfolio with accessible category filters, a native gallery and four project-detail studies.
* Bundle original architectural illustrations and reusable Architecture block patterns with editor styling.
* Preserve existing pages, content edits, menu assignments and homepage settings during repeat installation.

= 2.1.0 =
* Add a dedicated Blog panel for independent listing, card image, single-post and featured-image settings.
* Support custom reading widths, wider featured images, full-viewport images and per-post geometry overrides.
* Offer natural proportions, ratios, fixed heights, crop alignment and uncropped responsive image sources.
* Add responsive listing columns, grid/list views, excerpts and independent post detail controls.
* Keep date and reading metadata visible when author information is disabled.
* Center header branding with equal side tracks regardless of menu/action widths.

= 2.0.4 =
* Render loading indicators before page content to avoid late overlays on streamed responses.
* Preserve already visible content when delayed scroll-animation scripts initialize.

= 2.0.3 =
* Replace compact color swatches with editable full-color HEX fields and anchored picker popovers.
* Keep inherited color values, native WordPress color selection and accessible text contrast.

= 2.0.2 =
* Organize theme settings into category navigation, section tabs and responsive setting cards.
* Add cross-section settings search, visible save status and persistent save controls.
* Unify the role workspace with the theme panel, including profiles, diagnostics and recovery controls.
* Preserve native WordPress form security, revision conflict checks and accessible keyboard navigation.

= 2.0.1 =
* Preserve WordPress/Elementor dependency order and consent styles under asset optimization.
* Refresh generated optimization files after upgrading without resetting user settings.

= 2.0.0 =
* Revision-protected settings, recovery snapshots and accessible administration.
* Shared design tokens, local typography, configurable layouts and reusable block regions.
* Design-only profiles, reference-safe configuration transfer and draft starter pages.
* Delegated client/design roles, masked diagnostics and consent-first analytics.
* Existing option keys, logo fallbacks and edited customer templates remain supported.


= 1.6.2 =
* Validate settings imports before writes; preserve partial imports and legacy logos.
* Fix menu badge saves, RTL assets, keyboard navigation and no-JavaScript content.
* Add reproducible WordPress coding, accessibility and performance verification.

= 1.6.1 =
* Fix the latest-posts homepage and preserve static front-page content.
* Preserve Elementor Saved Template IDs and user edits during library refreshes.
* Register GitHub update checks for cron; validate named release assets and the
  extracted theme before installation; preserve failures and clear stale updates.
* Preserve WooCommerce assets for product shortcodes, nested/reusable blocks,
  Elementor content and active product widgets.
* Make slider rotation keyboard-safe with explicit start/stop and inactive-slide
  focus isolation; respect reduced motion and RTL navigation.
* Reset shared brand settings with panel preferences and explain preserved options.
* Preserve CSS exactly during packaging and validate source/release versions.

= 1.6.0 =
* Fix (important): with "Title over featured image" on, the title could
  disappear completely — on every blog post (the overlay was only ever
  rendered by the page templates), and on any page or post with no
  featured image (nothing to overlay onto). The overlay now renders on
  single posts too, and switches itself off when there is no image to
  sit on, so the normal title takes over.
* Fix: "Logo max height — mobile" did nothing — the mobile size was
  hardcoded in CSS. It now applies (and can only shrink the logo
  relative to the desktop height, never enlarge it).
* Fix: the estimated reading time over-counted words in Turkish and any
  other non-ASCII language, inflating the "N min read" figure.
* Add layout width settings (GNN Panel → Pages Layout → Layout widths):
  sidebar column width, Boxed template max width, Full Width template
  max width.
* The block editor's "Accent" color swatch now follows the panel's
  accent color instead of staying on the shipped default.
* Theme updates: WordPress's "enable auto-updates" link now appears for
  the theme while it is up to date.
* Fix: pressing Escape in a sub-menu dropped keyboard focus out of the
  navigation entirely; focus now stays on the parent link.
* RTL: added the components introduced since the last RTL pass (mobile
  dock, top bar, sidebar page templates, title overlay, button styles).

= 1.5.1 =
* Fix: "Title over featured image" ignored the "Hide the page title"
  setting — a page with its title explicitly hidden could still show
  the overlay title if the theme-wide overlay setting was on. Hiding
  the title now always wins, everywhere.

= 1.5.0 =
* Add "Title over featured image" (GNN Panel → Pages Layout): the page
  title can render centered inside the featured image itself — with a
  configurable font size and a semi-transparent background box — instead
  of above the content. Off by default, theme-wide; any individual page
  can force it on or off from its own GNN Display Options box regardless
  of the theme-wide setting.

= 1.4.5 =
* Fix: on sidebar page templates (Left/Right Sidebar), the sidebar sat
  visibly higher than the main content — `.site-main`'s own fixed
  padding was stacking on top of the GNN Panel's configurable content
  top/bottom spacing, instead of that spacing being the only source of
  truth. Content and sidebar now always top-align, at any configured
  spacing value, with no hardcoded pixel amount needed.

= 1.4.4 =
* Color pickers: removed the redundant "Select Color" text label — the
  colored swatch button itself is the whole control now (still fully
  accessible to screen readers, just visually hidden).
* Fix: Button Styles' name field used WP core's `.regular-text` (25em),
  far wider than its 260px-minimum card, and overflowed. Narrowed to
  the card's own width without touching `.regular-text` anywhere else
  in the panel.
* Fix: the Button Styles preview showed a bare, unstyled gray button
  regardless of its configured colors — `main.css` (where the pill
  shape/padding/weight actually live) isn't loaded in wp-admin, so the
  preview needed its own copy of that base styling.

= 1.4.3 =
* Fix: color-picker swatch buttons showed color only at their left/right
  edges (white in the middle) — a fixed width/height CSS override was
  fighting WordPress core's own internal color-chip positioning. Equal
  sizing for the picker and "Clear" buttons is now done with `flex: 1`
  instead, without touching WP's own sizing math.

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

== Ready-made Elementor sites ==

Site workspace offers GNNpro (business and professional services), GNNcommerce (WooCommerce), GNNdesign (independent designers/developers) and GNNarchitecture (architecture/construction). Activate Elementor Free before importing; GNNcommerce additionally requires WooCommerce and its store setup. Imports create editable draft page documents, do not publish or assign the homepage/menus, and never create a second editor edition or an automatic saved-template library. Preview installed drafts before publishing. Existing Gutenberg content remains supported and is not converted.

Portfolio projects and categories are managed under GNN. Publish a project to add it to live portfolio queries; category selection and pagination are automatic. Single project pages show native title, featured image, facts, editable body, central gallery and category-based previous/next links. Use featured images, excerpts, order and native editor content to customize projects. Project categories are shared across profiles.

Gallery albums use ordered Media Library image collections. GNN Gallery can query a project category, one project or an independent album ID. Images are referenced by attachment ID, not copied. Only published, unprotected records appear in public collections. Slider remains centrally managed and its groups are selectable through GNN Slider widgets.

Elementor widgets GNN Portfolio, GNN Gallery, GNN Products and GNN Slider use live central data. Their query/source controls are native Elementor controls. Colors, typography and geometry inherit the theme panel tokens; widget settings allow local overrides. Replace illustrative images in the Image widget; image artwork is independent of interface color tokens.

When applying a profile, optionally reset presentation defaults first. This resets theme appearance options, preserves identity/logo/contact information, customer code, integrations and central content, and records a configuration recovery snapshot. It does not erase intentional local styles inside customer-edited pages. Cleanup has a separate review list: only baseline-verified unchanged draft pages can be selected, with explicit Trash or permanent deletion. Published/modified/referenced/legacy-unverified records are protected. Remove child drafts before an unchanged parent. A configuration snapshot is not a page/database backup. Visitor maintenance mode remains an independent operational control.
