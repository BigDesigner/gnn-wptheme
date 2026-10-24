# Audit Remediation and Verification — GNN 1.6.1

Date: 2026-10-18. Status: **All ten audit findings fixed and locally verified.**
Baseline: commit `0ac1688`, theme 1.6.0. The current 1.6.1 changes are uncommitted.
The user authorized documenting the backlog, completing the fixes in order and
verifying them. WordPress.org publishing and `gnn-wporg.zip` maintenance were
explicitly excluded. Only the normal GitHub/private theme ZIP is maintained.

## Subsequent Acceptance — 2026-10-18

The implementation/verification sections below retain their original local-run
snapshot. Subsequent events:

- Commit `9e72354` was pushed to `main` at the user's request.
- [Verify theme](https://github.com/BigDesigner/gnn-wptheme/actions/runs/39076284690)
  and [Manual Release](https://github.com/BigDesigner/gnn-wptheme/actions/runs/39076562858)
  completed successfully for that commit.
- [v1.6.1](https://github.com/BigDesigner/gnn-wptheme/releases/tag/v1.6.1) is published
  with the named `gnn-1.6.1.zip` asset.
- The user reports a successful real update without problems. No further claims
  about their site's data or production behavior are inferred from that report.
- User-site WooCommerce visual/checkout checks are deferred until the end; that
  plugin is currently absent from their site.

## Finding Closure

| Audit / bug | Priority | Change | Verification |
|---|---|---|---|
| A01 / 47 | P1 | Latest-posts mode explicitly delegates to home.php before rendering a static homepage | Real browser: posts, pagination, static content and empty blog on both WP versions |
| A02 / 48 | P1 | Independent library version, stable IDs, edit fingerprints, conservative legacy handling, missing-entry healing and failure retry | 11 real WordPress/Elementor persistence checks |
| A03 / 49 | P2 | Updater bootstrap loads outside admin; disabled preference still prevents filter registration | Real non-admin WordPress requests and disabled-instance check |
| A04 / 50 | P2 | Remove post-install mutation; validate/prepare source before core installs it; retain WP_Error/unrelated sources; refuse occupied renames | Real WordPress filesystem and hook assertions; stale update cleanup |
| A05 / 51 | P2 | Exact versioned GitHub release asset required; no repository ZIP fallback; reject bad tags, prereleases and untrusted URLs | Real WP HTTP interception at the external GitHub boundary, positive/negative cache tests |
| A06 / 52 | P2 | Detect Woo shortcodes, any Woo block namespace, references, dynamic blocks, Elementor pages and active product widgets; preserve header cart fragments | 19 checks with actual WooCommerce, WP_Query, parsed blocks, options and enqueue queues |
| A07 / 53 | P2 | Inactive slides inert/hidden from AT; tab-index fallback; focus pauses rotation until restart; explicit rotation control; ordinary dot buttons; RTL direction | Chromium: autoplay, focus, restart, dots, reduced motion, RTL, mobile, no-JS and single-slide cases |
| A08 / 54 | P2 | Source-version validator guards package/release; override only confirms source; workflow input uses env; release depends on reusable verification | Five validator cases including mismatched versions and shell-like literal input; workflow source review |
| A09 / 55 | P3 | Reset panel options and shared brand mods; preserve global site icon, menu assignments and independent update preference; explain scope | Five real WordPress persistence/capability/nonce checks |
| A10 / 56 | P3 | Remove regex CSS minification and copy source CSS bytes into the ZIP | Every packaged CSS file compared byte-for-byte against source |

The baseline audit listed A09 as P3 in its final detailed classification; its
original impact remains a reset-contract mismatch rather than a security claim.

## Observed Execution

| Type | Real runtime / tool | Passed | Failed |
|---|---|---:|---:|
| WordPress integration | WP 7.1.3, PHP 8.2.33, SQLite, Elementor 4.3.5, WooCommerce 11.2.1 | 54 | 0 |
| Browser flows | Installed Chromium/Chrome against that WordPress server | 14 | 0 |
| Minimum-version integration | WP 6.0.17, PHP 7.4.33, SQLite, optional plugins absent | 24 | 0 |
| Minimum-version browser flows | Chromium/Chrome against minimum-version server | 14 | 0 |
| Static/package checks | Native PHP 8.5.8, Node 24.14.0, Python 3.14.3 | 88 | 0 |

Total: **194 successful checks**, separated by type above. Syntax checks are not
counted as WordPress integration. The minimum environment intentionally excludes
current optional plugins; their current-version integration was exercised in the
first environment.

**Coverage: unmeasured.** No PHP coverage extension or instrumented JS line/branch
coverage collector was configured. A scenario count is not a coverage percentage.

## Test Setup Corrections and Evidence Integrity

The first full integration/browser run produced 64 passes and four failures.
The failing expectations were retained. Their setup was corrected as follows:

- Sidebar fixtures were written through WordPress's widget setter so the
  per-request widget cache was invalidated.
- The real frontend enqueue action was run before asserting WooCommerce script
  handles; the earlier fixture had not registered those handles.
- The browser clock was installed and paused before navigation/timer creation,
  making the autoplay expectation deterministic.

No behavior assertion was removed, weakened or skipped to get the successful
repeat. The first-run JSON is preserved beside the final result JSON. An earlier
single-worker Playground bring-up stalled during plugin preparation; only that
test process was stopped, then the full plugin setup used six workers. The final
test servers/browser were disposed by the runner's finally block.

## Package

- Output: [.build/gnn.zip](C:/Users/bigde/.antigravity/gnn-wptheme/.build/gnn.zip).
- Version: 1.6.1; 85 theme files; 268,583 bytes.
- SHA-256: `69acab483635570b31f2513baba15f7509ca2295954e2b2c8addb2c7bbcbe0dc`.
- ZIP CRC passed; archive contains exactly the theme tree, including demo content.
- All six CSS files match source bytes; all five packaged JS files parse.
- Source/PHP/readme/gettext/architecture version declarations agree; current
  changelog exists and Turkish slider/control translations compile.
- No WordPress.org artifact was generated, published or used as a release gate.

## Documentation and Architecture

- The active plan and pipeline are complete; bugs 47–56 are marked fixed.
- Historical PRDs link to the current plan instead of masquerading as a backlog.
- ADR 0014 supersedes ADR 0004 for validated GitHub update behavior.
- ADR 0015 records non-destructive Elementor library migrations.
- ADR 0016 supersedes ADR 0012 for GitHub-only packaging and verification gates.
- Security boundaries, distribution/version rules, bootstrap instructions,
  architecture summary, readme/changelog, worklog and handoff are updated.
- The original report/evidence remain a labeled 1.6.0 baseline. Its observation-only
  framework doubles were retired in favor of real WordPress/SQLite and browser tests.

## Limits and Unexecuted Boundaries

- GitHub Actions workflow definitions were reviewed locally; no hosted Actions run,
  commit, push, GitHub release, production deployment or live theme update occurred.
- External GitHub API success/failure was intercepted through WordPress's HTTP
  boundary; real outbound release downloads and production filesystem permissions
  were not exercised.
- SQLite was used; a separate MySQL/MariaDB host was not tested.
- Elementor Pro UI, payment/checkout transactions, manual screen-reader operation,
  Lighthouse and measured production LCP/CLS/INP are outside these checks.
- PHPCS/WPCS remains unavailable locally; no WPCS compliance certification is claimed.
- Local Node 24.14.0 executed the suite successfully but is below the Playground
  package's declared 24.18.0 engine minimum. Development instructions and CI specify
  24.18.0 or later; the engine warning is not presented as a product defect.
- The earlier killed bring-up's native temporary test directory may remain;
  it contains only the disposable test installation, not user/production data.

## Reproduction

```powershell
python .scripts/verify.py
node .scripts/test-wordpress.mjs
# Minimum matrix (without the optional plugins):
$env:GNN_QA_MINIMUM = '1'
$env:GNN_QA_WP = '6.0'
$env:GNN_QA_PHP = '7.4'
$env:GNN_QA_PORT = '9733'
node .scripts/test-wordpress.mjs
```

Install the pinned development dependencies and browser as documented in
`.specs/bootstrap.md`. These tools are outside `gnn/` and do not ship with the theme.

## Evidence Files

- [Final current-runtime results](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/results.json).
- [Final minimum-runtime results](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/results-minimum.json).
- [Static/package results](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/static-package.json).
- [First run, retained](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/results-first-pass.json).
- [Package digest](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/package.json).
- [Mobile screenshot](C:/Users/bigde/.antigravity/gnn-wptheme/.memory-bank/audits/verification-1.6.1/mobile-slider.png).

Suggested commit, not executed:
`fix(theme): resolve audit findings and verify release 1.6.1`.
