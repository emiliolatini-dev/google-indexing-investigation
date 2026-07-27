# FotoMoto.Click — Piano operativo SEO

> **Data:** 27-07-2026
> **Basi:** [master audit 26-07](2026-07-26-master-audit.md) · [evidenze GSC/GA 26-07](2026-07-26-gsc-ga-evidence.md) · [E-015 lettura incrociata 27-07](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md)
> **Regola di ingaggio:** nessuna modifica su sito, GSC, GA4 o Meta senza conferma esplicita, una per volta.

---

## 0. Cosa è cambiato rispetto al piano del 26 luglio

Il master audit resta valido nella tesi di fondo — troppi URL, troppo poco segnale. Ma la
lettura incrociata di GSC e GA4 del 27/07 sposta tre cose:

1. **La misurazione non è affidabile, e questo viene prima della SEO.** GA4 registra 87
   acquisti a fronte di ~78 `begin_checkout`: un funnel in cui si compra più di quanto si
   entri in cassa non è un funnel, è uno strumento rotto. Ogni scelta di priorità basata su
   ROI misurato è, oggi, basata su numeri che sappiamo essere sbagliati **per difetto**.
2. **Le tre incognite N1, N2 e N4 erano la stessa cosa** e ora hanno un nome: il namespace
   legacy `/gallerie/`. 1.129 404, richieste andate in timeout a 34 secondi, e una catena di
   due redirect sulla radice.
3. **Esistono due pagine che raccolgono 47.121 impressioni senza servire alcuna intenzione**
   — `/gallerie-foto/` (un redirect) e `/contatti/` — con CTR combinato 1,7% contro il 16,1%
   del sito. Non erano nella lista dei quick win, e valgono più di quasi tutti quelli in lista.

Il P0 sitemap resta bloccato, ma il campo si è ristretto: il file è sano, la rete regge
(3 richieste fallite su 37.003 in 90 giorni), l'header è corretto, Bing lo legge.

---

## Fase 0 — Rimettere in sesto la misurazione *(2-3 giorni)*

Prerequisito, non parallelo. Motivo: il resto del piano si ordina per ROI, e il ROI oggi
non è calcolabile.

| # | Azione | Dove | Reversibile |
|---|---|---|---|
| 0.1 | **Riconciliare GA4 con WooCommerce** sugli stessi 28 giorni: numero ordini e ricavo lordo reali contro 87 / 1.238,00 €. Il delta è la misura del problema. | WP admin | sola lettura |
| 0.2 | **Verificare a runtime** pixel Meta e GA4 con un carrello reale: osservare `facebook.com/tr` e `google-analytics.com/g/collect` su prodotto → carrello → checkout. Stabilisce se `InitiateCheckout` e `Purchase` esistono davvero. | browser | sola lettura |
| 0.3 | **Esclusioni referral** in GA4 per il dominio del gateway di pagamento. Candidato n.1 per "Unassigned" passato da ~10 a 177 sessioni in una settimana. | GA4 admin | sì |
| 0.4 | **Completare gli eventi e-commerce**: `begin_checkout` affidabile, più `view_cart`, `add_shipping_info`, `add_payment_info` oggi assenti. | sito | sì |
| 0.5 | **Evento `search` sul finder.** `/cerca-foto-moto/` è la pagina n.1 del sito (542 utenti, 3 m 59 s) e non emette l'evento standard né lato GA4 né lato Meta. | sito | sì |
| 0.6 | **Meta Conversions API** server-side da WooCommerce, per recuperare gli eventi che adblock e ITP cancellano lato browser. | WP + Meta | sì |

**Esito atteso:** un numero di ordini e un ricavo in GA4 che coincidono con WooCommerce entro
qualche punto percentuale, e un funnel monotòno decrescente.

---

## Fase 1 — Sbloccare la sitemap *(1 giorno di attesa fra i passi)*

| # | Azione | Nota |
|---|---|---|
| 1.1 | **Inviare `https://fotomoto.click/sitemap.xml`** — URL mai inviato prima, serve lo stesso identico index (31.615 byte, verificato). Lasciare in piedi `sitemap_index.xml` come controllo. | Esperimento discriminante: se il nuovo URL viene letto, il problema è uno stato bloccato lato GSC su quella voce; se fallisce anche lui, il problema è cosa Google riceve. |
| 1.2 | Se 1.1 fallisce: **log server filtrati** su richieste a file `.xml` con UA Googlebot, più Cloudflare → Security Events sullo stesso filtro. | È l'unico punto di osservazione che ci manca: cosa vede Google, non cosa vediamo noi. |
| 1.3 | Registrare l'esito come `H-007` in `registry/hypotheses.md` e come evidenza. | Il riferimento a H-007 esiste già in `hypotheses.md:110` ma l'ipotesi non è mai stata scritta. |

**Nota di realismo.** Anche risolto, il fix sitemap da solo non fa indicizzare le sessioni:
5.640 pagine sono "scansionate ma non indicizzate" per **decisione di Google**, non per mancata
scoperta. Serve la Fase 3.

---

## Fase 2 — Igiene del crawl *(settimana 1)*

Oggi il 17% delle richieste di Googlebot finisce in un redirect o in un 404, e un terzo va a
JS e CSS. Il crawl budget viene speso su niente.

