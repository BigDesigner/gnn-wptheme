# ADR 0010: Native Option Storage and Theme Mod Compatibility

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Persistence
- **Supersedes**: None
- **Superseded By**: None

## Context

The native panel established by ADR 0002 manages many theme settings while the
Customizer still edits shared brand values. Both interfaces need to consume the
same persisted values. The constitution also requires compatibility with existing
theme mods. This record retrospectively documents the implemented storage model
at commit `0ac1688e937f07fe2ff637a8daf9e37bc510ca98`; it introduces no storage migration.

## Decision

Store general panel settings in the `gnn_options` WordPress option array. Define
defaults centrally in `gnn_option_defaults()` and read merged values through
`gnn_option()`, which caches that merged array for the current request. Sanitize
panel input through the Settings API callback `gnn_options_sanitize()`.

Keep the shared accent color, default appearance mode, and footer legal text in
`gnn_accent_color`, `gnn_default_theme`, and `gnn_footer_legal` theme mods. The panel
writes these same mods instead of maintaining competing copies. Keep the updater
enable switch in the separate `gnn_github_updates_enable` option. Panel logo IDs
currently live in `gnn_options`; the existing constitutional compatibility promise
for legacy logo theme mods remains a separate requirement, not a verified fallback.

Use native options, theme mods, and post metadata rather than introducing custom
tables or an external configuration framework for these settings.

## Consequences

- The panel and Customizer share the three brand values without synchronization jobs.
- Export/import must account for both `gnn_options` and the allowlisted theme mods.
- Resetting only `gnn_options` does not reset all visible theme settings. Audit A09
  identifies the resulting mismatch with the current UI promise.
- The static getter cache means writes after the first read are not automatically
  reflected by later `gnn_option()` calls during the same request.
- Storage changes must preserve existing keys and follow ADR 0006 version governance.
- This complements ADR 0002; it does not replace the native Settings API decision.

## Evidence

Implementation follow-up (1.6.1): A09 is resolved. Reset removes panel options and
the three shared brand mods while deliberately retaining the site icon, menu
assignments and independent updater preference; the UI now states that scope.

- [ADR 0002](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0002-pure-wp-settings-api-gnn-panel.md).
- [Defaults and getter](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/options.php:22).
- [Panel sanitizer and shared mod writes](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:103).
- [Customizer shared accent](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/customizer.php:45).
- [Export/import/reset](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:1158).
- [Independent updater option](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/updater.php:24).
- [Compatibility contract](C:/Users/bigde/.antigravity/gnn-wptheme/.specs/constitution.md:4).
