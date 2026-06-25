# Decision Log

> **Status:** seeded with the scaffolding decision only.

A chronological log of investigation decisions and review events: what was
decided, when, by whom, and why. This is the audit trail of the investigation's
process (distinct from the evidence about the site itself).

Append new entries at the bottom. Never rewrite past entries; correct them with a
new dated entry.

---

| Date | Type | Summary | Decided/Reviewed by | Outcome |
|------|------|---------|---------------------|---------|
| 2026-06-25 | setup | Repository architecture defined (English; full registry with F-/O-/H-/Q-/E- IDs; complete scaffolding). | Project owner | Approved |
| 2026-06-25 | decision | Methodology authored (chapter 02 v1.0): evidence lifecycle, claim classification, traceability, review workflow (incl. adversarial review), experiment documentation, privacy/redaction, reproducibility. Added `templates/experiment.md`. Methodology chapter set as canonical source for concept definitions; CONTRIBUTING.md and GLOSSARY.md updated to reference it. | Project owner | Chapter 02 moved to `in-review` |
| 2026-06-25 | decision | Removed the experiment `T-` namespace. Rationale: an experiment is a procedure, not a first-class entity; it produces evidence (`E-`), observations (`O-`), and possibly hypotheses (`H-`). A dedicated id added tracking overhead without representing a distinct artifact. Methodology §5 and `templates/experiment.md` updated. | Project owner | Adopted |
| 2026-06-25 | decision | Adopted **ASSUMPTION** (`A-`) as a first-class category (methodology §2.1, `registry/assumptions.md`). Rationale: the framework's principles require minimizing assumptions and making the rest explicit; a tracked category enforces visibility and falsifiability. Considered and rejected: folding assumptions into HYPOTHESIS (different role — premises are relied upon, not tested) or leaving them as prose (defeats traceability). | Project owner | Adopted |
| 2026-06-25 | decision | Added evidence **provenance** (primary/secondary/derived, §1.6) and a per-chapter **Confidence Assessment** standard (§8). Added root `INVESTIGATION_PRINCIPLES.md`. Performed cross-document consistency pass (README/CONTRIBUTING/GLOSSARY/methodology/templates); methodology remains the single canonical source. Chapter 02 → v1.1. | Project owner | Adopted |

<!--
Type values: setup | decision | review | scope-change | evidence | superseded
-->
