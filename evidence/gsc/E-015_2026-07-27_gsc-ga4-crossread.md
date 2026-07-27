# E-015 — Lettura incrociata GSC + GA4 + misure HTTP (27-07-2026)

- **Date:** 2026-07-27 (mattina)
- **Method:** accesso in sola lettura a Google Search Console (`sc-domain:fotomoto.click`) e
  Google Analytics 4 (`a352067879p485548121`) da browser autenticato; misure HTTP dalla rete
  dell'utente con `curl` (UA Googlebot e UA browser); ispezione del codice di tracciamento
  servito nell'HTML di home, landing sessione, prodotto, carrello, checkout, finder.
- **Demonstrates:** F-016, F-017, F-018, F-019, F-020, O-007, O-008; chiude N1, N2, N4;
  lascia aperta la causa di "Impossibile leggere".
- **Scritture effettuate:** nessuna. Nessun invio di sitemap, nessuna richiesta di
  indicizzazione, nessuna impostazione modificata su GSC o GA4.

---

## 1. Sitemap — stato al 27/07 e vettori esclusi

**Stato GSC.** Elenco Sitemap: 1 sola sitemap (`sitemap_index.xml`), Tipo **Sconosciuto**,
Inviata 27 lug 2026, colonna "Ultima lettura" **vuota**, Stato **"Impossibile recuperare"**,
0 pagine / 0 video. Pagina di dettaglio della stessa sitemap: **Ultima lettura 27/07/26**,
0 pagine, errore **"Impossibile leggere la Sitemap"**. `page-sitemap1.xml` non è più elencata
(rimossa il 26/07, cfr. E-013).

> Le due viste si contraddicono sulla colonna "Ultima lettura". Va citato lo stato di
> dettaglio, che è quello con il timestamp: Google ha eseguito una lettura fresca il 27/07
> e non è riuscita.

**Misure HTTP sull'index (UA Googlebot, rete utente).**

| Prova | Esito |
|---|---|
| `Content-Type` | `text/xml; charset=UTF-8` ✅ |
| `Cache-Control` | `public, max-age=300, must-revalidate` ✅ (fix E-014 attivo) |
| Dimensione | 31.615 byte in `identity`; 2.077 gzip; 1.471 br |
| Tempo | 0,21-0,31 s |
| `Accept-Encoding` testati | `gzip`, `br`, `gzip, deflate, br`, `identity`, assente → **tutti 200, tutti coerenti** |
| Struttura | 244 `<sitemap>`, tutti `https://fotomoto.click/…`, **0 host `www`**, 0 `http://` |
| Prologo | `<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet … main-sitemap.xsl?>` — nessun BOM |
| `page-sitemap1.xml` (sotto-sitemap) | 200, 0,22 s, `urlset` valido |
| `/sitemap.xml` | 200 — **serve lo stesso identico index** (31.615 byte) |

→ Nessun difetto di trasporto, formato o encoding rilevabile dalla rete dell'utente.

**Statistiche di scansione (90 gg, ultimo aggiornamento 25/07).**

- 37.003 richieste su `fotomoto.click` + 31 su `www.fotomoto.click`; stato host **"Nessun problema"**.
- Tempo medio di risposta **825 ms**.
- Per risposta: 200 **82%** · 301 **12%** · 404 **5%** · 304 <1% · altro 4XX <1% ·
  5XX <1% · 302 <1% · "Impossibile raggiungere la pagina" <1% (**2 richieste**) ·
  "DNS non risponde" <1% (**1 richiesta**).
- Per tipo di file (8 categorie): HTML 41% · JavaScript 19% · Altro tipo di file 13% ·
  CSS 13% · Immagine 4% · JSON 3% · Syndication 3% · Sconosciuto (richieste non riuscite) 5%.

→ **I fallimenti a livello di rete sono 3 richieste su 37.003.** L'ipotesi "Google non
riesce materialmente a raggiungere il file" non regge sui numeri. Nessuna categoria "XML"
compare fra i tipi di file, ma il bucket "Altro tipo di file" (13%) potrebbe contenerli:
**il dato non è conclusivo** sul numero di fetch di sitemap andati a buon fine.

