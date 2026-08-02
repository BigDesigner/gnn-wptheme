# 5. Default Elementor to Internal CSS Embedding

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
Elementor's default "CSS Print Method" is "External File": generated CSS is written
to `wp-content/uploads/elementor/css/`. On hosts that restrict writes to that path
(a common shared-hosting/staging restriction), the write silently fails — widget
text content still renders (it's plain HTML), but backgrounds, icons, borders, and
any other CSS-dependent styling disappear with no visible error, making the failure
very hard for a site owner to diagnose. This was confirmed live: switching a real
site to "Internal Embedding" (CSS printed inline in `<head>`) fixed the symptom
immediately.

## Decision
On both `after_switch_theme` and `admin_init`, if the Elementor option
`elementor_css_print_method` is unset, set it to `'internal'`. Hooking both events
means new installs get the safe default immediately, and existing installs that
already hit the hosting trap self-heal the next time an admin loads any wp-admin
page — no manual settings dive required.

## Consequences
- Removes an entire class of "my Elementor page looks broken" support burden caused
  by host filesystem restrictions, by default, for every GNN install.
- Internal embedding has a minor tradeoff (CSS is inlined per-page instead of
  cached as a static file), acceptable given GNN's performance work already keeps
  other assets lean.
- Site owners can still switch back to "External File" manually in Elementor →
  Settings → Advanced if their host is known-writable and they prefer that tradeoff;
  the theme only sets the option when it is unset, never overriding an explicit
  choice.