| # | Azione | Evidenza |
|---|---|---|
| 2.1 | **Namespace `/gallerie/`**: decidere fra `410 Gone` (se i contenuti non esistono più) e redirect 1:1 verso il prodotto canonico (se esistono). Oggi rispondono 404 e alcuni sono andati in timeout a 34 s. | 1.129 pagine, convalida GSC fallita il 01/07 |
| 2.2 | **Accorciare la catena** `/gallerie/` → `/gallerie-foto/` → `/passi-e-valichi/` a un solo hop. | 12% del crawl in 301 |
| 2.3 | **Deindicizzare le 1.604 pagine vuote** `fotomotoclick-*` e toglierle dal page-sitemap. | master audit §2.2 |
| 2.4 | **robots.txt** dell'execution kit: via `AdsBot`, via il gruppo `Googlebot-Image`, disallow su `?add-to-cart`, `?remove_item`, carrello/checkout/account, `?orderby`, `/feed/`; sblocco crawler AI. | ✅ applicato 27/07 (`execution-kit/robots.txt` v2) |
| 2.4-bis | **Cache dei file di controllo.** [MISURATO 27/07] Il TTL cache browser di Cloudflare era **1 mese** e veniva applicato a `robots.txt`: ogni modifica poteva impiegare fino a 31 giorni ad arrivare a Google. Risolto con una Cache Rule dedicata (`Controllo SEO - robots.txt e sitemap sempre fresche`, ordine 3) che mette in **bypass** `/robots.txt` e ogni path contenente `sitemap` con estensione `.xml`. Esito verificato: `cf-cache-status` da `HIT` a `DYNAMIC`, `max-age=2678400` rimosso. **Resta da valutare** il TTL di LiteSpeed sugli stessi file (`x-litespeed-cache-control: public,max-age=604800`, 7 giorni): oggi richiede una purga manuale per URL a ogni modifica. | ✅ Cloudflare fatto · LiteSpeed aperto |
| 2.5 | **`/page/N/`** → 404 o canonical a page/1. | trappola infinita confermata |
| 2.6 | **Warmup cache** in coda a `generate-json`. | TTFB landing sessione 2,02 s a freddo |

---

## Fase 3 — Le pagine che hanno già domanda *(settimane 2-4)*

Qui non si crea domanda nuova: si smette di sprecare quella che arriva già.

| # | Azione | Perché ora | Cifra |
|---|---|---|---|
| 3.1 | **`/gallerie-foto/` come pagina reale**, non redirect. | 2ª pagina del sito per impressioni, gira su un 301 a 2 hop | 27.098 impr., CTR 3,4% |
| 3.2 | **Disambiguare `/contatti/`**: title, meta e contenuto che non competano su query generiche. | prende impressioni che non può convertire e cannibalizza la home | 20.023 impr., CTR **0,8%** |
| 3.3 | **Riscrivere il template landing sessione**: H1, 150-250 parole data-driven, `ItemList`, `Event`, selettore fascia oraria server-side. | è la pagina che Google scansiona e scarta, ed è viva lato utenti (2-3 min di permanenza) | 5.640 pagine scartate |
| 3.4 | **TTFB landing sessione** da 2,02 s a < 0,5 s (cache HTML + purge in pipeline). | CWV mobile 0 URL buoni su 41, desktop 41 su 41: è il TTFB | 0 / 41 |
| 3.5 | **Curated indexing**: 20-40 foto/sessione indicizzabili con caption unica, il resto `noindex` e fuori sitemap. | costo misurato della deindicizzazione: 0,55% dei clic | −52.000 URL |
| 3.6 | **H1, meta description sessioni, `alt` gallerie, `max-image-preview:large`, fix `SearchAction`.** | igiene, già specificata nell'execution kit | — |

---

## Fase 4 — Espandere sulla domanda misurata *(mesi 2-3)*

Non ipotesi di mercato: query che il sito **già intercetta oggi**.

| # | Azione | Domanda misurata su 16 mesi |
|---|---|---|
| 4.1 | **Hub `fotografi moto sui passi`** — è la query n.1 del sito, davanti a ogni termine di brand. | 1.304 clic, 6.681 impr., pos. 3,2 |
| 4.2 | **Pagine per i passi non coperti**: Cornello, Montezemolo, Bocca Trabaria, Campo Imperatore. Il sito ci si posiziona già senza averli. | 247 clic complessivi, pos. 4,9-8,7 |
| 4.3 | **Pagine `/passi/{p}/`** per i 5 passi coperti — il lavoro è già iniziato (Bocca Serriola + modulo "Scopri il passo" nel mu-plugin). | `fotografo passo delle capannelle`: 178 clic, pos. 7,0 |
| 4.4 | **IPTC + `license`/`acquireLicensePage`** → badge Licenziabile. | Google Immagini: 29 clic in 3 mesi su 53.683 foto |
| 4.5 | **Il dataset** (`/passi/{p}/dati/`, report annuale, `Dataset` schema). | l'unico asset non replicabile |

---

## Ordine di esecuzione, in una riga

**0 → 1 → 2 → 3 → 4.** La Fase 0 perché senza numeri veri non si decide; la 1 perché è
bloccante ma da sola non basta; la 2 perché libera il crawl; la 3 perché è dove sta il valore
già pagato e non incassato; la 4 perché è crescita, e la crescita si fa dopo aver smesso di
perdere.

---

## Cosa serve da Emilio, e cosa posso fare io

**Posso eseguire io, previa conferma puntuale:** invii e rimozioni di sitemap in GSC,
impostazioni GA4 (esclusioni referral, eventi chiave), lettura e incrocio dei report,
preparazione di tutti gli artefatti di codice (snippet, template, schema, robots).

**Serve Emilio:** accesso WooCommerce per la riconciliazione ordini, deploy sul sito
(WPCode/mu-plugin), Cloudflare per i log e le cache rule, Meta Business per la Conversions API.

**Non tocco senza richiesta esplicita:** nulla che modifichi ordini, prezzi, prodotti o
impostazioni di pagamento.
