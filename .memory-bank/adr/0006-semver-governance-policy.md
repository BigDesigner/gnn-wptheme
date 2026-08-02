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
