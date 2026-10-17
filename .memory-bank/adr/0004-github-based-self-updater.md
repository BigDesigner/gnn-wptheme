# 4. GitHub-Based Self-Updater

> [!WARNING]
> **SUPERSEDED ARCHITECTURAL DECISION**
> Superseded on 2026-10-18 by [ADR 0014](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0014-validated-github-release-assets-and-cron-updates.md).
> Follow the successor for active update validation and migration behavior.

- **Status:** Superseded by ADR 0014
- **Superseded By**: [ADR 0014](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0014-validated-github-release-assets-and-cron-updates.md)
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
GNN is distributed outside WordPress.org (no theme repository listing), so it has no
built-in update mechanism. Site owners need a way to receive new versions without
manually downloading and re-uploading a zip file over FTP/SFTP.

## Decision
Implement a self-contained GitHub-based updater (`inc/class-gnn-github-updater.php`,
bootstrapped by `inc/updater.php`) that hooks into WordPress's native theme-update
APIs (`pre_set_site_transient_update_themes`, `upgrader_source_selection`). It polls
the GitHub Releases API for the repository configured in the class, caches the
response in a transient (with a negative-cache sentinel for "no release found" to
avoid hammering the API on every admin page load), and surfaces status through the
GNN Panel rather than a separate settings screen. The mechanism is opt-in via a
`gnn_github_updates_enable` option (default off) so it never phones out to GitHub
without the site owner's consent.

## Consequences
- Site owners get the standard WordPress "Update available" experience (Appearance →
  Themes, and a one-click "Update now") without a WordPress.org listing.
- Adds a single external dependency at runtime: the GitHub Releases API, called only
  when the feature is enabled and rate-limited via transient caching.
- The updater's own file must stay excluded from PHPCS's "one class per file" rule
  violations — it now lives in its own class file, with `updater.php` as a thin
  bootstrap, to satisfy WordPress Coding Standards.

## Implementation Review — 2026-10-18

The decision above is preserved as the historical integration intent. Source
inspection at commit `0ac1688e937f07fe2ff637a8daf9e37bc510ca98` shows differences
that must not be mistaken for verified compliance:

- `gnn_updates_enabled()` currently defaults to **enabled** through
  `get_option('gnn_github_updates_enable', 1)`, despite this ADR's default-off intent.
  No policy change approving that divergence is recorded here.
- The constructor registers `upgrader_post_install`, not the
  `upgrader_source_selection` hook named above.
- `functions.php` loads the updater only inside `is_admin()`. The isolated
  bootstrap audit therefore found no GitHub update filter in non-admin contexts.
- Audit A03–A05 record missing cron discovery, callback error handling, and an
  unsuitable source-archive fallback. These remain unresolved implementation issues.

No architecture replacement is accepted by this annotation, so no supersession
link is created. ADR 0012 separately records the existing release packaging path.

## Evidence

- [Updater switch default](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/updater.php:24).
- [Registered hooks](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/class-gnn-github-updater.php:65).
- [Admin-only loading](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/functions.php:258).
- [Packaging ADR 0012](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0012-shared-python-theme-packaging-and-manual-releases.md).
- [Audit report](C:/Users/bigde/.antigravity/gnn-wptheme/.tasks/audits/2026-10-18/report.md).
