# ADR 0012: Shared Python Theme Packaging and Manual Releases

> [!WARNING]
> **SUPERSEDED ARCHITECTURAL DECISION**
> Superseded on 2026-10-18 by [ADR 0016](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0016-github-only-lossless-packaging-and-release-gates.md).
> Follow the successor for distribution scope, lossless CSS and release gates.

- **Status**: Superseded by ADR 0016
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Structural Topology
- **Supersedes**: None
- **Superseded By**: [ADR 0016](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0016-github-only-lossless-packaging-and-release-gates.md)

## Context

The repository contains the distributable theme under `gnn/` alongside project
memory, scripts, and workflow configuration. A repository source archive therefore
differs from the installable theme package. Local and release builds need the same
packaging behavior. This record documents the existing build topology at commit
`0ac1688e937f07fe2ff637a8daf9e37bc510ca98`, without changing release policy.

## Decision

Use `.scripts/build-zip.py` as the shared local and CI packager. Include only the
theme tree under the top-level archive path `gnn/`. Keep the `style.css` header
intact; transform other CSS and JavaScript using the script's current minifiers.
Generate a placeholder screenshot only when the theme screenshot is absent.

Produce `.build/gnn.zip` by default. The `--wporg` mode produces
`.build/gnn-wporg.zip` and excludes the demo directory. This filename expresses a
packaging variant, not WordPress.org review approval.

Publish GitHub releases through the manually triggered workflow, which invokes
the same packager, copies the normal ZIP to a versioned asset name, and creates
a `v<version>` release. ADR 0006 remains the governing source-version policy.

## Consequences

- The same script controls archive membership and transformations locally and in CI.
- Project documentation and agent instructions are outside the shipped theme tree.
- A GitHub repository zipball cannot be treated as an equivalent installable asset;
  audit A05 records the updater fallback mismatch.
- Current limitations remain open: the CSS transformation changes a selector's
  semantics (A10), and `version_override` can label unchanged source with a different
  release version (A08).
- The workflow has no lint/test or version-consistency enforcement step. Shared
  packaging does not establish functional correctness or release readiness.
- Locally cached output can be stale: the audit found an existing WP.org ZIP with
  a 1.0.0 header while the inspected source is 1.6.0.
- This complements ADRs 0004 and 0006; it does not approve their implementation
  defects or introduce a replacement updater architecture.

## Evidence

- [Packager](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/build-zip.py:1).
- [Asset transforms](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/build-zip.py:80).
- [Release workflow](C:/Users/bigde/.antigravity/gnn-wptheme/.github/workflows/release.yml:1).
- [Version governance](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0006-semver-governance-policy.md).
- [Packaging and source-archive audit evidence](C:/Users/bigde/.antigravity/gnn-wptheme/.tasks/audits/2026-10-18/evidence.json).
