# Changelog

This file tracks the **editorial** version of the investigation report, separate
from the git history of individual files.

Versioning follows editorial semantic versioning (see
[CONTRIBUTING.md §7](CONTRIBUTING.md#7-versioning)):

- **MAJOR** — new conclusions or hypotheses that change the investigation's shape.
- **MINOR** — new evidence or new chapters.
- **PATCH** — editorial fixes (wording, formatting, broken links).

Each released version should correspond to a git tag (e.g. `report-v1.0`).

The format is based on [Keep a Changelog](https://keepachangelog.com/).

---

## [Unreleased]

### Added — Observatory
- Nuova directory `observatory/`: componente predittiva del progetto, dataset vivo
  separato dall'indagine storica (`evidence/` immutabile, `report/` interpretativo).
- `observatory/session-observatory.json`: dataset una-entry-per-sessione con
  timeline T0→T5 (pubblicazione → sitemap OK → Google legge sitemap → crawl landing
  → crawl primo prodotto → indicizzazione GSC). Seed storico pre-patch: sessioni
  20-06-2026 e 21-06-2026 con dati ricostruiti dai log; campi non noti come `null`.
- `observatory/schema.json`: JSON Schema (draft 2020-12) per validazione delle entry;
  campo chiave `patch_active` per il confronto PRE vs POST patch.
- `tools/publish-session-spec.md`: specifica del comando `wp83 fotomoto
  publish-session` (import → repair → generate-json → flush → sitemap-check →
  report JSON). Implementazione nel repo del mu-plugin; qui solo la spec di
  riferimento e il mapping report → observatory.

### Added — Investigation
- F-008: `lastmod` Rank Math coerente con `wp_posts.post_modified` — pista chiusa.
- F-009: Googlebot ha eseguito GET delle sitemap il 24/06/2026 durante il periodo
  in cui le sitemap erano stale (dimostrato dall'indagine).
- F-010: confronto crawl pipeline sessione 20-06-2026 (completa) vs sessione
  21-06-2026 (nessun crawl spontaneo osservato nei log disponibili).
- F-011: pipeline post-patch validata in produzione (`sitemap-check.sh` 8 PASS / 0 FAIL).
- O-001–O-005: osservazioni operative su cache drift, pipeline WP-CLI, flush,
  richieste GSC.
- E-008–E-012: evidenze crawl log (LiteSpeed), DB query, output script.
- H-002, H-003, H-004: aggiunte come `rejected` con evidenza falsificante.
- Q-001a: sotto-domanda operativa di Q-001 — "Google ha aggiornato la crawl queue
  su snapshot sitemap incompleto?"
- Q-002: domanda aperta sull'esperimento T-002 (crawl spontaneo entro 24h post-patch).
- T-002: protocollo esperimento controllato prossima sessione, timeline T0→T7
  (in attesa di esecuzione).

### Changed
- H-001: status aggiornato da `supported` a `supported (rafforzata)` — E-009
  dimostra che Googlebot ha letto le sitemap stale in modo verificabile.
- Q-001: mantenuta aperta; nota rafforzata con riferimento a F-009 e F-010.
- report/05-tests-performed.md → v0.3: aggiunto T-002, referenced evidence
  aggiornato, open questions aggiornate, confidence assessment aggiornato.
- registry/decision-log.md: aggiunti pivot investigativo e approvazione T-002.

### Closed hypotheses
- H-004 (lastmod errato): falsificata da F-008/E-008.
- H-002 (prodotti non pubblicati): falsificata da F-001/F-002/F-003.
- H-003 (canonical/schema errati): falsificata da F-002/F-003.

---

## [Unreleased — scaffolding]

### Added
- Repository scaffolding: folder structure, indices, templates, and empty
  registry files. No investigation content yet.
- Methodology framework (report chapter 02, v1.0): evidence lifecycle, claim
  classification, traceability rules, review workflow with adversarial review,
  experiment documentation standard, privacy/redaction policy, and
  reproducibility standard. Added `templates/experiment.md`.
- Framework hardening (chapter 02 → v1.1): `INVESTIGATION_PRINCIPLES.md` (root);
  ASSUMPTION (`A-`) category with `registry/assumptions.md`; evidence provenance
  (primary/secondary/derived, §1.6); per-chapter Confidence Assessment standard
  (§8) added to all chapters and the chapter template.

### Changed
- `CONTRIBUTING.md` and `GLOSSARY.md` now reference chapter 02 as the canonical
  source for concept definitions, removing conceptual duplication.
- Cross-document consistency pass: README, CONTRIBUTING, GLOSSARY, methodology,
  and templates aligned on identical terminology and the `F-/O-/A-/H-/Q-/E-`
  identifier set.

### Removed
- The experiment `T-` identifier namespace. Experiments are procedures tracked
  through the `E-`/`O-`/`H-` items they produce.

---

<!--
Template for future releases:

## [x.y.z] — YYYY-MM-DD
### Added
### Changed
### Superseded
### Notes
-->
