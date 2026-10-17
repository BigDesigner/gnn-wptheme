# ADR 0014: Validated GitHub Release Assets and Cron Updates

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: External Integrations
- **Supersedes**: [ADR 0004: GitHub-Based Self-Updater](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0004-github-based-self-updater.md)
- **Superseded By**: None

## Context

The user authorized remediation of audit A03–A05/A08. The previous integration was
admin-only, accepted repository zipballs with the wrong theme root, and attempted
unchecked moves after installation. GitHub/private ZIP is the sole distribution
channel; WordPress.org publication is explicitly excluded.

## Decision

Load updater hooks outside the panel-only include so WP-Cron can discover releases.
Preserve the existing `gnn_github_updates_enable` option and its implemented default
of enabled; do not silently change existing installations to a different policy.
The old ADR's opt-in/default-off description did not match the implementation.

Accept stable MAJOR.MINOR.PATCH tags and only the exact `gnn-VERSION.zip` asset on
the configured repository's HTTPS GitHub release path. No asset means no offered
update; repository source archives are never a fallback. Cache results separately
from the old permissive cache. Ensure update/no-update entries are mutually exclusive.

Use `upgrader_source_selection` to validate the extracted theme and prepare its
directory before core replaces the installed theme. Preserve incoming errors and
unrelated themes. Refuse invalid roots, path traversal and failed/occupied renames.
Use `upgrader_process_complete` for cache invalidation after a theme update.

## Consequences

- Cron discovery and normal admin checks share one updater instance and cache.
- A missing/misnamed release asset safely suppresses the update offer.
- GitHub availability remains an external dependency; transient caching limits calls.
- Package validation occurs before core mutates the installed theme; core remains
  responsible for installation and its native rollback behavior.
- This is structural/package validation, not cryptographic release signing.

## Lineage & Migration

ADR 0004's integration intent is retained while its hook and fallback mechanisms
are replaced. Existing option names and user choices are preserved. The versioned
cache prevents reusing permissive cached package URLs. No database migration or
source-archive conversion is performed. Existing releases must attach the named ZIP.

## Evidence

- [Updater loading](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/functions.php).
- [Updater validation and hooks](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/class-gnn-github-updater.php).
- [Integration assertions](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/tests/wordpress.php).
- [Authorized remediation plan](C:/Users/bigde/.antigravity/gnn-wptheme/.tasks/audit-remediation-plan.md).
