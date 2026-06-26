# Observatory

> **Status:** active — dataset vivo.

L'Observatory è la componente **predittiva** del progetto. Dove l'indagine
(`report/`, `evidence/`, `registry/`) risponde a *"cosa è successo"*, l'Observatory
risponde a *"cosa sta succedendo e come cambia nel tempo"*.

A differenza di `evidence/` (immutabile per definizione), l'Observatory è
**mutabile**: cresce di una entry ad ogni sessione fotografica pubblicata.

---

## Perché esiste

Dopo 5–10 sessioni, il dataset diventa più potente di qualunque singola ipotesi.
Invece di affermare *"mi sembra che Google sia più veloce dopo la patch"*, sarà
possibile misurare:

> tempo mediano tra pubblicazione e primo crawl Googlebot
> **PRE patch** (`patch_active: false`) vs **POST patch** (`patch_active: true`).

Il campo `patch_active` è la variabile di confronto: separa le sessioni storiche
(20-06, 21-06) dalle sessioni post-patch.

---

## File

| File | Ruolo |
|------|-------|
| [`session-observatory.json`](session-observatory.json) | Dataset: una entry per sessione, timeline T0→T5. |
| [`schema.json`](schema.json) | JSON Schema (draft 2020-12) per validazione delle entry. |

---

## Timeline registrata per sessione

| Punto | Campo | Fonte |
|-------|-------|-------|
| T0 | `t0_published_at` | publish-session report |
| T1 | `t1_sitemap_ok_at` + `t1_sitemap_check_status` | `sitemap-check.sh` |
| T2 | `t2_google_sitemap_first_seen` | log LiteSpeed |
| T3 | `t3_google_landing_first_seen` | log LiteSpeed |
| T4 | `t4_google_product_first_seen` | log LiteSpeed |
| T5 | `t5_gsc_indexed_at` + `t5_gsc_indexed_confirmed` | GSC URL Inspection |

---

## Regole di aggiornamento

1. **`null` = evento non ancora osservato.** Distinguere sempre l'assenza di dato
   dall'assenza di evento. Non usare stringhe vuote o `0` come placeholder.
2. **Un'entry per sessione.** Identificata dallo slug `session`.
3. **I timestamp sono UTC** in formato ISO 8601 (`YYYY-MM-DDTHH:MM:SSZ`).
4. **`patch_active` è obbligatorio e immutabile** una volta pubblicata la sessione.
5. **Le `notes` restano neutre**: riferimenti a `F-/O-/E-/H-/Q-` dove pertinente,
   nessuna conclusione oltre l'evidenza.
6. **Append-only nello spirito**: si aggiungono entry e si raffinano i campi `null`
   man mano che gli eventi vengono osservati; non si riscrive la storia confermata.

---

## Relazione con l'indagine

L'Observatory **non sostituisce** il registry epistemico. È la fonte dati che
alimenterà la risposta a:

- [Q-002](../registry/questions.md#q-002): la prossima sessione post-patch verrà
  crawlata spontaneamente entro 24h?
- [Q-001a](../registry/questions.md#q-001a): Google ha aggiornato la crawl queue
  su uno snapshot sitemap incompleto?

L'esperimento controllato [T-002](../report/05-tests-performed.md) compila la
prima riga POST patch del dataset.

---

## Seed storico

Le sessioni 20-06-2026 e 21-06-2026 sono inserite come baseline **pre-patch** con
i dati ricostruiti dai log disponibili. I campi non ricostruibili restano `null`.
Questi due datapoint storici sono il termine di paragone PRE PATCH.