**Conclusione.** Il file è valido e servito correttamente; la rete regge; l'header è
corretto (E-014); Bing legge la stessa sitemap (F-013). Restano due sole spiegazioni
plausibili, entrambe non verificabili dalla rete dell'utente:

- **(a)** Google riceve una risposta diversa da quella che riceviamo noi — un interstiziale
  o un errore servito da Cloudflare/LiteSpeed agli IP di Google sulle richieste `.xml`;
- **(b)** stato bloccato lato GSC su quella specifica voce sitemap.

### 1-bis. Esperimento eseguito (con autorizzazione) e riproduzione del 403

**Scrittura autorizzata da Emilio il 27/07:** inviata in GSC
`https://fotomoto.click/sitemap.xml` — URL **mai inviato prima**, che serve lo stesso
identico index — lasciando `sitemap_index.xml` in elenco come controllo.

**Esito, entro pochi secondi dall'invio:**

| Sitemap | Tipo | Inviato | Ultima lettura | Stato | Pagine |
|---|---|---|---|---|---|
| `sitemap_index.xml` | Sconosciuto | 27 lug 2026 | 27/07/26 | Impossibile leggere | 0 |
| **`sitemap.xml` (nuovo)** | Sconosciuto | 27 lug 2026 | **27/07/26** | **Impossibile leggere** | **0** |

→ **Ipotesi (b) — stato bloccato lato GSC su una specifica voce — RIGETTATA.** Un URL
vergine fallisce in modo identico e immediato.

**Riproduzione del 403 da IP datacenter (27/07).** Stesse URL richieste dalla rete
dell'utente e da un fetcher su IP datacenter:

| URL | Da rete utente (residenziale) | Da IP datacenter |
|---|---|---|
| `https://fotomoto.click/robots.txt` | 200 | **200** |
| `https://fotomoto.click/` | 200 | **403 Forbidden** |
| `https://fotomoto.click/sitemap.xml` | 200, XML valido | **403 Forbidden** |

→ **Esiste, ed è attiva oggi, una regola che restituisce 403 ai client su IP datacenter,
con `robots.txt` esplicitamente in eccezione.** È esattamente la firma osservata durante il
crawl del 26-07 (evidenze §1) e mai risolta.

