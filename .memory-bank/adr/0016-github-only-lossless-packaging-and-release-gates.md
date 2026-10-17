# ADR 0016: GitHub-Only Lossless Packaging and Release Gates

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-18
- **Category**: Structural Topology
- **Supersedes**: [ADR 0012: Shared Python Theme Packaging and Manual Releases](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/adr/0012-shared-python-theme-packaging-and-manual-releases.md)
- **Superseded By**: None

## Context

The user explicitly excluded WordPress.org publishing and requested completion of
audit A08/A10. The old package variant is unnecessary, the CSS regex changes
selector semantics, and release overrides can label a package with a false version.

## Decision

Keep the shared Python packager and single top-level `gnn/` theme tree. Produce only
the normal `gnn.zip`; reject the retired `--wporg` flag. Leave the existing ignored
WordPress.org artifact outside the maintained release process.

Copy CSS bytes without transformation. Validate `style.css`, the PHP constant,
readme/stable tag/changelog, gettext version headers and architecture version
before writing a ZIP. A workflow override is only a confirmation and must equal
the source version; pass it through the environment rather than shell interpolation.

Add static/package and real WordPress/browser CI jobs for pushes and pull requests.
Make the manual release workflow depend on this reusable verification workflow.
Development test dependencies live outside the distributable theme.

## Consequences

- Source and packaged CSS have identical semantics and bytes.
- Invalid or mismatched version declarations fail before publication.
- GitHub Actions release creation waits for verification; CI definitions still
  require actual GitHub execution to prove hosted-runner compatibility.
- Browser/WordPress tests add development download/runtime cost but no shipped
  runtime dependencies. Coverage is reported separately from pass counts.
- ZIP compression remains active; CSS source text is no longer regex-minified.

## Lineage & Migration

ADR 0012's shared Python entry point and manual release model remain, but the
WordPress.org variant, CSS transformation and missing verification gates are
replaced. Existing private installs continue to receive the named GitHub ZIP.
The stale ignored WP.org ZIP is neither a blocker nor a publication target.
ADR 0006's source-version governance remains in force.

## Evidence

- [Packager](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/build-zip.py).
- [Release validator](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/check-release.py).
- [Package checks](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/verify.py).
- [Reusable CI](C:/Users/bigde/.antigravity/gnn-wptheme/.github/workflows/verify.yml).
- [Release dependency](C:/Users/bigde/.antigravity/gnn-wptheme/.github/workflows/release.yml).
