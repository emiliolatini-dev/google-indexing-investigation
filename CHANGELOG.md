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
