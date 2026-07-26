# Evidenze GSC + GA4 — 26-07-2026

> Addendum a [2026-07-26-master-audit.md](2026-07-26-master-audit.md).
> **Fonte:** accesso diretto a Google Search Console (proprietà dominio `fotomoto.click`) e Google Analytics 4 (proprietà `fotomoto.click`, ID `352067879` / stream `485548121`) tramite sessione browser autenticata dell'utente, il 26-07-2026.
> Tutti i valori in questo file sono **[MISURATO]** salvo dove indicato.

---

## 1. 🔴 SCOPERTA CRITICA — Google non riesce a leggere le sitemap

Report **Indicizzazione → Sitemap**:

| Sitemap | Inviata | Ultima lettura | Stato | Pagine rilevate |
|---|---|---|---|---|
| `https://fotomoto.click/sitemap_index.xml` | 3 lug 2026 | **(vuoto)** | **Impossibile recuperare** | **0** |
| `https://fotomoto.click/page-sitemap1.xml` | 25 giu 2026 | **(vuoto)** | **Impossibile recuperare** | **0** |

**Il campo "Ultima lettura" è vuoto per entrambe. Google non ha mai completato con successo una lettura delle sitemap inviate.**

Questo è un fatto nuovo, non presente nel registry dell'investigation, e ha priorità su tutto il resto della roadmap.

### Perché è coerente con il resto delle evidenze

- Le sitemap dichiarano ~55.360 URL. Google ne conosce **16.869** (4.822 indicizzate + 12.047 non indicizzate). **~38.500 URL non sono mai entrati nel sistema di Google.**
- F-009 del registry documenta GET di Googlebot su `sitemap_index.xml` il 24/06/2026 dai log LiteSpeed. **Un GET nei log non implica una lettura riuscita lato Google.** I due fatti non sono in contraddizione: la richiesta parte, la lettura fallisce.
- Non compare il motivo "Rilevata, attualmente non indicizzata" tra i 12 motivi di non indicizzazione: coerente con l'assenza di una coda di URL scoperti da sitemap.

### Ipotesi da verificare, in ordine di plausibilità

**H1 — Un filtro bot (Cloudflare/WAF) blocca il fetcher delle sitemap.** [IPOTESI, con evidenza indiretta]
Durante il crawl del 26-07-2026 il fetcher di Claude ha ricevuto **HTTP 403** su `https://fotomoto.click/` e su `https://fotomoto.click/sitemap_index.xml`, mentre `robots.txt` rispondeva 200 e `curl` con user-agent browser riceveva 200 su tutto. Esiste quindi **una regola che discrimina per user-agent/client e restituisce 403**. GSC riporta inoltre 7 pagine "Bloccata a causa di un accesso non autorizzato (403)".
→ Verifica: GSC → Controllo URL su `sitemap_index.xml`; log LiteSpeed/Cloudflare filtrati per user-agent Googlebot e status 403; Cloudflare → Security Events.

**H2 — Timeout sul fetch.** L'index dichiara 244 sotto-sitemap ed è servito con `Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private` (misurato). Nessuna cache + generazione dinamica + 244 file può eccedere i limiti di tempo del fetcher.

**H3 — Errore 5xx intermittente.** GSC riporta 7 URL con errore server.

### Azione

**Priorità assoluta, prima di qualsiasi altro intervento SEO.** Finché la sitemap non è leggibile, ogni ottimizzazione a valle è cieca.

1. GSC → Controllo URL su `https://fotomoto.click/sitemap_index.xml` → "Testa URL pubblicato" → leggere la risposta esatta.
2. Cloudflare → Security Events: cercare 403/challenge su user-agent Googlebot.
3. Verificare che non ci sia una Managed Rule / Bot Fight Mode attiva che sfida i crawler.
4. Rimuovere `no-store, private` dall'header della sitemap.
5. Reinviare e verificare che "Ultima lettura" si popoli.

---

## 2. Indicizzazione — il verdetto di Google

Report **Indicizzazione → Pagine**, ultimo aggiornamento 10/07/2026.

| | Pagine |
|---|---|
| **Indicizzate** | **4.822** |
| **Non indicizzate** | **12.047** (12 motivi) |
| Totale noto a Google | 16.869 |
| Dichiarate nelle sitemap | ~55.360 |
| **Mai entrate nel sistema** | **~38.500** |

### I 12 motivi di non indicizzazione

