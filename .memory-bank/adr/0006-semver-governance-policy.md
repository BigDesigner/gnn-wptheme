# 6. Semantic Versioning Governance Policy

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-02

## Context
GNN ships via the GitHub-based self-updater ([[0004-github-based-self-updater]]),
so the version number is no longer cosmetic — WordPress compares it against
`GNN_VERSION` to decide whether an "Update available" notice appears at all. A
version bump that is skipped, inconsistent across files, or unnecessarily large
(e.g. a routine bug-fix release bumping MAJOR) directly breaks the update
experience or needlessly signals a breaking change that never happened.

## Decision
Codify version governance as a project rule in `.specs/constitution.md`:
1. Every commit that changes theme behavior (not just docs/comments) MUST bump the
   version.
2. Never bump MAJOR by default — only PATCH or MINOR unless a breaking change is
   explicit and deliberate.
3. The version must be synced, in the same commit, across every location that
   declares it: `gnn/style.css` (theme header), `gnn/functions.php`
   (`GNN_VERSION` constant), `gnn/readme.txt` (Stable tag + a new changelog
   entry), and `.memory-bank/system-coherence.md`.
4. `.scripts/gen-i18n.py` reads `Project-Id-Version` dynamically from `style.css`
   rather than hardcoding it, so the i18n pipeline can never drift out of sync
   with the declared version.

## Consequences
- The self-updater's version comparison is always trustworthy — a release is never
  half-tagged (e.g. `style.css` bumped but `GNN_VERSION` left stale, which would
  make WordPress core and the GitHub updater disagree about the current version).
- Every release's actual scope is visible from the version number alone, which
  matters more here than in most themes because the updater surfaces it directly
  to site owners as an actionable "Update now" decision.
- Slightly more commit discipline is required (four files instead of one), but this
  is checked mechanically as part of the standard quality gate before every push.

## Implementation Review — 2026-10-18

The version policy remains active. Its enforcement claims above are not fully
implemented in the inspected workflow at commit
`0ac1688e937f07fe2ff637a8daf9e37bc510ca98`:

- `.github/workflows/release.yml` is manually triggered and contains no mechanical
  version-consistency or test gate before publishing. No tracked push/PR workflow
  was found to establish the claimed automatic check before every push.
- `version_override` can change the release tag and asset name without changing
  the installed theme version. Audit A08 records this policy-enforcement gap.
- The custom updater compares the remote version with the installed theme's
  `style.css` version via `wp_get_theme(...)->get('Version')`, rather than comparing
  directly with `GNN_VERSION` as the historical Context paragraph suggests.
- The current source version declarations are synchronized at 1.6.0; that snapshot
  does not prove that future releases are mechanically protected from mismatch.

This is an evidence correction, not a policy replacement. ADR 0012 documents the
current packaging topology; changes to release enforcement still require implementation.

## Evidence

Remediation follow-up: version 1.6.1 adds `.scripts/check-release.py`, reusable
verification CI, and a release dependency on that verification. The gaps above
describe the 1.6.0 baseline. See ADR 0016 and the remediation report for current
implementation and the distinction between local checks and hosted CI execution.

- [Release workflow](C:/Users/bigde/.antigravity/gnn-wptheme/.github/workflows/release.yml:1).
- [Updater local version lookup](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/class-gnn-github-updater.php:154).
- [Active version policy](C:/Users/bigde/.antigravity/gnn-wptheme/.specs/constitution.md:19).
- [Packaging ADR 0012](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0012-shared-python-theme-packaging-and-manual-releases.md).
