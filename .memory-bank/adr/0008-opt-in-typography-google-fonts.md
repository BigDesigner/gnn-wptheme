# 8. Opt-In Per-Element Google Fonts Typography System

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
The theme ships self-hosted variable WOFF2 fonts (Space Grotesk for headings,
Manrope for body) specifically to avoid an external font-CDN dependency and its
performance cost. The site owner asked for a complete Google Fonts integration
controllable per element (H1-H6, paragraph) from the panel, explicitly on the
condition that it must not compromise the theme's fast-loading default.

## Decision
- The feature (`typography_google_enable`) is off by default. With it off, no
  code path changes: the existing `--font-heading` / `--font-body` CSS custom
  properties and self-hosted `@font-face` rules are the only thing in play.
- Font selection is a **freeform text field** per role (H1-H6, paragraph) —
  the admin types the exact Google Fonts family name — rather than a bundled
  dropdown catalog of hundreds of fonts. This avoids shipping and maintaining
  a large embedded font list (and the payload/complexity that implies) while
  still covering every Google Font that exists.
- Every family+weight actually configured across all 7 roles is merged into
  **one** combined `fonts.googleapis.com/css2` request (not one request per
  font), behind a shared preconnect hook generalized from the existing
  Material Symbols preconnect ([[0004-github-based-self-updater]] sibling
  pattern) so the two features never emit duplicate `<link rel=preconnect>`
  tags. `display=swap` is always applied.
- Each role's CSS is `var(--font-h1, var(--font-heading))` (and `--font-p`
  falling back to `--font-body` for paragraph/body text) — an unconfigured
  role costs nothing and looks identical to today.
- No explicit "Latin / Latin Extended" subset toggle was implemented, even
  though it was requested, because it would not have controlled anything
  real: Google's css2 endpoint already returns per-subset `@font-face` rules
  gated by `unicode-range`, and browsers already fetch only the subset a
  page's actual text needs (exactly how the theme's own self-hosted fonts
  already behave). Turkish glyphs (ğ ı ş ö ü ç) load automatically without
  any admin action.

## Consequences
- Zero performance regression for every install that leaves the feature off
  (the default) — this was the explicit, repeated constraint from the site
  owner.
- When turned on, worst case is exactly one extra stylesheet request
  regardless of how many of the 7 roles are configured, plus the fonts'
  own woff2 downloads (which the browser would need regardless of how the
  font is delivered).
- The freeform text input trades discoverability (no visual font browser)
  for zero maintenance burden and zero payload cost; the panel's hint text
  points the admin to fonts.google.com to find exact names.
- A follow-up mismatch risk: a mistyped or non-existent family name silently
  falls back to the browser's default sans-serif for that role (Google's API
  ignores unknown family names rather than erroring) — acceptable given the
  alternative (validating against a live API call or a bundled catalog) would
  reintroduce the complexity/dependency this design deliberately avoided.
