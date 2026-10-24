# Production homepage loading incident — 2026-10-18

Status: resolved and accepted by the user on 2026-10-19. The user enabled the
FAZ update and confirmed the production site works.
User-reported URL: https://gnn.tr/ (redirects to https://www.gnn.tr/).

## Findings

The homepage returns HTTP 200 with its Elementor content and theme footer.
The response declares `Hummingbird-Cache: Served`. The full-screen GNN loading
overlay is enabled. The theme's `features.js` is accessible and is fetched by
the browser, but the renderer becomes unresponsive while the FAZ consent banner
initializes. This prevents the theme's load/timer callbacks from completing.

The same downloaded HTML was replayed in a temporary Chromium page, retaining
its external asset URLs. All non-GET requests were blocked in this diagnostic
environment; no live settings or consent records were changed.

| Diagnostic variant | Result |
|---|---|
| Original homepage and original scripts | Renderer unresponsive |
| Block all FAZ Cookie Manager assets in the test browser | Document completes; GNN overlay disappears |
| Block only FAZ `frontend/js/script.min.js` | Document completes; homepage text is present; GNN overlay disappears |
| Replace only that script with official FAZ 1.34.1 script | Document completes; homepage text is present; GNN overlay disappears |
| Normalize only escaped template closing tags, retaining the original runtime | Still unresponsive; template encoding alone is not an established explanation |
| Substitute theme.js, features.js and slider.js from theme 1.6.0; retain original FAZ runtime | Still unresponsive; changed theme JavaScript alone does not explain the freeze |
| Substitute theme JavaScript/CSS from 1.5.1, reproducing its historical CSS packaging; retain original HTML and FAZ runtime | Still unresponsive; the old static theme assets do not resolve the current-page replay |

The user clarified that the actual upgrade was **1.5.1 to 1.6.1**, not 1.6.0
to 1.6.1. This timing remains relevant and is not disproven by the replay. Git
comparison confirms the loading code (`features.js`, `inc/frontend.php`, footer
and header) did not change between 1.5.1 and 1.6.1. The 1.6.0 script substitution
was initially too narrow for the actual upgrade. A second comparison substituted
the 1.5.1 JavaScript and CSS, reproducing the historical CSS minifier, and also
froze. This remains a static-assets-only comparison, not a complete
old-theme/old-cache rollback: PHP-generated output, runtime order and production
cache history have not all been independently compared. A theme update exposing stale/mixed plugin
assets through cache regeneration is a hypothesis, not an established cause.

A debugger pause sampled the stalled stack inside `_fazRenderBanner`, called
by `_fazInitOperations` and `_fazInit`. This identifies the FAZ initialization
path, but is not a complete root-cause analysis of every internal instruction.
The production runtime's bytes differed from the released 1.34.1 runtime.
The installed plugin version cannot be determined reliably from the public
responses. An older installation or a stale optimized/cached asset remains the
likely explanation; cache responsibility has not been proven.

## Verified update source

- Official release: https://github.com/fabiodalez-dev/FAZ-Cookie-Manager/releases/tag/v1.34.2
- Installable package: https://github.com/fabiodalez-dev/FAZ-Cookie-Manager/releases/download/v1.34.2/faz-cookie-manager-v1.34.2.zip
- Release runtime SHA-256: `c07649c6470d1e0e52f23bed0d73cdf1e40bf328084e77eecd167e388f29d9a5`.
- Served runtime SHA-256: `b9459357d44006bf1beb8a69eae0e359b01b834f34764de18757db44878239f8`.

The user updated/enabled FAZ and confirmed acceptance on the live site. This
production acceptance is user-reported; the agent did not operate production
admin or independently inspect the enabled site. The local replay does not
establish visual image correctness or complete consent behavior: some remote
images were blocked by browser cross-origin protections and consent POSTs were
intentionally disabled. No theme behavior changed or version bump was needed.

Curated machine-readable outcomes: `live-loading-2026-10-18.json`.
Raw public HTML/configs and downloaded plugin sources remain in ignored `.temp/`.

## Upstream confirmation — FAZ Cookie Manager 1.34.2

The 1.34.2 changelog dated 2026-10-18 describes the same trigger: while its
consent runtime held DOM insertions, an insertion next to the held element via
`insertBefore`, `replaceChild`, `before` or `replaceWith` could lose the new
element. In the frontend source, FAZ explicitly says this left the old
`_fazRenderBanner()` loop spinning forever because held insertions never left
their source. The new build identifies itself as
`1.34.2+bootstrap-held-insertions`.

The official 1.34.2 release ZIP was downloaded and its `script.min.js` was
substituted into the saved production homepage response. Chromium then reached
`document.readyState === "complete"`, the page text rendered, and
`.gnn-loading` was absent. This is a direct reproduction of the reported
interaction, not just similar changelog wording. It confirms that 1.34.2 fixes
the reproduced freeze. The user subsequently confirmed live acceptance on
2026-10-19.