| Motivo | Sorgente | Pagine | Convalida |
|---|---|---|---|
| **Pagina scansionata, ma attualmente non indicizzata** | **Sistemi di Google** | **5.640** | Non iniziata |
| Esclusa in base al tag "noindex" | Sito web | 2.442 | Non iniziata |
| Pagina con reindirizzamento | Sito web | 1.176 | Non iniziata |
| **Non trovata (404)** | Sito web | **1.129** | **Non riuscita** |
| Pagina alternativa con tag canonical appropriato | Sito web | 766 | Non iniziata |
| Pagina duplicata senza URL canonico selezionato dall'utente | Sito web | 759 | Non iniziata |
| Pagina duplicata, Google ha scelto una canonica diversa | Sistemi di Google | 117 | Non iniziata |
| Errore del server (5xx) | Sito web | 7 | Non iniziata |
| Bloccata a causa di un accesso non autorizzato (403) | Sito web | 7 | Non iniziata |
| Errore di reindirizzamento | Sito web | 2 | Non iniziata |
| Bloccata a causa di un altro errore 4xx | Sito web | 1 | Non iniziata |
| Bloccata da robots.txt | Sito web | 1 | Non iniziata |

### "Pagina scansionata, ma attualmente non indicizzata" — 5.640

Data primo rilevamento: **01/07/2025**. Sorgente: **Sistemi di Google** — cioè non è un errore del sito: è **una decisione di Google**. Ha scaricato queste pagine e ha stabilito che non meritano l'indice.

Esempi restituiti da GSC (ultima scansione 11 lug 2026):

```
/foto/bocca-serriola/fotomotoclick-bocca-serriola-29-06-2025-94-11-18/     prodotto
/foto/bocca-serriola/fotomotoclick-bocca-serriola-09-08-2025-686-11-58/    prodotto
/foto/bocca-serriola/fotomotoclick-bocca-serriola-19-07-2025-288-11-25/    prodotto
/foto/bocca-serriola/11-07-2026/                                          ← LANDING SESSIONE
/foto/26-04-2026-3/fotomotoclick-spino-26-04-2026-84-10-59/               prodotto con path-data rotto
```

**Una landing di sessione è in questo elenco.** È la conferma diretta e documentale della tesi §0 del master audit: il problema di indicizzazione delle sessioni non è di trasporto (sitemap/cache), è di **giudizio di qualità**. Google le vede, le scarica, e decide di lasciarle fuori.

### Correlazioni con i difetti già documentati

| Motivo GSC | Difetto corrispondente nel master audit |
|---|---|
| 5.640 scansionate non indicizzate | §2.2 pagine vuote, §2.3 landing sessione senza contenuto, §4.6 thin content |
| 2.442 noindex | §2.3 `/page/N/` infinite + carrello/checkout |
| 1.176 reindirizzamento | §3.3 link a 301, `/gallerie-foto/` in sitemap |
| **1.129 404 con convalida NON RIUSCITA** | non ancora diagnosticato — **nuovo, da investigare** |
| 766 + 759 + 117 duplicati | §2.1 path-data rotti, §4.6 duplicazioni |

---

## 3. Rendimento — ricerca Web (3 mesi, 25/04–24/07/2026)

| Metrica | Valore |
|---|---|
| Clic totali | **9.960** |
| Impressioni | 51.300 |
| CTR medio | 19,4% |
| Posizione media | 5,3 |
| Query distinte | **613** |

### Pagine principali

| Pagina | Clic | Impr. | Quota clic |
|---|---|---|---|
| `https://fotomoto.click/` | **7.028** | 46.498 | **70,6%** |
| `/foto/bocca-serriola/` | 1.063 | 5.615 | 10,7% |
| `/foto/terminillo/` | 754 | 2.105 | 7,6% |
| **`/gallerie-foto/` (fa 301!)** | **426** | 3.794 | 4,3% |
| `/passi-e-valichi/` | 286 | 4.094 | 2,9% |
| `/foto/viamaggio/` | 176 | 559 | 1,8% |
| **Totale primi 6** | **9.733** | | **97,7%** |

⚠️ **`/gallerie-foto/` riceve 426 clic e 3.794 impressioni pur essendo un redirect 301.** Il master audit la trattava come semplice spreco di link interni: in realtà è la **quarta pagina del sito per traffico organico** e ogni utente che ci arriva fa un salto in più. Va ripristinata come pagina reale o il redirect va reso irrilevante spostando il posizionamento su `/passi-e-valichi/`.

### Query principali

