# ADR 0015: Non-Destructive Elementor Library Migrations

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Persistence
- **Supersedes**: None
- **Superseded By**: None

## Context

Audit A02 found that comparing the installed library flag to every theme release
caused permanent deletion and recreation of saved Elementor templates. This could
discard user edits and break ID-based references. The user authorized fixing it.

## Decision

Version generated library content independently with `GNN_ELEMENTOR_LIBRARY_VERSION`.
Identify templates by their existing slug metadata and preserve their post IDs.
Store a fingerprint of the generated title, content, status, Elementor data and
page settings after a successful managed write. Refresh in place only if that
fingerprint still matches. Preserve modified records and unknown legacy baselines.

Add missing entries without deleting siblings. Respect trash status instead of
resurrecting user-trashed templates. Automatic migration requires
`edit_theme_options`. Report failures without marking the migration complete.
The panel refresh action follows the same preservation policy and explains it.

## Consequences

- User edits and existing template IDs survive library/theme updates.
- Legacy templates without trustworthy baselines remain unchanged, including during
  manual refresh. This deliberately trades automatic recoloring for data preservation.
- Ordinary theme patch releases no longer force template regeneration.
- Missing records can heal independently, and failed inserts can be retried.
- Fingerprints detect edits; they do not replace database backups or transactional storage.
- ADR 0005's Elementor CSS default remains unchanged; it addresses a different concern.

## Evidence

- [Library installer and fingerprint](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/elementor-templates.php).
- [Panel refresh and failure notice](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php).
- [Real WordPress persistence scenarios](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/tests/wordpress.php).
