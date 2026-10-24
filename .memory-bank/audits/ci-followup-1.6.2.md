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
1.6.2; these changes complete its CI verification. [GitHub Actions run 40045204839](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40045204839) completed successfully at commit
`fda9916`: 119/119 integration/browser scenarios and 90/90 static/package checks.
[Manual Release](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40046157813) subsequently succeeded and published `v1.6.2`.
User-site WooCommerce visual/checkout acceptance remains deferred.