**Perché questo non è in contraddizione con F-012 (Googlebot scarica l'HTML).**
Il crawl HTML avviene tramite il **Googlebot verificato**, che Cloudflare riconosce per
reverse DNS e lascia passare: 37.003 richieste, 82% di 200, live test URL Inspection
superato. Il recupero delle sitemap avviato da Search Console **non è necessariamente lo
stesso client verificato**. Se non lo è, ricade nella regola sopra e riceve 403 — che GSC
riporta come "Impossibile recuperare / Impossibile leggere".

### 1-ter. Cloudflare Security Events — H-007 NON confermata

Accesso in lettura al dashboard Cloudflare (zona `fotomoto.click`, piano **Free**,
account `1bc6860423a1cf3cb75847526a85d82e`), Sicurezza → Analytics → Eventi, ultime 24 ore.

**Filtro `ASN di origine = AS15169` (Google):**
→ **"Non è stato trovato un evento del firewall che corrisponda ai tuoi filtri".**

**Nessuna richiesta proveniente da Google è stata mitigata da Cloudflare nelle ultime 24 ore**,
compresa la finestra dei due invii sitemap del 27/07. **H-007 non è confermata: Cloudflare
non sta bloccando Google.**

**Filtro `Azione = Blocca`, ultime 24 ore** — tutti i blocchi provengono da **Regole gestite**:

| Ora (CEST) | Paese | IP | Note |
|---|---|---|---|
| 10:14:10 / 10:13:52 | Italy | 79.23.70.22 | IP residenziale |
| 10:07:25 · 09:34:16 · 08:27:20 · 07:54:09 · 07:20:57 | Singapore | 114.119.x.x | **PetalBot** (Huawei) |
| 09:33:27 ×3 | Germany | 204.10.194.139 | datacenter |
| 09:31:57 ×2 | United States | 198.74.57.41 | datacenter |
| 09:17:08 ×6 | Germany | 104.28.207.215/.219 | datacenter |
| **08:23:15 · 07:34:34 · 06:22:59** | United States | **2a03:2880:...** | **crawler Meta/Facebook** |
| 07:56:14 | Singapore | 47.128.126.68 | AWS |
| 06:57:16 ×2 | Netherlands | 45.148.10.62 | datacenter |

È attivo anche **AI Labyrinth**, che nelle ultime 24 ore ha servito contenuto-esca in modo
continuativo a un IP tedesco (`2a01:4f8:1c18:4d56::1`, Hetzner), ~25 richieste in 10 minuti.

**Due conseguenze.**

1. **Il 403 osservato in F-016 non è generato da Cloudflare** — non compare come evento di
   mitigazione. O proviene dalle Regole gestite senza essere campionato, oppure **è generato
   dall'origine** (LiteSpeed / plugin di sicurezza WordPress / ModSecurity sul VPS). Questo
   sposta l'indagine sul server, non sul CDN.
2. **Il crawler di Meta (`2a03:2880::/32`) viene bloccato dalle Regole gestite**, almeno 3
   volte nelle ultime 24 ore. È un candidato diretto per lo stato del dataset Meta descritto
   in §6-bis ("Nessun sito web trovato"): se Meta non riesce a raggiungere il sito, non può
   verificare il dominio né associarlo al pixel. **Correlazione plausibile, da confermare.**

**Stato di H-007:** *non confermata sul vettore Cloudflare*. Va riformulata sul vettore
origine. Rimane vero e non spiegato che (i) il sito restituisce 403 ai client datacenter
mentre `robots.txt` passa (F-016), e (ii) un URL sitemap vergine fallisce istantaneamente
(F-017). Prossimo punto di osservazione: **log del server di origine** (access log LiteSpeed
+ eventuale log del plugin di sicurezza), filtrati su richieste a `/sitemap*.xml`.

Resta anche da verificare un'alternativa più prosaica, finora non esclusa: che
"Ultima lettura 27/07" sia il timestamp dell'**invio** e non di un fetch realmente eseguito,
nel qual caso Google potrebbe non aver mai ritentato dopo il fix dell'header.

### 1-quater. Access log dell'origine — la causa vera

Accesso SSH in lettura al VPS (CyberPanel + OpenLiteSpeed, docroot
`/home/fotomoto.click/public_html`, log in `/home/fotomoto.click/logs/`, orario server UTC).

**Richieste a URL contenenti "sitemap", per giorno e per client:**

| File di log | Google (`66.249.*`) | Bingbot | Totale |
|---|---|---|---|
| 2026_07_18 | **0** | 263 | 306 |
| 2026_07_19 (×4 rotazioni) | **0** | 180 | 197 |
| 2026_07_20 (×2) | **0** | 227 | 268 |
| 2026_07_21 | **0** | 162 | 649 |
| 2026_07_23 | **0** | 237 | 260 |
| 2026_07_24 | **0** | 246 | 309 |
| 2026_07_25 | **0** | 181 | 665 |
| 2026_07_26 | **0** | 70 | 74 |
| 2026_07_27 | **4** | 169 | 473 |

Le **uniche 4 richieste di Google in 9 giorni** sono queste, tutte con risposta 200:

```
"66.249.72.136 [26/Jul/2026:19:27:12 +0000] "GET /sitemap_index.xml HTTP/2" 200 2077 "Google-InspectionTool/1.0"
"66.249.72.136 [26/Jul/2026:19:27:12 +0000] "GET /sitemap_index.xml HTTP/2" 200 2077 "…Google-InspectionTool/1.0"
"66.249.72.136 [26/Jul/2026:19:27:52 +0000] "GET /sitemap_index.xml HTTP/2" 200 2077 "…Google-InspectionTool/1.0"
"66.249.72.135 [26/Jul/2026:19:27:52 +0000] "GET /sitemap_index.xml HTTP/2" 200 2077 "Google-InspectionTool/1.0"
```

`Google-InspectionTool` è il fetcher del "Testa URL pubblicato": sono le richieste generate a
mano dall'utente durante la sessione live del 26/07 (E-013 riporta "test live eseguito 26/07
21:28" — 19:27 UTC = 21:27 CEST, coincidenza al minuto). **Hanno ricevuto il file corretto.**

**Googlebot è attivo e non ostacolato.** Nel file di log precedente al 27/07, richieste da
`66.249.*`: **587 × 200 · 64 × 301 · 4 × 404 · 1 × 302 · zero 403**.

**Cloudflare non serve le sitemap dalla cache di bordo** — `cf-cache-status: DYNAMIC` misurato
su `sitemap_index.xml`, `sitemap.xml` e `page-sitemap1.xml` — quindi ogni richiesta raggiunge
l'origine e comparirebbe nel log.

**Conclusione:** non esiste un ostacolo, perché non esiste traffico da ostacolare.
**Google non richiede le sitemap.** Cadono H-005, H-006, H-007 e l'ipotesi WAF dell'audit del
26/07: cercavano tutte un blocco lungo un percorso che nessuno sta percorrendo. Subentra
**H-009** (stato GSC persistito), con il test in
[execution-kit/01-sitemap-alias-test.md](../../execution-kit/01-sitemap-alias-test.md).

**Correzione a §1 di questo stesso documento:** `/sitemap.xml` risponde **301** verso
`sitemap_index.xml` (F-022). La misura riportata in §1 — "serve lo stesso index" — era stata
presa con `curl -L`, che segue i redirect e maschera il 301. L'esperimento di §1-bis non è
quindi un controllo valido, e la voce è stata rimossa da GSC il 27/07. Resta valido il fatto
che nemmeno quell'invio ha prodotto una richiesta di Google nel log.

---

## 2. N1 + N2 + N4 chiusi — il namespace legacy `/gallerie/`

**N1 — le 1.129 pagine "Non trovata (404)" con convalida NON RIUSCITA.**
Drill-down GSC (convalida avviata il 18/06, fallita il 01/07): gli esempi restituiti sono
**tutti** dello stesso pattern, senza eccezioni nelle prime 10 righe di 1.000 mostrate:

```
https://fotomoto.click/gallerie/fotomotoclick-bocca-serriola-07-09-2025-292-10-47/
https://fotomoto.click/gallerie/fotomotoclick-bocca-serriola-09-08-2025-567-11-35/
https://fotomoto.click/gallerie/fotomotoclick-bocca-serriola-09-08-2025-431-11-16/
…
```

**N2 — le 2 richieste "Impossibile raggiungere la pagina"** delle statistiche di scansione
sono anch'esse `/gallerie/fotomotoclick-*`, con **tempo medio di risposta 34.100 ms**
(12/05 e 03/05).

**Verifica HTTP diretta (27/07):**

| URL | Esito |
|---|---|
| `/gallerie/fotomotoclick-bocca-serriola-07-09-2025-292-10-47/` | **404** in 0,57 s |
| `/gallerie/fotomotoclick-bocca-serriola-09-08-2025-567-11-35/` | **404** in 0,48 s |
| `/gallerie/` | **301** → `/gallerie-foto/` |
| `/gallerie-foto/` | **301** → `/passi-e-valichi/` |

→ `/gallerie/` è un **namespace di URL legacy**: i prodotti sotto di esso rispondono 404,
alcuni sono andati in timeout a 34 s, e la radice produce una **catena di due redirect**.

**N4 — `/gallerie-foto/`.** Non è solo un redirect da correggere nei link interni: su 16 mesi
è la **quarta pagina del sito per clic e la seconda per impressioni**, e gira su un 301 a due
hop (vedi §3).

---

## 3. Rendimento 16 mesi (08/05/2025 – 24/07/2026)

Totali: **19.000 clic · 118.000 impressioni · CTR 16,1% · posizione media 5,8 · 871 query**.
Il grafico mostra traffico piatto fino a marzo 2026 ed espansione forte da aprile 2026
(picchi di impressioni ~1.800/giorno).

### Pagine principali

| Pagina | Clic | Impressioni | CTR calcolato |
|---|---|---|---|
| `/` | 13.922 | 105.941 | 13,1% |
| `/foto/bocca-serriola/` | 2.549 | 29.717 | 8,6% |
| `/foto/terminillo/` | 981 | 2.835 | 34,6% |
| **`/gallerie-foto/`** (301 a 2 hop) | **912** | **27.098** | **3,4%** |
| `/passi-e-valichi/` | 357 | 5.290 | 6,7% |
| `/foto/viamaggio/` | 176 | 559 | 31,5% |
| `/foto/capannelle/` | 175 | 1.235 | 14,2% |
| **`/contatti/`** | **158** | **20.023** | **0,8%** |
| `/foto/spino/` | 133 | 877 | 15,2% |
| `/link/` | 124 | 3.274 | 3,8% |

Due pagine che non servono alcuna intenzione di ricerca — un redirect e la pagina contatti —
raccolgono insieme **47.121 impressioni** con CTR combinato **1,7%**, contro il 16,1% del sito.
Le landing località, quando l'intento coincide, stanno fra il 14% e il **34,6%** di CTR.

### Query principali

| Query | Clic | Impr. | CTR | Pos. |
|---|---|---|---|---|
| **fotografi moto sui passi** | **1.304** | 6.681 | 19,5% | 3,2 |
| foto bocca serriola | 1.006 | 2.474 | 40,7% | 3,3 |
| fotografo bocca serriola | 589 | 1.432 | 41,1% | 3,5 |
| fotomoto | 439 | 1.776 | 24,7% | 7,4 |
| fotomotoclick | 433 | 481 | 90% | 1,0 |
| fotografo passo delle capannelle | 178 | 1.711 | 10,4% | 7,0 |
| **foto moto** | **176** | **14.818** | **1,2%** | **3,2** |
| foto passi moto | 96 | 1.366 | 7% | 6,2 |
| foto in moto | 41 | 1.368 | 3% | 6,4 |

**La query n.1 del sito su 16 mesi non è di brand né di località: è `fotografi moto sui passi`**
(1.304 clic, posizione 3,2).

**Località con domanda misurata e non coperte** (le località coperte sono 5: bocca serriola,
terminillo, viamaggio, capannelle, spino):

| Query | Clic | Impr. | Pos. |
|---|---|---|---|
| fotografo passo del cornello | 55 | 220 | 4,9 |
| foto moto montezemolo | 50 | 444 | 8,5 |
| fotografo bocca trabaria | 38 | 533 | 8,7 |
| foto montezemolo moto | 37 | 335 | 8,7 |
| fotografo moto campo imperatore | 36 | 325 | 5,3 |
| fotografo moto montezemolo | 31 | 188 | 7,2 |

### Cannibalizzazione su `foto moto` (filtro query esatta, 16 mesi)

| Pagina | Clic | Impressioni |
|---|---|---|
| `/` | 173 | 14.674 |
| `/gallerie-foto/` | 7 | 3.686 |
| `/passi-e-valichi/` | 1 | 11 |
| `/foto/bocca-serriola/` | 0 | 3.224 |
| `/contatti/` | 0 | 3.073 |
| `/link/` | 0 | 19 |

Cinque URL del sito compaiono nella stessa SERP; solo la home converte (173 clic su 176).
`/contatti/` da sola prende 3.073 impressioni e **zero** clic su questa query.

---

## 4. Core Web Vitals e TTFB

CWV (CrUX, 25/07): **mobile 0 URL buoni / 41 richiedono miglioramenti / 0 scadenti**;
**desktop 41 buoni / 0 / 0**. Situazione invariata rispetto al 26/07.

TTFB misurato (UA mobile, 27/07):

| Pagina | TTFB | Peso HTML |
|---|---|---|
| `/` | **0,14 s** | 278 KB |
| `/foto/bocca-serriola/` | **0,39 s** | 225 KB |
| **`/foto/bocca-serriola/11-07-2026/`** (landing sessione) | **2,02 s** | 212 KB |

→ Il rallentamento è concentrato esattamente sul tipo di pagina che deve entrare
nell'indice e che oggi non ci entra.

---

## 5. GA4 — il funnel non chiude

Proprietà `fotomoto.click`, ultimi 28 giorni (29/06 – 26/07/2026).
Totali: **11.144 visualizzazioni · 1.451 utenti · 78.173 eventi · 1.238,00 €**.

### Eventi e-commerce

| Evento | Conteggio | Utenti | Entrate |
|---|---|---|---|
| `view_item` | 3.122 | 485 (33,4%) | — |
| `add_to_cart` | 429 | ~99 (6,8%) | — |
| `begin_checkout` | ~78 (0,1%) | ~53 (3,65%) | — |
| **`purchase`** | **87** | **84** (5,8%) | **1.238,00 €** |

**`purchase` (87 eventi / 84 utenti) supera `begin_checkout` (~78 eventi / 53 utenti).**
In un funnel strumentato correttamente non si può acquistare più volte di quante se ne
inizi il checkout: **`begin_checkout` è sotto-rilevato**. Ordine medio dichiarato da GA4:
14,23 €.

Eventi e-commerce standard **assenti** dall'elenco (verificato con ordinamento alfabetico:
la prima riga è `add_to_cart`, quindi nulla la precede): `add_payment_info`,
`add_shipping_info`, `view_cart`, `remove_from_cart`.