| Query | Clic | Impr. | Tipo |
|---|---|---|---|
| foto bocca serriola | 493 | 1.372 | località |
| **fotografi moto sui passi** | **406** | **2.855** | **categoria, non-brand** |
| fotomotoclick | 383 | 429 | brand |
| foto moto click | 369 | 457 | brand |
| fotomoto clik | 300 | 327 | brand |
| fotomoto | 146 | 423 | brand |
| fotografo bocca serriola | 139 | 447 | località |
| foto bocca serriola oggi | 110 | 347 | località |
| foto moto terminillo | 108 | 257 | località |
| foto moto bocca serriola | 103 | 267 | località |

**Due letture importanti.**

1. **`fotografi moto sui passi`: 406 clic, 2.855 impressioni.** È una query di **categoria**, non di brand e non di località — e la stai già intercettando bene. È la prova empirica che il cluster proposto in §13.6 (directory dei fotografi dei passi) ha domanda reale e che sei già competitivo su di esso. **Questa query da sola giustifica quel progetto.**
2. **Solo 613 query distinte in 3 mesi.** Per un sito con 55.000 URL è un dato bassissimo: conferma che l'ampiezza semantica del sito è vicina a zero e che tutto il traffico passa da una manciata di intenti.

### Verifica decisiva per la deindicizzazione (§3.1 e §13.1 del master audit)

Filtro: pagine il cui URL contiene `/fotomotoclick-` (prodotti + pagine vuote).

| Tipo di ricerca | Clic | Impressioni | CTR | Posizione |
|---|---|---|---|---|
| **Web** | **55** | 3.760 | 1,5% | 4,9 |
| **Immagine** | **0** | 747 | 0% | 32,3 |

**55 clic su 9.960 = 0,55% del traffico organico**, prodotti da **53.683 URL**.

> ⚠️ Nota di metodo: un primo tentativo di applicare il filtro tramite parametro URL (`&page=*fotomotoclick-*`) ha restituito 0 clic. È un **falso negativo**: GSC interpreta quel parametro come corrispondenza letterale, asterischi inclusi. I valori riportati qui sono ottenuti impostando il filtro "URL che contengono" dall'interfaccia, con controllo di sanità su un pattern a traffico noto.

**Verdetto: la raccomandazione §3.1 (curated indexing, opzione B) è confermata dai dati.** Il costo della deindicizzazione è ~0,55% dei clic. Il beneficio è la concentrazione del crawl budget e la rimozione di 5.640 pagine giudicate negativamente da Google.

Due cautele emerse dai dati e assenti dal master audit:

- Le pagine prodotto in ricerca Web hanno **posizione media 4,9**: quando emergono, emergono in alta posizione. Sono probabilmente query di navigazione da parte di chi ha già il link. Il curated indexing (mantenere 20-40 foto/sessione) le preserva.
- **Esiste un terzo pattern URL non presente in nessuna sitemap analizzata:** `https://fotomoto.click/gallerie/fotomotoclick-*`. Riceve clic (5, 3, 3, 2 sulle singole URL) ed è duplicato del pattern `/foto/{località}/fotomotoclick-*`. **Da investigare: due URL pubblici per la stessa foto spiegherebbero i 759 + 117 duplicati del report di indicizzazione.**

---

## 4. Rendimento — Google Immagini (3 mesi)

| Ambito | Clic | Impressioni | CTR | Posizione |
|---|---|---|---|---|
| **Tutto il sito** | **29** | 2.200 | 1,3% | 26,7 |
| di cui homepage | 24 | 1.678 | — | — |
| Pagine `/fotomotoclick-` | **0** | 747 | 0% | 32,3 |

**Un'attività che possiede 53.683 fotografie professionali riceve 29 clic da Google Immagini in tre mesi, e 24 di questi arrivano dalla homepage.** L'intero archivio ne produce 5.

Posizione media 26,7 = terza pagina dei risultati immagini.

È la validazione empirica di due diagnosi del master audit:
- **§4.5** — `alt` duplicati e privi di contenuto descrittivo: senza testo, le immagini non si posizionano;
- **§13.3** — il canale Google Images è completamente inespresso, ed è esattamente quello dove il badge "Licenziabile" (IPTC + `acquireLicensePage`) opera.

**Questo dato alza la priorità dell'azione 22 della roadmap da P0 a "prima azione di contenuto dopo il fix delle sitemap".**

---

## 5. Core Web Vitals (CrUX, aggiornato 25/07/2026)

| Dispositivo | URL buoni | Richiedono miglioramenti | Scadenti |
|---|---|---|---|
| **Mobile** | **0** | **42** | 0 |
| Desktop | 42 | 0 | 0 |

**Regressione mobile netta intorno al 30/05/2026:** il grafico mostra il passaggio da ~20 URL "buoni" a 0, con l'intero gruppo che migra in arancione e ci resta fino a oggi.

