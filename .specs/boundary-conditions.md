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
