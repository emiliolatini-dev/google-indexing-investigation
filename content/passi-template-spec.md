# Template dinamico `/passi/{loc}/` — spec campi + data layer

> Come scalare la pagina passo (mockup: `content/passi-bocca-serriola/`) a tutte le località
> **senza** creare thin/duplicate. Rif. master audit §3.2, §6.2. Dati in `passi-data.json`.

## Principio
Un **solo template dinamico** + un **data layer per località**. La struttura (layout, sezioni,
schema, FAQ scaffold, CTA, breadcrumb, galleria) è identica e templatizzata; la **sostanza**
(fatti geografici, profili, dintorni, dati d'archivio) viene da campi per-passo. Se il template
si limitasse a sostituire il nome, otterresti 5 pagine quasi identiche = thin content. La
differenziazione reale la danno i campi "curati" + i numeri d'archivio esclusivi.

## I tre livelli dei campi

### A · AUTO / dinamico — zero lavoro per località
| Campo | Fonte |
|---|---|
| Galleria immagini | `wp-content/uploads/fm-hero/{slug}/desktop/hero-desktop-NNN.webp` (verificato: esiste per tutte e 5 le località — Bocca Serriola 23, altre 5) |
| Conteggio foto | count prodotti in `/foto/{slug}/` (dall'archivio) |
| Breadcrumb, CTA, og:image | template ({slug}) |
| Schema scaffold | template + campi curati |
| FAQ generiche | template con interpolazione `{nome}` (es. "C'è un fotografo a {nome}?") |
| Link interni | template → `/foto/{slug}/`, `/cerca-foto-archivio/`, `/dove-siamo/` |

### B · CALCOLATO dal dataset — zero lavoro per località (stessa query)
| Campo | Query |
|---|---|
| Ora di punta dei passaggi | GROUP BY ora sui metadati foto del passo |
| Weekend più affollato | GROUP BY data, MAX conteggio |
| Primo/ultimo weekend di stagione | MIN/MAX data sessioni |
> ⚠️ Non pubblicare valori non calcolati. Finché la query non è pronta, nascondi la sezione
> "in numeri" (o mostra solo il conteggio foto). Mai numeri inventati (integrità [MISURATO]).

### C · CURATO una volta per località — il differenziatore (~2-3 h ricerca/passo)
Campi in `passi-data.json`: `quota_m`, `strada`, `regioni`, `province`, `comuni`, `spartiacque`,
`catena`, `coordinate` (da verificare su Maps/OSM), `versanti[]` (km, dislivello, pendenza,
tornanti), `carattere` (2-4 frasi uniche sul passo), `dintorni`, `punto_foto`, `note_stagionalita`,
`fonti[]`. **Già pre-compilati per tutte e 5 le località** con ricerca sorgente (vedi JSON).

## Implementazione WordPress
- **Custom Post Type `passo`** (o gruppo ACF sulla tassonomia località esistente) con i campi del
  livello C. Un record per passo.
- **Template `single-passo.php`** (o template Elementor dinamico) che renderizza: campi C +
  galleria/conteggio (A) + sezione numeri (B, se pronta).
- **URL:** `/passi/{slug}/` — nuovo livello **informativo**, che *affianca* `/foto/{slug}/`
  (transazionale, già ottimo). NON fondere i due: intento diverso, così non si cannibalizzano.
  Interlink reciproco.
- **Schema:** `Place`/`Mountain`/`TouristAttraction` + `FAQPage` + `BreadcrumbList` (vedi mockup).
  Per Terminillo usare `Mountain` (vetta) + `SkiResort` opzionale; è un massiccio, non un valico.

## Rischio duplicati — soglia
A **5 località** con geografia genuinamente diversa + numeri d'archivio esclusivi, il rischio
thin/duplicate è basso. Diventa reale solo scalando ai ~60 passi dell'espansione (§6.3-E) con solo
testo templatizzato: lì ogni pagina deve avere dati veri suoi, altrimenti è thin. Regola: **una
pagina passo esiste solo se ha almeno 5 dati oggettivi unici + contenuto d'archivio reale.**

## Ordine consigliato
1. Bocca Serriola (fatto — è l'89% dell'archivio e la SERP persa).
2. Terminillo (entità forte, molto cercata).
3. Viamaggio (oggi non ha nemmeno un path prodotto proprio — §2.1).
4. Spino, Capannelle.