### Strumentazione custom del finder — esiste ed è ricca

`finder_passo_select` 5.045 · `search_load_more` 4.775 · `finder_slot_select` 3.982 ·
`finder_data_select` 2.613 · `finder_date_wheel_roll` 2.602 · `search_results_view` 1.883 ·
`search_results_loaded` 1.821 · `finder_view` 1.613 · `finder_data_loaded` 1.604 ·
`search_refine_slot_select` 1.535 · `search_result_click` 1.358 · `finder_submit` 450 ·
più la famiglia `linkbio_finder_*`. Manca però l'evento standard **`search`**.

### Pagine più viste (28 gg)

| Pagina | Visualizzazioni | Utenti | Durata media |
|---|---|---|---|
| **`/cerca-foto-moto/`** | **1.864** (16,7%) | **542** (37,6%) | **3 m 59 s** |
| `/` | 1.629 | 768 (53,3%) | 37 s |
| `/link/` | 745 | 324 | 19 s |
| `/foto/bocca-serriola/11-07-2026/` | 475 | 80 | 1 m 57 s |
| `/foto/bocca-serriola/25-07-2026/` | 439 | 55 | 2 m 52 s |
| `/foto/bocca-serriola/05-07-2026/` | 435 | 90 | 1 m 54 s |
| `/foto/bocca-serriola/12-07-2026/` | 421 | 71 | 2 m 08 s |
| `/foto/bocca-serriola/19-07-2026/` | 401 | 87 | 1 m 49 s |
| `/carrello/` | 310 | 82 | 34 s |

