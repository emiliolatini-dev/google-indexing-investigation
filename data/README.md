# Data

Derived, **analyzable** datasets (cleaned CSV/JSON) produced from raw evidence.

> **Status:** empty — no datasets yet.

This folder holds **processed** data used for analysis (e.g. a normalized table
of URL → indexing state → last crawl). It is distinct from
[`evidence/`](../evidence/), which holds raw, immutable captures.

## Rules

- Every dataset must cite the evidence item(s) it is derived from (`E-NNN`).
- Document the transformation: which raw evidence, what cleaning/derivation steps,
  when, and with what tool/script.
- No sensitive data. Anonymize before committing.
- Prefer open, diff-friendly formats (CSV, JSON).

## Index

| Dataset | Derived from | Description | Added |
|---------|--------------|-------------|-------|
| _none yet_ | | | |
