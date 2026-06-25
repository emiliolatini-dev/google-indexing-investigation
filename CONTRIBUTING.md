# Contributing & Review Conventions

This document defines how the investigation is written, tracked, and reviewed.
It applies to every file in the repository. The objective is a white-paper-grade
artifact that any reader can independently reconstruct.

---

## 1. Epistemic rules (non-negotiable)

> Canonical definitions of the categories and the traceability rules live in
> [report/02-methodology.md](report/02-methodology.md) (§2–§3). The table below is
> a quick-reference for contributors; if it ever diverges, the methodology chapter
> wins.

Every substantive statement must belong to exactly one category and be labeled
accordingly:

| Category | Inline badge | Requirement |
|----------|--------------|-------------|
| **FACT** | `[FACT · F-NNN]` | Must cite at least one evidence ID (`E-NNN`). |
| **OBSERVATION** | `[OBSERVATION · O-NNN]` | Must cite at least one evidence ID (`E-NNN`). |
| **ASSUMPTION** | `[ASSUMPTION · A-NNN]` | Unproven premise made explicit; states what would falsify it. Marked *(unproven premise)*. |
| **HYPOTHESIS** | `[HYPOTHESIS · H-NNN]` | Must list supporting **and** falsifying evidence. Always marked *(to be verified)*. |
| **QUESTION** | `[QUESTION · Q-NNN]` | An open question; no claim implied. |

Hard rules:

- **Never invent data.** If a value is unknown, mark it as a QUESTION.
- **Never make assumptions** presented as facts.
- **Never draw unsupported conclusions.**
- A hypothesis is **never** written as if it were a fact.
- The tone is neutral: never accusatory, never sensationalist, never written to
  prove a predetermined thesis.

---

## 2. Identifier system

| Type | Prefix | Numbering | Defined in |
|------|--------|-----------|------------|
| Fact | `F-` | zero-padded, 3 digits (`F-001`) | [`registry/claims.md`](registry/claims.md) |
| Observation | `O-` | `O-001` | [`registry/claims.md`](registry/claims.md) |
| Assumption | `A-` | `A-001` | [`registry/assumptions.md`](registry/assumptions.md) |
| Hypothesis | `H-` | `H-001` | [`registry/hypotheses.md`](registry/hypotheses.md) |
| Question | `Q-` | `Q-001` | [`registry/questions.md`](registry/questions.md) |
| Evidence | `E-` | `E-001` | [`evidence/`](evidence/) indices |

- IDs are **stable**: once assigned, never reused or renumbered.
- A retired item is marked `superseded` or `rejected`, never deleted from the registry.
- **Experiments have no identifier** — they are procedures, tracked through the
  `E-`/`O-`/`H-` items they produce (see
  [methodology §5](report/02-methodology.md#5-experiment-documentation)).

---

## 3. Single source of truth (no duplication)

- A fact lives in **one** place and is referenced everywhere else via a relative
  Markdown link, e.g. `[F-012](../registry/claims.md#f-012)`.
- Never copy-paste content between chapters.
- The report **references** evidence; it never pastes raw data inline. Raw data
  lives only under [`evidence/`](evidence/).

---

## 4. Evidence rules

- Evidence is **immutable and dated.** Once committed, an evidence file is never
  edited. A correction is a **new** evidence item that supersedes the old one
  (noted in the folder index).
- Each evidence item is **self-describing**: how it was collected, with which
  tool/command, and when.
- Where possible, include the **reproducible command** (e.g. the exact `curl`
  line) so a reviewer can re-run it.
- **No sensitive data** in the repository (GSC tokens, internal IPs, credentials).
  Record the collection procedure in anonymized form instead.
- Naming: `E-<id>_<YYYY-MM-DD>_<category>_<short-description>.<ext>`
  Example: `E-021_2026-06-25_http_product-url-headers.txt`

See [`templates/evidence-entry.md`](templates/evidence-entry.md).

---

## 5. Report chapter standard

Every chapter follows the skeleton in
[`templates/report-chapter.md`](templates/report-chapter.md) and begins with a
header declaring `Status`, `Version`, `Last updated`, and `Depends on`. This
makes each chapter independently reviewable.

---

## 6. Review workflow

Each chapter and registry file advances through three states, declared in its header:

| State | Meaning |
|-------|---------|
| `draft` | Initial content; may contain `TODO`. |
| `in-review` | Ready for peer review (human + ChatGPT). |
| `stable` | Reviewed and frozen until new evidence arrives. |

Every review event is logged in
[`registry/decision-log.md`](registry/decision-log.md) (date, reviewer, target,
outcome).

### Review checklist

Before moving an item to `stable`, confirm:

- [ ] Every FACT and OBSERVATION cites at least one evidence ID.
- [ ] No hypothesis is presented as a fact; no assumption is presented as a fact.
- [ ] Every hypothesis lists both supporting and falsifying evidence.
- [ ] Every load-bearing assumption is explicit and states what would falsify it.
- [ ] Every evidence item records its provenance (primary/secondary/derived).
- [ ] Tone is neutral and non-accusatory.
- [ ] No duplicated content; cross-references used instead.
- [ ] No sensitive data committed.
- [ ] All referenced `E-`/`F-`/`O-`/`A-`/`H-`/`Q-` IDs resolve to an existing entry.
- [ ] Chapter header (status/version/depends-on) is up to date.
- [ ] Chapter ends with a Confidence Assessment consistent with its evidence.

---

## 7. Versioning

- **Git** versions the files.
- [`CHANGELOG.md`](CHANGELOG.md) versions the *document* using editorial semver:
  `MAJOR` (new conclusions/hypotheses), `MINOR` (new evidence/chapters),
  `PATCH` (editorial fixes). Each report release is a git tag (e.g. `report-v1.0`).

### Commit message convention

Prefix commits by area: `evidence:`, `report:`, `registry:`, `docs:`, `data:`,
`chore:`. Example: `report: draft methodology chapter (02)`.

---

## 8. Templates

Reusable forms live in [`templates/`](templates/):

- [`report-chapter.md`](templates/report-chapter.md) — new report chapter skeleton.
- [`evidence-entry.md`](templates/evidence-entry.md) — evidence index entry.
- [`hypothesis.md`](templates/hypothesis.md) — hypothesis registry entry.
- [`experiment.md`](templates/experiment.md) — test/experiment record (see
  [methodology §5](report/02-methodology.md#5-experiment-documentation)).
