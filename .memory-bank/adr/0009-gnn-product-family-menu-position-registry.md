# 9. GNN Product Family Admin Menu Position Registry

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
GNN is a growing family of separately-developed WordPress themes and plugins
(this theme, plus sibling plugins such as GNN SMTPMail and GNN Shortner,
with more planned). Each product registers its own `wp-admin` menu
independently. Left to arbitrary `add_menu_page()` position choices, GNN
products end up scattered across the sidebar instead of forming a
recognizable, predictable group — confirmed directly on a live site
(screenshot showing "GNN Tema" and "GNN Slider" landing in unrelated parts
of the menu relative to each other and to other GNN products).

Two additional constraints shaped the design:
1. Users conceptually look for **theme** settings near Appearance
   (Görünüm) and **plugin** settings near Settings (Ayarlar) — one shared
   position band for both types would fight that expectation.
2. `add_menu_page()` keys WordPress's internal `$menu` array off
   `"$position"` (string interpolation). A bare PHP float literal with a
   trailing zero, e.g. `58.010`, is parsed as `58.01` before it ever
   reaches that interpolation — so two developers who each believe they
   picked a distinct slot can silently collide, and WordPress does not
   reliably protect against it (older core simply overwrites one of the
   two entries).

## Decision
- Two position bands, chosen by product type, each parked next to the core
  menu item users already associate with that product type:
  - **Themes** → `'58.xyz'`–`'59.xyz'` (next to Appearance/Görünüm, since
    `59` sits in WordPress core's own reserved blank separator slot right
    before Appearance at `60`).
  - **Plugins** → `'78.xyz'`–`'79.xyz'` (next to Settings/Ayarlar; Tools
    sits at `75` and Settings at `80`, leaving this range free of any core
    item).
- Every position is written as a **quoted string literal** in source
  (`'58.101'`), never a bare number, specifically to sidestep the
  float-truncation collision described above.
- Every slot — including the first/anchor product in a band — uses the
  same 3-digit-suffix format (`'59.100'`, not a bare `'59'`), so the
  registry table stays visually uniform as more products are added rather
  than mixing bare-integer and decimal entries.
- A product with more than one admin screen (like this theme's Theme panel
  + Slider CPT) registers exactly ONE top-level menu and nests every other
  screen under it via `add_submenu_page()` — never multiple top-level
  entries for one product. See `gnn_panel_menu()` in
  `gnn/inc/admin-panel.php`, which nests `gnn_slide` this way instead of
  letting the CPT register its own top-level menu.
- This registry is the single source of truth for which 3-digit slot is
  taken. Check it before assigning a new position; add a row here (and to
  `.specs/constitution.md` rule 7 if the band itself changes) for every new
  GNN product.

### Slot registry

| Position | Product | Type | Repo |
|---|---|---|---|
| `'59.100'` | GNN Tema (Theme panel + Slider submenu) | Theme | this repo (`gnn-wptheme`) |
| `'58.101'` | *(next theme product — unassigned)* | Theme | — |
| `'79.101'` | GNN SMTPMail *(planned)* | Plugin | — |
| `'79.102'` | GNN Shortner *(planned)* | Plugin | — |

## Consequences
- Every GNN product visually clusters next to the core menu section its
  users would already look under, instead of scattering across the
  sidebar based on load order or unrelated plugins' own position choices.
- Zero coordination code is required between products (no shared parent
  menu to register defensively) — each product stays a fully independent
  codebase, only sharing a written numbering convention.
- The one ongoing cost: this table must be kept up to date by hand as new
  GNN products are built. There's no automated enforcement — a future
  product that skips checking this ADR could still pick a colliding slot.
  Mitigated by making rule 7 in `.specs/constitution.md` an explicit
  checklist item for any new GNN admin menu registration.
