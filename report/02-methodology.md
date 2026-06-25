# 02 — Methodology

> **Status:** in-review
> **Version:** 1.1
> **Last updated:** 2026-06-25
> **Depends on:** [INVESTIGATION_PRINCIPLES.md](../INVESTIGATION_PRINCIPLES.md)

---

## Purpose of this chapter

This chapter defines the operating rules of the investigation: how evidence is
collected, validated, classified, referenced, and reviewed. It is the canonical
source for the investigation's method. It documents *process*, not findings, and
contains no case-specific content.

[CONTRIBUTING.md](../CONTRIBUTING.md) restates the operational mechanics
(file naming, commit prefixes, the review checklist) as a contributor
quick-reference. Where the two overlap, **this chapter is authoritative for the
method**; CONTRIBUTING.md is authoritative for mechanics. Concept definitions are
not duplicated elsewhere; other documents link here.

The method rests on three principles:

1. **Separation of layers** — raw evidence, analysis, and tracking are kept
   distinct (see [README §Repository layout](../README.md#repository-layout)).
2. **Traceability** — every claim resolves to verifiable evidence.
3. **Falsifiability** — every explanation is stated so that it could, in
   principle, be disproven.

---

## 1. Evidence lifecycle

Evidence is any raw, verifiable artifact captured from a system or tool: an HTTP
response, a log excerpt, an export, a screenshot, a configuration dump.

### 1.1 Collection

- Evidence is captured directly from the source, with the least transformation
  possible. Raw output is preferred over summaries.
- Each capture records **how** it was obtained: the tool, the exact command or
  procedure, and the timestamp. See [§7 Reproducibility](#7-reproducibility-standard).
- Where a capture can be produced by a command, the command is stored verbatim so
  it can be re-run.
- Manual captures (e.g. console screenshots) record the steps taken instead of a
  command.

### 1.2 Identifier assignment

- Every evidence item receives a stable identifier `E-NNN`, zero-padded to three
  digits, assigned **sequentially** in order of registration.
- IDs are **never reused, renumbered, or recycled**, even if an item is later
  superseded.
- The identifier is recorded in the index of the relevant evidence category
  before the item is referenced by any claim.

### 1.3 Storage

- Evidence files live under [`evidence/<category>/`](../evidence/) using the
  naming convention:

  ```
  E-<id>_<YYYY-MM-DD>_<category>_<short-description>.<ext>
  ```

  Example: `E-021_2026-06-25_http_product-url-headers.txt`

- Categories are fixed: `http`, `robots-sitemap`, `gsc`, `rendering`, `crawl`,
  `network`, `screenshots`. Each category folder maintains an index
  (`README.md`) describing every item it contains, using
  [`templates/evidence-entry.md`](../templates/evidence-entry.md).
- An index entry states, for each item: date collected, category, file(s), what
  it demonstrates (one neutral sentence, no interpretation), collection method,
  reproducible command, collector, status, redaction status, and which claims
  reference it.

### 1.4 Immutability

Once committed, a raw evidence file is **never edited**. Immutability exists
because:

- the evidentiary value of a capture depends on it being exactly what the source
  produced at a known time;
- edits would break the chain between a claim and the artifact that supports it;
- reviewers (human or automated) must be able to inspect the original, not a
  curated version.

Derived or cleaned data is **not** evidence; it belongs under [`data/`](../data/)
and must cite the raw `E-` item(s) it was produced from.

### 1.5 Superseding

When a capture is later found to be incomplete, outdated, or corrected by a newer
observation, it is **superseded**, not replaced:

- a **new** evidence item is collected and assigned a new `E-` id;
- the original item's status is changed to `superseded by E-MMM` in its index
  entry (the only permitted change to an existing entry — a status pointer, not
  the content);
- the original file remains in the repository unchanged.

Superseding never deletes history. A reader can always trace why an item was
replaced and by what.

### 1.6 Evidence quality (provenance)

Every evidence item is classified by **provenance** — how far it sits from the
original source. Provenance is recorded in the evidence index entry.

| Provenance | Definition | Example |
|------------|------------|---------|
| **Primary** | A direct observation or original artifact, captured from the source with no interpretation. | A raw HTTP response capture; an unmodified server log excerpt; an original Search Console export. |
| **Secondary** | Derived directly from primary evidence **without interpretation** — a faithful extract, reformat, or decode. | A field extracted verbatim from a primary capture; a CSV transcribed 1:1 from an export. |
| **Derived** | The product of analysis, aggregation, or visualization built on primary/secondary evidence. | A computed rate; an aggregated table; a chart. Lives under [`data/`](../data/). |

How provenance influences **confidence** without changing the facts:

- Provenance does not change what an artifact *is*; a fact remains a fact
  regardless of how it was packaged. What it changes is how much weight a claim
  built on it can carry.
- **Primary** evidence supports the strongest claims. **Secondary** evidence is
  reliable but inherits any limitation of its primary source. **Derived**
  evidence depends entirely on the soundness of the transformation that produced
  it and must cite the primary/secondary items it was built from.
- A claim is only as strong as the weakest provenance in its chain. Confidence
  (see [§8](#8-confidence-assessment)) is bounded by the provenance mix of the
  evidence a chapter relies on.
- Provenance never *downgrades a fact into a non-fact*; it informs how confidently
  conclusions may be drawn, which is a separate axis from whether the underlying
  observation is true.

---

## 2. Claim classification

Every substantive statement in the report belongs to exactly one category and is
labeled with an inline badge. These are the canonical definitions; the
[GLOSSARY](../GLOSSARY.md) links here.

| Category | Badge | Definition | Evidence requirement |
|----------|-------|------------|----------------------|
| **FACT** | `[FACT · F-NNN]` | A demonstrated statement that follows directly from evidence and is not reasonably disputable given that evidence. | Must cite ≥1 `E-`. |
| **OBSERVATION** | `[OBSERVATION · O-NNN]` | A behavior that was observed and recorded, without asserting its cause or generality. | Must cite ≥1 `E-`. |
| **ASSUMPTION** | `[ASSUMPTION · A-NNN]` | A premise accepted as true so the investigation can proceed, without (or in advance of) direct evidence. | Must be stated explicitly; no evidence required, but its lack of evidence is the point. |
| **HYPOTHESIS** | `[HYPOTHESIS · H-NNN]` | A possible explanation that is not yet verified. Always provisional. | Must list supporting and contradicting evidence where available, plus a test plan. |
| **QUESTION** | `[QUESTION · Q-NNN]` | An open question. Implies no claim. | Must reference the claim(s) or hypothesis(es) that raise it. |

Rules:

- A **HYPOTHESIS is never written as if it were a FACT.** Provisional language and
  the `*(to be verified)*` marker are required until evidence resolves it.
- The distinction between FACT and OBSERVATION is deliberate: an OBSERVATION
  records *what was seen* in a specific instance; a FACT asserts something the
  evidence establishes. When in doubt, classify as OBSERVATION.
- If a value is unknown, it is a QUESTION — never an invented or assumed value.

### 2.1 ASSUMPTION — definition and use

An **ASSUMPTION** is a premise the reasoning *depends on* but does not itself
evidence. It exists so that the [principle of minimizing assumptions](../INVESTIGATION_PRINCIPLES.md)
can be enforced: assumptions are not forbidden, but every one that matters must be
made **visible** rather than left implicit.

How it differs from the other categories:

- **vs. FACT** — a FACT is backed by evidence; an ASSUMPTION is explicitly *not*
  (yet) backed by evidence. Labeling something an assumption is an admission that
  it is unproven.
- **vs. OBSERVATION** — an OBSERVATION is something that was seen and recorded; an
  ASSUMPTION is something *taken as given* without being observed.
- **vs. HYPOTHESIS** — a HYPOTHESIS is a candidate explanation that the
  investigation sets out to *test*; an ASSUMPTION is a premise the investigation
  *relies on* while testing something else. You test hypotheses; you depend on
  assumptions.

An assumption **must be documented explicitly** whenever:

- a claim, a test setup, or an interpretation would change if the premise were
  false; or
- a procedure relies on a condition that was not directly verified (e.g. that a
  fetch reached the intended origin rather than a cached layer).

Each assumption carries a falsification note (what would invalidate it) and is
periodically revisited: where possible it is converted into a FACT by collecting
evidence, or retired. An assumption is never silently promoted to a FACT.

Each category is tracked in its registry:
[`claims.md`](../registry/claims.md) (F-/O-),
[`assumptions.md`](../registry/assumptions.md) (A-),
[`hypotheses.md`](../registry/hypotheses.md) (H-),
[`questions.md`](../registry/questions.md) (Q-).

---

## 3. Traceability rules

Traceability binds every claim to evidence and every evidence item to the claims
that use it.

- **Every `F-` and every `O-`** must cite at least one `E-`. A claim without
  evidence is not admissible and must be downgraded to a `H-` or `Q-`.
- **Every `H-`** must cite its **supporting** evidence and its **contradicting**
  evidence where such evidence exists. If no contradicting evidence has been
  sought, the hypothesis must state what evidence *would* falsify it (its test
  plan). A hypothesis with no falsification path is not admissible.
- **Every `A-`** must state what would falsify it and be cited wherever a claim,
  test, or interpretation depends on it, so that no assumption is hidden.
- **Every `Q-`** must reference the related claim(s) or hypothesis(es) that give
  rise to it, so that no question is orphaned.
- **References are bidirectional.** When a claim cites `E-021`, the index entry
  for `E-021` lists that claim under "Referenced by". This lets a reviewer audit
  from either direction.
- **Single source of truth.** A claim is defined once, in its registry, and
  referenced elsewhere by relative link (e.g.
  `[F-012](../registry/claims.md#f-012)`). Content is never copy-pasted between
  documents.

---

## 4. Review workflow

Each chapter and registry entry advances through explicit states, declared in the
document header (`Status:`). The workflow adds an **adversarial review** step to
the three states defined in [CONTRIBUTING.md §6](../CONTRIBUTING.md#6-review-workflow).

| Stage | State | Purpose |
|-------|-------|---------|
| **1. Authoring** | `draft` | The author writes content, classifies each statement, and links evidence. May contain `TODO`. |
| **2. Technical review** | `in-review` | A reviewer checks correctness: do the cited evidence items actually support each claim? Are commands reproducible? Is the classification correct? |
| **3. Adversarial review** | `in-review` | A reviewer (human or automated, e.g. ChatGPT) actively attempts to falsify each claim and to find alternative explanations for each observation. Hypotheses are stress-tested against their falsification paths. |
| **4. Approval** | `stable` | After both reviews pass the [review checklist](../CONTRIBUTING.md#review-checklist), the document is frozen. |
| **5. Revision after new evidence** | back to `in-review` | New or superseding evidence reopens affected documents. The change is logged and re-reviewed before returning to `stable`. |

- Every review event (technical, adversarial, approval, reopening) is recorded in
  [`registry/decision-log.md`](../registry/decision-log.md): date, type, target,
  reviewer, outcome.
- Adversarial review is mandatory before approval. Its goal is not to defend the
  report's narrative but to break it; surviving claims are stronger for it.
- A `stable` document is not final — it is current. New evidence can always
  reopen it.

---

## 5. Experiment documentation

A test or experiment is any deliberate procedure run to confirm, measure, or
falsify something. **An experiment is a procedure, not a first-class entity:** it
has no identifier of its own. It is identified by what it produces — evidence
(`E-`), observations (`O-`), and possibly hypotheses (`H-`) — and by the
hypothesis or question it addresses. Each experiment is documented with a fixed
structure so it can be reproduced and audited. The reusable form is
[`templates/experiment.md`](../templates/experiment.md); narrated results live in
[05-tests-performed](05-tests-performed.md).

Every experiment records:

1. **Hypothesis or question under test** — the `H-`/`Q-` it addresses, or the
   specific claim it checks.
2. **Setup** — the environment, target, and preconditions.
3. **Controlled variables** — what was held constant and what was varied, so the
   result can be attributed.
4. **Test commands / procedure** — the exact, reproducible commands (or manual
   steps), verbatim.
5. **Expected result** — what outcome each possible interpretation predicts,
   stated *before* running where feasible.
6. **Actual result** — what was observed, linked to the `E-` item(s) it produced.
7. **Interpretation** — what the result supports or falsifies, stated within the
   limits of the evidence. No conclusion that exceeds the result.
8. **Limitations** — confounders, sample scope, timing effects, and anything that
   constrains how far the result generalizes.

An experiment that produces a result also produces evidence: the captured output
is registered as one or more `E-` items per [§1](#1-evidence-lifecycle). Any
behavior it reveals is recorded as an `O-`, and any explanation it suggests as an
`H-`. The procedure itself is never assigned an identifier; it is fully
reconstructable from the entities it produced.

---

## 6. Privacy and redaction policy

Evidence is collected and stored under a strict minimization rule: capture what
is needed to demonstrate a point, and nothing that exposes private data.

- **No personal customer data.** Names, emails, addresses, order data, or any
  personally identifiable information must not be committed. Redact before
  storing.
- **No private credentials.** API keys, tokens, passwords, session cookies, and
  verification strings must never appear in evidence, commands, or logs. Replace
  with a placeholder and note that redaction occurred.
- **No sensitive server details** that would aid an attacker — internal IP
  addresses, internal hostnames, non-public paths, and infrastructure secrets are
  redacted or anonymized. The collection procedure is described in anonymized
  form.
- **Search Console screenshots must be reviewed before publication.** Each GSC
  export or screenshot is inspected for account identifiers, property URLs that
  reveal private structure, verification tokens, and any PII, and redacted
  accordingly, before it is committed.
- **Redaction is recorded.** Each evidence entry states whether redaction was
  applied (`Sensitive data removed: yes / n/a`), so a reviewer knows the artifact
  was screened.
- Redaction must not alter the evidentiary meaning of a capture. If redaction
  would obscure what the evidence demonstrates, the item is reconsidered or
  described in prose rather than published.

---

## 7. Reproducibility standard

The investigation is reproducible to the extent that an independent reader can
re-run its procedures and obtain comparable results.

- **Commands are documented.** Every command-producible capture stores the exact
  command, including flags, verbatim. Manual procedures are documented step by
  step.
- **Dates and environment are included.** Each capture records the date (and time
  where relevant), and the environment it was taken from (e.g. client location
  class, network path) to the extent it can affect the result, subject to the
  redaction policy in [§6](#6-privacy-and-redaction-policy).
- **Tools and versions are listed where relevant.** When a result can depend on
  the tool used, the tool name and version are recorded (e.g. the HTTP client,
  the crawler, the browser/rendering engine).
- **Variability is acknowledged.** Where a procedure interacts with systems
  outside the investigator's control, results may vary over time; such procedures
  note this and, where possible, are repeated and dated.

---

## 8. Confidence assessment

Every report chapter ends with a short **Confidence Assessment** section. This is
a deliberate, standardized statement — not optional prose.

**What it measures.** Confidence here refers to the **quality and completeness of
the available evidence**, *not* to subjective certainty or how strongly anyone
believes the result. It answers: *how well does the evidence on the table support
what this chapter states?*

**Levels.**

| Level | Meaning |
|-------|---------|
| **High** | Claims rest on **primary** evidence, independently reproducible, corroborated by more than one item, with no material open questions or load-bearing assumptions. |
| **Medium** | Evidence is present and relevant but has gaps: partial reproduction, reliance on **secondary**/**derived** evidence, or open questions that do not undermine the core claims. |
| **Low** | Evidence is sparse, largely **derived**/**secondary**, not yet reproduced, or several load-bearing assumptions remain unresolved. |
| **Insufficient** | Not enough evidence to assess; the chapter holds observations and questions only, with no admissible facts. |

**How it is determined.** The level is bounded by, in combination:

- **Provenance mix** ([§1.6](#16-evidence-quality-provenance)) — more primary
  evidence permits higher confidence.
- **Reproducibility** ([§7](#7-reproducibility-standard)) — re-run and dated
  procedures raise confidence.
- **Corroboration** — independent evidence items pointing the same way.
- **Completeness** — unresolved `Q-` and load-bearing `A-` cap the level.

A chapter's confidence can never exceed what its weakest load-bearing evidence
allows. The assessment states the level and one short paragraph justifying it
against these factors. It changes the *expressed confidence*, never the
underlying facts.

---

## Referenced evidence

_None yet — this chapter defines method only._

## Related open questions

_None yet._

## Confidence Assessment

**Level: n/a (methodology).** This chapter defines process rather than making
evidentiary claims about the case, so the evidence-quality scale does not apply.
The framework it establishes is internally consistent and self-contained.

## Chapter changelog

| Date | Version | Change | Author |
|------|---------|--------|--------|
| 2026-06-25 | 0.1 | Skeleton created | |
| 2026-06-25 | 1.0 | Full methodology authored: evidence lifecycle, claim classification, traceability, review workflow, experiment documentation, privacy/redaction, reproducibility. | |
| 2026-06-25 | 1.1 | Framework hardening: removed experiment `T-` namespace (experiments are procedures); added ASSUMPTION (`A-`) category (§2.1); added evidence provenance (§1.6); added Confidence Assessment standard (§8). | |
