# 1.6.2 GitHub Actions follow-up — 2026-10-24

The initial remote verification of commit `9140a32` failed:
https://github.com/BigDesigner/gnn-wptheme/actions/runs/40043429653

- Linux PHP disables short tags. PHPCS warned about four HTML-only `.php`
  pattern fragments. Added the same PHP package header used by other fragments;
  no sniff exclusions or relaxed warning policy were introduced.
- Integration passed 118/119 scenarios. WooCommerce redirects subscriber login
  to `/my-account/`, while the test assumed `/wp-admin/`. The test now waits for
  the login redirect, verifies a WordPress authenticated-session cookie exists,
  and still requires HTTP 403 from both import/export handlers.

Local verification: PHPCS with `short_open_tag=0` reports zero errors/warnings;
static/package checks pass 90/90. Package metadata was refreshed. Theme remains
1.6.2; these changes complete its CI verification. The next GitHub Actions run
is the authoritative remote verification; its result is available in Actions.
User-site WooCommerce visual/checkout acceptance remains deferred.