Il finder è la pagina più usata del sito e la più coinvolgente. Le landing sessione hanno
2-3 minuti di permanenza: **sono commercialmente vive anche mentre sono fuori dall'indice**.

### Canali (7 gg) e attribuzione

Direct 263 (+45,3%) · Organic Search 227 (+14,6%) · Paid Social 126 (−6,7%) ·
Organic Social 115 (+38,6%) · **Unassigned 177 (+2.428%)** · Cross-network 51 ·
AI Assistant 2 (−50%).

**"Unassigned" è passato da ~10 a 177 sessioni in una settimana.** È la firma tipica di
sessioni che perdono la sorgente — candidato principale: il rientro dal gateway di pagamento
non escluso dai referral, che spezza anche l'attribuzione delle conversioni.

---

## 6. Tracciamento servito nell'HTML

Ispezione del sorgente servito (senza esecuzione JS) su home, landing sessione, prodotto,
carrello, checkout, finder.

| Pagina | `fbq` presenti | Tag Google |
|---|---|---|
| `/` | PageView, AddToCart | `GT-PZVLXWXN` |
| `/foto/{loc}/{data}/` | PageView, AddToCart | `GT-PZVLXWXN` |
| prodotto | PageView, **ViewContent**, AddToCart | `GT-PZVLXWXN` |
| `/carrello/` | PageView, AddToCart | `GT-PZVLXWXN` |
| `/checkout/` | PageView, AddToCart | `GT-PZVLXWXN` |
| `/cerca-foto-moto/` | PageView, AddToCart | `GT-PZVLXWXN` |

