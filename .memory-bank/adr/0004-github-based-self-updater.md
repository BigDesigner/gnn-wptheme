# 4. GitHub-Based Self-Updater

- **Status:** Accepted
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
