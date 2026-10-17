# ADR 0013: Capability Boundary for Custom Code Settings

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Auth
- **Supersedes**: None
- **Superseded By**: None

## Context

The theme panel accepts both ordinary presentation settings and deliberately
executable custom JavaScript/HTML. Applying ordinary text sanitization to scripts
would destroy their meaning, while accepting raw code from all panel users would
weaken the trust boundary. This record documents the implemented boundary at
commit `0ac1688e937f07fe2ff637a8daf9e37bc510ca98`; it is not a full security review.

## Decision

Require `manage_options` for the theme panel and its administrative action
handlers. Use the native Settings API save path for general options and
action-specific nonce checks for custom import/export/reset/update handlers.

Additionally require `unfiltered_html` when changing `custom_js_head`,
`custom_js_footer`, `head_html`, or `body_html` in `gnn_options_sanitize()`. Preserve
the previously stored value and emit a settings error when that capability is
absent. The options import path also invokes this sanitizer.

Treat accepted custom script/HTML fields as trusted operator code and render them
at their designated head/body/footer hooks without ordinary HTML text escaping.
Keep other fields on their existing context-specific sanitization paths: strip
HTML tags from custom CSS, use text sanitizers for plain text, and use
`wp_kses_post()` for permitted rich text.

## Consequences

- Panel access alone is insufficient to change executable custom code fields.
- Multiline script/HTML content retains its structure instead of being passed
  through a single-line text sanitizer.
- Output safety for these explicit raw-code sinks depends on controlling writes.
  Direct database writes or another plugin bypassing this sanitizer are outside
  this function's protection.
- Import, future REST endpoints, and additional save paths must preserve the same
  capability boundary rather than storing raw fields independently.
- Operator code may affect all visitors; these fields are intentionally privileged.
- Verification is based on the source paths, not live role/multisite integration
  tests. Existing escaping rules in the boundary specification remain applicable
  to ordinary output; this documents the narrow trusted-code exception.
- This complements ADR 0002 and ADR 0010; neither predecessor is superseded.

## Evidence

- [Panel capability](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:26).
- [Settings registration](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:84).
- [Custom-code sanitizer boundary](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:259).
- [Import capability/nonce and sanitizer](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:1200).
- [Head output](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/options.php:454).
- [Body and footer output](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/options.php:483).
- [Security contract](C:/Users/bigde/.antigravity/gnn-wptheme/.specs/boundary-conditions.md).