Pixel Meta: `fbq('init','636147212576293')`, libreria `connect.facebook.net/en_US/fbevents.js`.

**Non compaiono nel sorgente servito:** `InitiateCheckout`, `AddPaymentInfo`, `Purchase`,
`Search`.

> ⚠️ **Limite del metodo.** Le pagine sono state richieste **senza sessione e con carrello
> vuoto, e senza eseguire JavaScript**. Eventi che il plugin Meta emette dinamicamente o solo
> in presenza di un carrello/ordine possono non comparire in questa ispezione. L'assenza qui
> è un **indizio forte, non una prova**. Verifica conclusiva: caricare le pagine in browser
> con un carrello reale e osservare le chiamate a `facebook.com/tr` e
> `google-analytics.com/g/collect`.

---

### 6-bis. Meta Events Manager (accesso in lettura, 27/07)

Portfolio aziendale consultato: **`photomoto.click`** (`business_id 1394804364887155`).
Dataset elencati:

| Nome | ID | Note |
|---|---|---|
| ⚠ old_test | 1353450195958939 | icona app mobile |
| ⚠ **fotomoto.click** | **636147212576293** | **in grigio**, icona ⓘ, icona app mobile — è l'ID installato sul sito |
| old_test | 2482896492083241 | — |

Pannello del dataset `636147212576293` (intervallo 29 giu – 26 lug 2026), dopo ricarica
completa con URL agganciato all'ID:

