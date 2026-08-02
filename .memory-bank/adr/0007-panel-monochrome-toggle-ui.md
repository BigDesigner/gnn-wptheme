# 7. GNN Panel Monochrome Toggle-Switch UI

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
The GNN Panel ([[0002-pure-wp-settings-api-gnn-panel]]) was modernized into a
card-based layout using an indigo/purple accent color and native browser
checkboxes for its ~30 boolean settings. The site owner found the purple accent
visually inconsistent with the panel's otherwise clean look, and native checkboxes
read as dated compared to the on/off switches used throughout the rest of the
panel's design language (e.g. the dark/light mode toggle on the front end).

## Decision
- Replace the panel's single CSS custom property `--gnn-admin-accent` with black
  (`#000`), making every accent-derived surface (active tab, focus rings, primary
  buttons, icon backgrounds, the version badge) monochrome by construction — no
  color values needed changing anywhere else, since the whole panel already
  referenced this one variable.
- Replace every boolean `<input type="checkbox">` field (`gnn_field_checkbox()`,
  used by all ~30 toggle settings, plus the GitHub-updates enable checkbox) with a
  sliding switch built from the same underlying checkbox input, visually hidden
  and styled via a `:checked` sibling selector — so form submission, sanitization,
  and `checked()` logic in PHP are completely unchanged; only the visual layer
  differs.

## Consequences
- Zero backend/sanitization changes were needed — the switch is a pure CSS/markup
  wrapper around the existing native checkbox, so `gnn_options_sanitize()`'s
  checkbox-handling loop keeps working unmodified.
- The panel's entire color story now reduces to one variable; a future accent
  color change (if ever wanted) is a one-line edit again.
- Radio-button fields (e.g. "Default mode": Dark/Light) were deliberately left as
  radios rather than converted to switches — they represent a mutually exclusive
  choice between two named options, not a single boolean, so a switch would blur
  that distinction.
