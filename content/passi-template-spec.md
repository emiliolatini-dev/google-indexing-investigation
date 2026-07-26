# Contenuti passo per località — spec campi + data layer

> Come rendere ogni landing località `/foto/{loc}/` unica e utile con un **modulo "Scopri il passo"**,
> senza thin/duplicate.
> **Delivery scelta (26-07-2026):** modulo nella landing esistente (mockup `content/landing-info-module/`),
> NON pagine `/passi/` separate. Contenuto standalone di riferimento: `content/passi-bocca-serriola/`.
> Dati in `passi-data.json`. Rif. master audit §3.2, §6.2.

## Principio
Un **solo template dinamico** + un **data layer per località**. La struttura (layout, sezioni,
schema, FAQ scaffold, CTA, breadcrumb, galleria) è identica e templatizzata; la **sostanza**
(fatti geografici, profili, dintorni, dati d'archivio) viene da campi per-passo. Se il template
si limitasse a sostituire il nome, otterresti 5 pagine quasi identiche = thin content. La
differenziazione reale la danno i campi "curati" + i numeri d'archivio esclusivi.

## Regola editoriale (anti-ridondanza)
**Ogni fatto una volta sola, nella sua sede migliore.** I fatti duri (quota, strada, province,
coordinate) vivono nella **tabella dati**; la prosa (hero, intro) aggiunge contesto e non li
ripete. Niente ripetizioni di keyword per "compiacere i motori": Google non le premia da anni e
un lettore che conosce il passo le percepisce come riempitivo. Lo `stat strip` mostra i numeri
del *servizio/archivio* (foto fatte, tempi, risoluzione), non la geografia già in tabella.

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

## Implementazione WordPress — DECISIONE: modulo nella landing (non pagina separata)
Scelta architetturale (26-07-2026): invece di creare pagine `/passi/{slug}/` separate, si
**arricchisce la landing località esistente `/foto/{slug}/`** con un modulo "Scopri il passo".
Perché: a 5 località una sola pagina forte batte due pagine che si contendono lo stesso intento;
meno lavoro, nessuna cannibalizzazione, e i contenuti informativi atterrano dove c'è già autorità.
Le pagine `/passi/` restano un'opzione solo se in futuro si scala a molte località (§6.3-E).

- **Dati:** gruppo di campi **ACF** (livello C) agganciato alla tassonomia località, popolato da
  `passi-data.json`. Un record per località.
- **Template:** il modulo "Scopri il passo" nel template della landing `/foto/{slug}/`, **sotto**
  ricerca/sessioni/gallerie. Contenuto in **accordion `<details>`**: testo nell'HTML e crawlabile,
  **NON** modale JS (altrimenti Google non lo vede). Mockup: `content/landing-info-module/`.
- **Schema:** `Place`/`Mountain`/`TouristAttraction` + `FAQPage` sulla stessa landing. Per
  Terminillo `Mountain` (+ `SkiResort` opzionale): è un massiccio, non un valico.
- La landing resta **transazionale-prima**: ricerca e gallerie non si spostano.

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