- **"Nessuna attività ricevuta nell'intervallo di tempo selezionato"**
- **Nessuna integrazione**
- **Siti web: Nessun sito web trovato** · **App mobile: 636147212576293**
- Completamento configurazione **33%**; passo "Configura eventi" non completato
- Tabella eventi vuota

> ⚠️ **Due letture possibili, non ancora discriminate.**
> **(a)** Il pixel non riceve nulla → tutta la misurazione Meta è cieca e le campagne Paid
> Social ottimizzano senza segnale di conversione.
> **(b)** Questo portfolio ha visibilità solo parziale sul dataset (la voce è in grigio con
> ⓘ, tipico degli asset condivisi o ad accesso limitato) → i dati esistono ma non qui.
>
> **Test discriminante, 2 minuti:** scheda **"Testa gli eventi"** del dataset, poi caricare
> il sito in un'altra scheda e osservare se gli eventi arrivano in tempo reale.

Da notare anche la discrepanza di naming: il portfolio si chiama **photomoto**.click, il
sito è **fotomoto**.click. Da verificare che non esistano un secondo portfolio o un secondo
pixel storici.

---

## 7. Cosa resta aperto

| # | Domanda | Stato |
|---|---|---|
| Q1 | Perché GSC dice "Impossibile leggere" su un file che tutti gli altri client leggono | **ipotesi forte (H-007)**: il fetcher sitemap di GSC riceve il 403 della regola anti-datacenter riprodotta in §1-bis. Da confermare su Cloudflare Security Events / log LiteSpeed |
| Q2 | Di quanto GA4 sotto-registra rispetto agli ordini reali di WooCommerce | **aperta** — serve il confronto con i dati WooCommerce sullo stesso periodo |
| Q3 | Il pixel Meta emette davvero Purchase/InitiateCheckout a runtime | **aperta** — serve la verifica in browser con carrello reale |
| Q4 | Cosa ha generato il salto di "Unassigned" (da ~10 a 177) nell'ultima settimana | **aperta** |
| Q5 | Cosa è cambiato il 30/05/2026 sui CWV mobile (N3, da E-013) | **aperta** |
