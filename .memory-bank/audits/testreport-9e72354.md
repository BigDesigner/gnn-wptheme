# Test Execution Report: 9e72354 working tree / theme 1.6.2

Date: 2026-10-24. Scope authorized by the user: close non-WooCommerce follow-up
work. The baseline 1.6.1 no-plugin run passed 38/38 before feature-test authoring.

## Execution dashboard

| Type | Runtime | Total | Passed | Failed | Skipped |
|---|---|---:|---:|---:|---:|
| Integration | Real WordPress 7.1.3 / PHP 8.2.33 / SQLite | 61 | 61 | 0 | 0 |
| Browser/HTTP | Chromium against the same install | 28 | 28 | 0 | 0 |
| Integration | Real WordPress 6.0.17 / PHP 7.4.33 / SQLite | 61 | 61 | 0 | 0 |
| Browser/HTTP | Chromium against the minimum install | 28 | 28 | 0 | 0 |
| Static/package (not integration) | PHP/Node/Python | 90 | 90 | 0 | 0 |

WPCS reports zero errors and warnings. Both matrices have no Elementor or
WooCommerce installed. Existing plugin-dependent suites were outside this
requested matrix rather than being relabeled as executed passes.

## Coverage

Line/branch coverage: **unmeasured**. Playground PHP WASM has no PCOV/Xdebug
coverage extension; browser code coverage was not instrumented. Counts below
describe executed scenarios, not a code coverage percentage.

## Coverage map

- Native WordPress option/theme_mod persistence and exact export/import round trips.
- Bounded JSON parsing; nested/null/unknown/invalid fields; oversized documents.
- Partial imports preserve absent checkbox values; multiline/Unicode/code content.
- Capability filtering, subscriber denial and code-field authorization boundaries.
- Real admin authentication, multipart uploads, content-disposition/JSON response,
  redirect status, failed-import state preservation and CSRF denial.
- Legacy logo IDs/URLs, actual media-field HTML, re-save and transfer, explicit
  deletion, missing-attachment fallback, shared-logo visibility across modes.
- Native menu update hook with a valid core menu nonce and correct meta target.
- Existing updater, reset, homepage, pagination and slider regression scenarios.
- Mobile Escape/focus, no-JavaScript readability, special-character anchors, RTL
  stylesheet request/geometry/overflow and a real Turkish gettext catalog.
- Four axe WCAG scans per matrix, stable color transitions, measured local
  navigation/LCP/CLS/resource budgets; screenshots retained.

## Product bugs and test triage

Implemented fixes are documented in `remediation-1.6.2.md` and issue IDs 58–65.
The test harness initially exposed setup defects: missing core admin helpers,
manual POST/REQUEST nonce mismatch, a nonexistent native-English translation
download, an English-only expectation after Turkish locale installation,
offscreen reveal timing, color-transition sampling and CSS computed `auto`
offset serialization. Those setup/expectation defects were corrected using
actual WordPress APIs and stable browser conditions. Assertions were retained;
none were skipped or softened to hide an application failure.

## Unresolved after self-heal

None in the final requested matrices. Earlier diagnostic outputs remain under
`verification-1.6.2/`; final authoritative matrices are in `current-no-woo/`
and `minimum-no-woo/`.

## Not covered / unverified boundaries

- User-site WooCommerce visual/checkout acceptance remains explicitly deferred.
- No production mutation, real payment transaction, MySQL/MariaDB host or
  Elementor Pro exercise was performed.
- No manual screen-reader or production Web Vitals/INP result is claimed.
- axe incomplete contrast targets remain recorded for content-dependent manual
  review; zero automated violations is not complete WCAG certification.
- Optional third-party fonts/icons were disabled in the measured local fixture.
- PHP/browser percentage coverage is unmeasured; Node engine warning is noted
  in the main report. Subsequent hosted verification at `fda9916` passed; see the follow-up below.

## Evidence and teardown

`verification-1.6.2/delivery-summary.json` points to observed results and counts.
Both runners dispose their WordPress workers and browsers in `finally`.
The production site and production data were not used as the test datastore.

## Commit plan (user authorized)

`fix(theme): harden settings and close non-commerce QA in 1.6.2`

The exact reviewable staging inventory is `.tasks/files-to-stage-1.6.2.txt`.
It also includes earlier uncommitted FAZ acceptance/documentation updates carried
forward in this chat. The user authorized versioning, commit and push after
successful verification; the resulting commit is recorded in Git history.

## Hosted follow-up and release — 2026-10-24

Commit `9140a32` was pushed; `fda9916` corrected Linux PHPCS markup detection and
the WooCommerce subscriber-login redirect assumption. [Hosted CI](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40045204839) passed
119/119 integration/browser cases plus 90/90 static/package checks. Runtime:
WP 7.1.3/PHP 8.2.33, Elementor 4.3.5, WooCommerce 11.2.1. [Manual Release](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40046157813)
succeeded and published `v1.6.2`. Local 89/89 matrices above remain separate
evidence. Neither publication nor automated WooCommerce tests imply production
installation or the deferred user-site checkout acceptance.
