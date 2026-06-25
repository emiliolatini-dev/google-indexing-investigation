# Google Indexing Investigation — fotomoto.click

A rigorous, evidence-based technical investigation into the Google Search
indexing behavior observed for the website `https://fotomoto.click`.

> **Status:** scaffolding established — investigation content not yet authored.
> **Report version:** see [CHANGELOG.md](CHANGELOG.md).

---

## Purpose

A significant portion of the site's URLs remains in the Google Search Console
state **"Crawled — currently not indexed"**, despite passing the standard set of
technical checks (robots.txt, valid XML sitemaps, HTTP 200 responses, correct
canonicals, confirmed Googlebot access, verified redirects, verified CDN
configuration, and no intentional crawl blocks).

The goal of this repository is **not** to prove that Google has a bug. The goal
is to **document an observed anomaly through verifiable evidence**, following an
engineering and scientific method, so that any reader can reconstruct the entire
investigation.

The intended audience includes Google Search Quality Engineers, Google Search
Central Product Experts, WordPress developers, and technical SEO specialists.

---

## Epistemic discipline

Every substantive statement in this repository belongs to exactly one category.
The summaries below are quick-reference; the **canonical definitions** live in
[report/02-methodology.md §2](report/02-methodology.md#2-claim-classification).

| Category | Summary |
|----------|---------|
| **FACT** | A demonstrated statement that follows directly from evidence. |
| **OBSERVATION** | A recorded behavior in a specific instance, asserting no cause. |
| **ASSUMPTION** | An unproven premise accepted to proceed, made explicit. |
| **HYPOTHESIS** | A possible explanation, not yet verified. |
| **QUESTION** | An open question. |

Hypotheses are always explicitly labeled as such. The tone is neutral, never
accusatory, never sensationalist, never oriented toward proving a thesis. The
guiding principles are in
[INVESTIGATION_PRINCIPLES.md](INVESTIGATION_PRINCIPLES.md); the full operating
rules are in [report/02-methodology.md](report/02-methodology.md).

---

## Repository layout

```
.
├── report/      The white paper, in independent, reviewable chapters
├── evidence/    Raw, immutable, dated evidence (referenced — never pasted into the report)
├── registry/    Structured tracking: claims, assumptions, hypotheses, questions, decision log
├── data/        Derived, analyzable datasets (cleaned CSV/JSON)
└── templates/   Templates for evidence entries, hypotheses, experiments, and report chapters
```

The architecture enforces a strict separation of three layers:

1. **Evidence** — raw data, immutable. → [`evidence/`](evidence/)
2. **Analysis** — interpretation of evidence. → [`report/`](report/)
3. **Tracking** — claims, assumptions, hypotheses, questions, decisions. → [`registry/`](registry/)

Analysis never embeds raw data; it **references** it by evidence ID. Evidence
never embeds interpretation.

---

## How to navigate

- Start with [`report/00-executive-summary.md`](report/00-executive-summary.md).
- Read chapters in order; each declares its own status, version, and dependencies.
- Follow any `F-`, `O-`, `A-`, `H-`, `Q-`, or `E-` identifier back to its
  definition in [`registry/`](registry/) or [`evidence/`](evidence/).

---

## Identifier system

| Type | Prefix | Lives in |
|------|--------|----------|
| Fact | `F-` | [`registry/claims.md`](registry/claims.md) |
| Observation | `O-` | [`registry/claims.md`](registry/claims.md) |
| Assumption | `A-` | [`registry/assumptions.md`](registry/assumptions.md) |
| Hypothesis | `H-` | [`registry/hypotheses.md`](registry/hypotheses.md) |
| Question | `Q-` | [`registry/questions.md`](registry/questions.md) |
| Evidence | `E-` | [`evidence/`](evidence/) |

**Traceability rule:** every `F-` and every `O-` must cite at least one `E-`.
Experiments are procedures and have **no identifier**; they are tracked through
the `E-`/`O-`/`H-` items they produce (see
[methodology §5](report/02-methodology.md#5-experiment-documentation)).

---

## Contributing & review

This investigation is peer-reviewed continuously. See
[CONTRIBUTING.md](CONTRIBUTING.md) for conventions, the review workflow, and the
review checklist.

## License

See [LICENSE](LICENSE).
