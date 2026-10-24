# Security & Boundary Conditions

## Security Rules & Escaping Protocol
1. **Output Escaping:**
   - Text output MUST use `esc_html()`.
   - HTML attributes MUST use `esc_attr()`.
   - URLs MUST use `esc_url()`.
   - Rich text / HTML fields MUST use `wp_kses_post()` or `wp_kses()`.
2. **Input Sanitization:**
   - Text fields MUST be sanitized via `sanitize_text_field()`.
   - Multi-line text / HTML fields MUST be sanitized via `wp_kses_post()` or `sanitize_textarea_field()`.
   - Numeric option inputs MUST be sanitized via `absint()` or clamped integer filters.
3. **No Direct Database Queries:**
   - Standard WP APIs (`get_option()`, `get_post_meta()`, `WP_Query`) MUST be used. No raw SQL or `$wpdb->query` without parameter binding.
4. **Nonces & Nonce Checks:**
   - All metaboxes and form submissions MUST verify nonces (`check_admin_referer()` / `wp_verify_nonce()`).
5. **No Main Loop Blocking:**
   - No heavy operations or external HTTP requests on synchronous `wp_head` or `wp_footer` hooks.
6. **Generated Content Preservation:**
   - Library refreshes MUST preserve stable template IDs, user edits and trash decisions.
   - Unknown legacy baselines MUST NOT be overwritten. Failed template post insert/update operations MUST NOT mark a migration complete.
7. **Update Package Boundary:**
   - Offer only the configured GitHub repository's exact versioned theme release asset.
   - Repository source archives are not installable theme packages and MUST NOT be fallback updates.
   - Validate extracted source before replacing installed files; preserve incoming errors and unrelated upgrades.
8. **Packaging Semantics:**
   - Packaged CSS MUST be byte-identical to source; regex whitespace rewriting is prohibited.
   - Release labels MUST match every shipped version declaration before a package is published.
9. **Settings Import Boundary:**
   - Read only verified temporary JSON uploads, at most 1 MiB and bounded depth.
   - Validate the entire document before writes; reject unknown fields, nested
     values and invalid shared color/mode values. Preserve omitted partial fields.
   - Require manage_options plus a nonce; changing code/HTML also requires
     unfiltered_html. Export/import must preserve multiline strings and legacy logos.
10. **Progressive Enhancement and Locale:**
   - Reading content must not depend on an animation script or loading overlay.
   - Load RTL overrides explicitly and retain visible keyboard focus.
   - Validate the Turkish compiled catalog and printf placeholders in CI.