Coerente con il TTFB di **2,16 s** misurato sulla landing sessione in cache MISS (§4.3 del master audit). Desktop resta verde perché soffre meno il TTFB.

**Da investigare:** cosa è cambiato intorno al 30/05/2026. È una data precisa e verificabile contro il changelog dei deploy.

Questo colma il buco dichiarato in §2.5 del master audit: i CWV di campo **esistono** e dicono che **il mobile è fuori soglia al 100%**.

---

## 6. GA4 (proprietà `fotomoto.click`)

### Ultimi 30 giorni

| Metrica | Valore | Variazione |
|---|---|---|
| Visualizzazioni | 11.000 | **−21,1%** |
| Utenti attivi | 1.500 | **−24,6%** |
| Quantità | 3.800 | −5,2% |
| **Acquisti** | **95** | **+13,1%** |
| Tasso di acquirenti | 5,5% | +3,2% |

Lettura: **il traffico cala di un quarto ma le conversioni salgono.** Il sito sta convertendo meglio su meno traffico — coerente con un problema di *acquisizione*, non di prodotto. Il collo di bottiglia è a monte, ed è quello che l'audit descrive.

### Canali (ultimi 7 giorni, sessioni)

| Canale | Sessioni | Var. |
|---|---|---|
| Direct | 279 | +82,4% |
| Organic Search | 258 | +33,7% |
| Paid Social | 148 | −1,3% |
| Organic Social | 135 | +62,7% |
| Unassigned | 10 | +100% |
| Referral | **3** | 0% |
| **AI Assistant** | **2** | −66,7% |

Tre osservazioni.

1. **Referral: 3 sessioni.** Conferma quantitativa dell'assenza totale di backlink attivi (§2.5 e §13.7 del master audit). Non è una stima: è il dato.
2. **AI Assistant: 2 sessioni.** GA4 ha una categoria dedicata al traffico da assistenti AI, e tu ne ricevi 2 a settimana. È la misura diretta del costo del blocco a `GPTBot`/`Google-Extended` in robots.txt (§11.1).
3. **Direct 279 > Organic Search 258.** Su un sito la cui SEO è per il 70% brand, "Direct" è in larga parte brand anch'esso: la quota di acquisizione realmente *nuova* è piccola.

---

## 7. Impatto sulla roadmap del master audit

### Nuova azione, priorità sopra tutte

| # | Azione | Imp. | Diff. | Tempo | Pri |
|---|---|---|---|---|---|
| **0** | **Diagnosticare e risolvere "Impossibile recuperare" sulle sitemap** (WAF/403, header no-store, timeout su 244 file) | **10** | 3 | 1-2 g | **P0 assoluta** |

### Riordinamenti confermati dai dati

- **Azione 22 (IPTC + Licenziabile)** — sale di priorità: 29 clic da Google Immagini in 3 mesi è il canale più sottoutilizzato del progetto.
- **Azione 21 (curated indexing)** — confermata: costo misurato 0,55% dei clic.
- **Azione 30 (directory fotografi dei passi)** — confermata da `fotografi moto sui passi`: 406 clic e 2.855 impressioni già oggi.
- **Azione 4 (link a `/gallerie-foto/`)** — **cambia natura**: non è solo igiene di link interni, è la 4ª pagina per traffico organico che gira su un 301.
- **Azione 24 (cache HTML Cloudflare)** — confermata: mobile CWV 0/42 buoni dal 30/05/2026.

### Nuove voci da investigare

| # | Voce | Priorità |
|---|---|---|
| N1 | 1.129 pagine **404 con convalida NON RIUSCITA** — origine sconosciuta | P0 |
| N2 | Pattern URL `/gallerie/fotomotoclick-*` non presente nelle sitemap: duplicato di `/foto/{loc}/fotomotoclick-*`? | P0 |
| N3 | Cosa è cambiato il **30/05/2026** che ha fatto collassare i CWV mobile | P1 |
| N4 | Perché `/gallerie-foto/` è diventata un 301 pur avendo 426 clic organici | P1 |

---

## 8. Note di metodo

- Accesso in sola lettura. Nessuna modifica effettuata su GSC o GA4: nessun invio di sitemap, nessuna richiesta di indicizzazione, nessuna convalida di correzione avviata, nessuna impostazione toccata.
- L'intervallo "3 mesi" è quello predefinito di GSC (25/04/2026 – 24/07/2026). I dati a 16 mesi non sono stati estratti in questa sessione.
- Un filtro applicato via parametro URL ha prodotto un falso negativo, individuato con un controllo di sanità e corretto (§3). I dati filtrati riportati qui provengono tutti dall'interfaccia.
