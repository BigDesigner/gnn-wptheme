# Memory and Documentation Map

Updated 2026-10-24. Current context takes precedence over historical session snapshots.

| Path | Status | Purpose |
|---|---|---|
| `.memory-bank/state-of-the-union.md` | Current entry point | Compact Turkish onboarding summary |
| `.memory-bank/active-session.json` | Current session | Schema 1.1.0, branch/commit, governance hashes, deferred task |
| `.memory-bank/system-coherence.md` | Current architecture | Theme 1.6.2 components and verified release state |
| `.memory-bank/adr/0001-*.md` through `0016-*.md` | Decision history | ADR 0004 and 0012 superseded by 0014 and 0016; others retained |
| `.memory-bank/bugs/bug-list.md` | Issue ledger | Audit, FAZ, hardening and CI fixes; closed through issue 67 |
| `.memory-bank/changelog/verified-worklog.md` | Append-only history | Latest verified work first; old entries are dated snapshots |
| `.memory-bank/audits/` | Evidence and reports | Audit/remediation, FAZ reproduction, local and hosted verification |
| `.memory-bank/audits/verification-1.6.2/github-release.json` | Published artifact metadata | CI/release URLs, tag commit, asset size/digest; distinct from local ZIP |
| `.tasks/pipeline.md` | Active roadmap | Only user-site WooCommerce acceptance remains deferred |
| `.tasks/handoff.md` | Session snapshots | Current entry first; historical actions must not be replayed |
| `.tasks/theme-hardening-plan.md` | Completed | 1.6.2 six-scope follow-up and verification |
| `.tasks/audit-remediation-plan.md` | Completed | 1.6.1 A01-A10 remediation |
| `.tasks/files-to-stage-1.6.2.txt` | Historical staging inventory | Original 9140a32 scope; not a list for future commits |
| `.tasks/FEATURE-PLAN.md` | Historical reference | Original PRD, not an active unchecked backlog |
| `.tasks/IMPLEMENTATION-PLAN.md` | Historical reference | Completed Phase 2 specification |
| `.tasks/audits/` | Historical plans | Original audit task evidence |
| `.specs/constitution.md` | Governance | Compatibility, versioning, native panel and GitHub-only distribution |
| `.specs/boundary-conditions.md` | Security contract | Escaping, nonces/capabilities, bounded imports and update packages |
| `.specs/bootstrap.md` | Development setup | Build, locked QA tooling and verification commands |
| `gnn/readme.txt` | Shipped documentation | Theme metadata and changelog |
| `gnn/LICENSE.md` | Shipped license | GPLv2 or later |

No documentation was moved or deleted during this sync. The archive location
`.archive/docs-migration/` is reserved; no archive files currently exist.
