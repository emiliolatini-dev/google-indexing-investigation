# FotoMoto.Click — SEO Master Audit

> **Data:** 26-07-2026
> **Metodo:** audit basato su crawl reale del sito in produzione (robots.txt, sitemap_index + 244 sotto-sitemap scaricate, HTML di homepage, landing località, landing sessione, pagina prodotto foto, pagina prodotto video, pagine editoriali, header HTTP, SERP reali, analisi competitor).
> **Nota epistemica:** ogni affermazione marcata **[MISURATO]** deriva da una richiesta HTTP eseguita il 26-07-2026. Le affermazioni marcate **[STIMA]** sono inferenze. Ciò che non ho potuto misurare è dichiarato come tale (§2.5).

---

## 0. Verdetto in una pagina

FotoMoto.Click non ha un problema di SEO. Ha **un problema di rapporto segnale/rumore che si manifesta come problema di SEO**, e che oggi sta anche bloccando l'indicizzazione delle sessioni nuove (l'oggetto dell'investigation in questo repo).

I numeri, misurati:

| Metrica | Valore | Fonte |
|---|---|---|
| URL totali dichiarati nelle sitemap | **~55.360** | [MISURATO] |
| di cui URL prodotto (singole foto/video) | **53.683** | [MISURATO] |
| di cui pagine WP vuote e indicizzabili | **1.604** | [MISURATO] |
| di cui pagine con reale domanda di ricerca | **~70** (57 categorie + 13 pagine) | [MISURATO] |
| **Rapporto rumore : segnale** | **~790 : 1** | calcolato |
| Foto linkate internamente da una landing sessione | **18** (su 1.430 della sessione campione) | [MISURATO] |
| Sessioni pubblicate | 54 | [MISURATO] |
| Località coperte | 5 | [MISURATO] |
| Video pubblicati | 12 | [MISURATO] |

Stai chiedendo a Google di scoprire, crawlare, renderizzare e valutare **55.000 URL** per servirti su **70**. E le 55.000 sono in gran parte pagine senza H1, senza testo, con `alt` duplicati e descrizioni generate male. Google non ti sta ignorando: ti sta **classificando come sito a bassa densità di valore** e riducendo di conseguenza la frequenza di crawl — che è esattamente il sintomo documentato in `registry/claims.md` (F-010: nessun crawl spontaneo sulla sessione 21-06-2026).

La correzione della cache Rank Math (F-011) era necessaria ma non sufficiente: hai risolto il *trasporto* del segnale, non il *segnale*.

**La cosa più controintuitiva di questo audit:** la mossa a più alto impatto non è aggiungere pagine. È **rimuoverne ~52.000 dall'indice**. §3.1 e §13.1.

E la seconda cosa più importante: **stai perdendo la SERP sul tuo passo principale**. Per `foto moto Bocca Serriola motociclisti`, Motoevasioni occupa posizione 1 e 2; FotoMoto.Click compare dopo. [MISURATO — SERP reale, 26-07-2026]. Bocca Serriola è l'89% del tuo archivio.

---

## 1. Riformulazione del modello (prima di ogni best practice)

Hai chiesto di non trattarlo come un e-commerce. Correttamente. Ecco la riformulazione che uso in tutto il documento.

**Cosa vendi davvero:** non fotografie. Vendi **la prova di un'esperienza già avvenuta**. L'unità di valore non è un file JPG: è un **evento irripetibile identificato da luogo + data + ora + soggetto**.

Conseguenze che cambiano tutta la SEO:

1. **La domanda è retroattiva.** Nessuno cerca "foto moto" *prima* di guidare. La ricerca nasce dopo: *"ero a Bocca Serriola domenica, mi hanno fotografato, dove trovo la foto?"*. È una domanda ad altissima intenzione, volume basso, finestra temporale di 48-72 ore.
2. **Il prodotto ha domanda zero a livello di singola unità.** Nessun essere umano cercherà mai `fotomotoclick-bocca-serriola-19-07-2026-284-10-05`. Il 97% del tuo sito è composto da URL con **domanda di ricerca strutturalmente nulla**. Questo è il fatto centrale che i consulenti SEO generalisti non vedono, perché in un e-commerce normale la pagina prodotto *è* la pagina di atterraggio.
3. **Il volume di ricerca sfruttabile non è nel post-giro, è nel pre-giro.** "Passi in moto Umbria", "itinerari moto Appennino", "Bocca Serriola moto", "dove andare in moto weekend Toscana": qui c'è 50-100× il volume di "foto moto passo". **Oggi non presidi niente di tutto questo.** §5.3.
4. **Possiedi un asset che nessun concorrente ha e che nessuno può copiare:** 53.683 osservazioni datate, geolocalizzate e orarie del traffico motociclistico reale sui passi dell'Italia centrale, su 16 mesi. **Questo non è un catalogo. È un dataset.** È l'unica cosa in tutto il progetto che possa produrre contenuto originale non replicabile — e quindi l'unica base credibile per dominare. §13.2.

Regola che applico da qui in poi: *ogni volta che una best practice standard direbbe "ottimizza la pagina prodotto", mi chiedo prima se quella pagina debba esistere per Google.*

---

## 2. Evidenze raccolte

### 2.1 Inventario URL [MISURATO]

```
sitemap_index.xml            244 sotto-sitemap
├── page-sitemap1..7.xml     1.617 URL  → 13 pagine reali + 1.604 pagine spazzatura
├── product-sitemap1..235    53.683 URL → foto (53.671) + video (12)
├── product_cat-sitemap.xml  57 URL     → 5 località + 52 sessioni
└── product_tag-sitemap.xml  4 URL      → capannelle, spino, terminillo, viamaggio
```

Distribuzione prodotti per path:

| Path | Prodotti |
|---|---|
| `/foto/bocca-serriola/` | 48.226 |
| `/foto/capannelle/` | 1.032 |
| `/foto/terminillo/` | 875 |
| `/foto/spino/` | 86 |
| `/foto/28-06-2026-3/` ⚠️ | 1.601 |
| `/foto/14-06-2026-2/` ⚠️ | 1.043 |
| `/foto/01-06-2025-2/` ⚠️ | 530 |
| `/foto/26-04-2026-3/` ⚠️ | 188 |
| `/foto/28-06-2026-2/` ⚠️ | 101 |

⚠️ **3.463 prodotti hanno come primo segmento URL una DATA invece di una LOCALITÀ.** Bug di categoria primaria WooCommerce: per quelle sessioni il termine "data" è stato assegnato come primary term. Risultato: URL semanticamente rotti, segnale di località perso, e Viamaggio (1.144 foto totali) **non ha nemmeno un path proprio**.

Inoltre: `https://fotomoto.click/gallerie-foto/` è presente **dentro `product-sitemap1.xml`** e restituisce **301** → `/passi-e-valichi/`. Una sitemap che dichiara un redirect.

### 2.2 Le 1.604 pagine spazzatura [MISURATO]

Campione: `https://fotomoto.click/fotomotoclick-bocca-serriola-29-06-2025-694-16-22-3/`

```
HTTP 200
<meta name="robots" content="follow, index, max-snippet:-1, max-image-preview:large">
<link rel="canonical" href="...self...">
<title>fotomotoclick-bocca-serriola-29-06-2025-694-16-22 | FotoMoto.Click</title>
<h1>   → ASSENTE
<h2>   → ASSENTE
Immagini nel body: 1 (il logo)
Testo visibile: 0 parole
Schema: solo BreadcrumbList
TTFB: 442 ms (cache MISS)
```

Sono **pagine WordPress completamente vuote**, indicizzabili, self-canonical, e **attivamente sottoposte a Google via sitemap**. Duplicano nel titolo lo slug di prodotti che esistono già altrove. Sono 1.604.

Questo è, da solo, un segnale di qualità di sito devastante. Google campiona: se pesca 5 URL a caso dal tuo `page-sitemap`, ne pesca 5 vuote su 5.

### 2.3 Landing sessione — la pagina che deve posizionarsi, e non può [MISURATO]

Campione: `https://fotomoto.click/foto/bocca-serriola/19-07-2026/` (1.430 foto nella sessione)

```
TTFB: 2.163 s   ← x-litespeed-cache: miss
<h1> ASSENTE   <h2> ASSENTE   <h3> ASSENTE
Testo visibile totale: "Foto moto Bocca Serriola 19-07-2026 | FotoMoto.Click"  (il titolo, nient'altro)
<meta name="description" content="Foto del 19-07-2026 |">     ← template rotto, pipe orfana
Link a prodotti nell'HTML: 18   (su 1.430)
Paginazione: assente. Nessun rel=next, nessun markup woocommerce-pagination.
alt delle 18 immagini: 1 solo valore, identico, e che cita un file diverso
  alt="FotoMoto.Click | La Tua Moto, la Nostra Foto fotomotoclick-bocca-serriola-19-07-2026-212-10-00"
Schema: CollectionPage + BreadcrumbList. Nessun ItemList, nessun Product, nessun Event.
```

E il comportamento della paginazione:

```
/foto/bocca-serriola/19-07-2026/page/2/   → 200, stessi identici 18 prodotti, noindex, NESSUN canonical
/foto/bocca-serriola/19-07-2026/page/5/   → 200, idem
/foto/bocca-serriola/19-07-2026/page/99/  → 200, idem
```

Tre problemi in uno:
- **spazio URL infinito** crawlabile (54 sessioni × ∞ pagine);
- **paginazione rotta** (page/2 = page/1);
- **nessun canonical** su quelle pagine.

La griglia usa Elementor Loop Grid con caricamento AJAX/infinite scroll (18 per batch) [STIMA, coerente con 18 link + assenza totale di markup di paginazione]. Googlebot non attiva eventi di scroll: **vede 18 foto su 1.430, e non ha alcun percorso alternativo per le altre 1.412 se non la sitemap.**

### 2.4 Pagina prodotto [MISURATO]

Onestamente: **è la pagina fatta meglio del sito.**

```
<title>Foto moto Bocca Serriola 19-07-2026 ore 10:05 #284 | FotoMoto.Click</title>
<meta description>  scritta bene, unica, con specifiche tecniche
Schema: Product + Offer + Brand + ItemPage + 3 ImageObject + BreadcrumbList + Organization
Contenuto: prezzo, sconti quantità, privacy notice, "Momenti vicini" (±2 foto), upsell Premium +15€
Link interni: 5 (catena sequenziale prev/next crawlabile)
<h1>: ASSENTE
```

Difetti: manca l'H1; `og:image:alt` è il nome del file; **il video (15€) usa `Product` e non `VideoObject`** → nessuna possibilità di video rich result né di comparire in Google Video.

La catena "Momenti vicini" ±2 è un'idea architettonicamente buona: crea una linked list crawlabile. Ma con ingresso solo dai primi 18 elementi, per arrivare alla foto 1.430 servono ~700 hop. **Profondità di crawl effettiva: irraggiungibile.**

### 2.5 Dati GSC / GA4 — colmati il 26-07-2026

> I tre buchi dichiarati qui erano: CWV di campo, dati GSC, profilo backlink. **Sono stati tutti colmati lo stesso giorno** con accesso diretto a Search Console e GA4.
> **→ Vedi [2026-07-26-gsc-ga-evidence.md](2026-07-26-gsc-ga-evidence.md).**

Sintesi di ciò che i dati reali hanno cambiato:

| Punto | Stato precedente | Dato reale |
|---|---|---|
| Core Web Vitals mobile | non misurato | **0 URL buoni su 42**, regressione dal 30/05/2026 |
| Sitemap | assunte funzionanti | **"Impossibile recuperare", 0 pagine rilevate, mai lette** 🔴 |
| Indicizzazione | ignota | 4.822 indicizzate, 12.047 escluse, **5.640 "scansionate ma non indicizzate"** |
| URL noti a Google | ~55.360 dichiarati | **16.869** — ~38.500 mai entrati nel sistema |
| Clic dei prodotti | [STIMA] vicino a zero | **55 su 9.960 = 0,55%** — tesi §3.1 confermata |
| Google Immagini | non misurato | **29 clic in 3 mesi sull'intero sito** |
| Backlink | [STIMA] quasi nullo | **3 sessioni referral in 7 giorni** — confermato |
| Traffico da AI | non misurato | **2 sessioni "AI Assistant" in 7 giorni** |

Rimane non estratto: la serie storica a 16 mesi (in sessione è stato usato l'intervallo predefinito a 3 mesi).

---

## 3. Architettura dell'informazione

### 3.1 Il problema strutturale: hai modellato le foto come prodotti, e i prodotti come pagine

In WooCommerce ogni foto è un `product` con permalink pubblico. È la scelta di default e per un e-commerce normale è giusta. **Per te è l'errore fondativo**, perché genera 53.683 URL indicizzabili con domanda di ricerca zero.

Alternative, in ordine di radicalità:

| Opzione | Cosa comporta | Valutazione |
|---|---|---|
| A. `noindex, follow` su tutti i prodotti + rimozione da sitemap | Google smette di indicizzare le foto, continua a seguirne i link. Le landing concentrano tutto il crawl budget | Sicura, reversibile, alto impatto |
| B. Curated indexing: 20-40 foto/sessione indicizzabili con caption unica, resto `noindex` | Mantieni presenza in Google Images con contenuto di qualità; elimini il rumore | **Raccomandata** |
| C. Rimuovere del tutto i permalink prodotto (foto come entità non-pubbliche, acquisto in overlay sulla galleria) | Architettura più pulita, ma rompe la condivisione dei link e la catena "Momenti vicini" | Troppo distruttiva ora |

**Raccomandazione: B.** Perché A butta via l'unica leva davvero specifica del tuo modello (Google Images, §13.3), e C rompe la UX di condivisione che oggi funziona.

Effetto atteso: URL in sitemap da ~55.360 a **~2.500**. Crawl budget concentrato di ~22×.

> ⚠️ Non è una mossa a rischio zero. Se oggi ricevi traffico organico da pagine prodotto, lo perdi. **Prima di eseguire: esporta da GSC (Rendimento → Pagine, 16 mesi) e verifica quante pagine `/foto/*/fotomotoclick-*` hanno ≥1 click.** [STIMA: vicino a zero, ma va verificato, non assunto.] Questo è esattamente il tipo di verifica che il tuo repo esiste per fare.

### 3.2 Gerarchia attuale vs gerarchia corretta

**Attuale (incoerente):**
```
/                                          Home (nessun H1)
/passi-e-valichi/                          hub località (1 H2, ItemList con 7 item)
/foto/{località}/                          landing località  ✅ buona
/foto/{località}/{data}/                   landing sessione  ❌ vuota
/foto/{località}/{slug-foto}/              prodotto
/foto/{data-slug}/{slug-foto}/             ⚠️ prodotto con path rotto (3.463 URL)
/fotomotoclick-{...}/                      ⚠️ 1.604 pagine vuote
/cerca-foto-archivio/                      finder (H1, 0 H2, 283→617 parole)
/gallerie-foto/                            301 → /passi-e-valichi/ (linkato 8× dalla home!)
```

**Target:**
```
/                                          Home
/passi/                                    Hub: mappa + 5 località + espansione a N passi
/passi/{passo}/                            ENTITÀ PASSO — contenuto turistico/motociclistico (NUOVO)
/passi/{passo}/foto/                       landing commerciale località (attuale /foto/{loc}/)
/passi/{passo}/foto/{data}/                landing sessione, riscritta
/passi/{passo}/dati/                       "il passo in numeri" — dal dataset (NUOVO, §13.2)
/gallerie/{tema}/                          collezioni curate: in piega, tramonto, pioggia (NUOVO)
/moto/{marca-modello}/                     collezioni per modello (NUOVO, §13.4)
/guide/{slug}/                             contenuto editoriale pre-giro (NUOVO)
```

Non sto proponendo di migrare gli URL esistenti — un redirect di massa su 53k URL è rischio puro. Sto proponendo di **affiancare** un livello `/passi/` che oggi non esiste, e di lasciare `/foto/` come livello transazionale.

### 3.3 Internal linking — lo strato più rotto

[MISURATO] La homepage contiene **8 link a `/gallerie-foto/`, che è un 301**. Otto link interni sprecati in redirect, dalla pagina con più autorità del sito. Fix da 10 minuti.

Altri difetti:
- La home linka 5 località e 4 sessioni. Nessun link a `/cerca-foto-archivio/`, che è lo strumento di conversione principale.
- `/passi-e-valichi/` ha `ItemList` con **7 ListItem** ma tu hai 5 località: due voci fantasma o duplicate.
- Nessun link da pagina sessione → pagine sessione adiacenti ("sessione precedente / successiva sullo stesso passo"). È il link più naturale del modello e manca.
- Nessun hub temporale: non esiste `/foto/bocca-serriola/2026/` né un archivio per mese.
- I 4 `product_tag` (capannelle, spino, terminillo, viamaggio) duplicano le categorie località → **cannibalizzazione tassonomica**. Vanno rimossi o messi in noindex.

### 3.4 Pagine orfane

Con le 18 foto per sessione e la paginazione rotta: **~52.700 prodotti sono di fatto orfani** (raggiungibili solo via sitemap e via catena ±2 a profondità proibitiva). Anche mantenendo l'opzione B di §3.1, va aggiunta una **pagina indice per sessione** con link testuali a tutte le foto (o a blocchi orari), server-side, senza JS.

### 3.5 Breadcrumb

Presenti e corretti (`BreadcrumbList` su prodotto, sessione, località). Uno dei pochi elementi tecnicamente sani. ✅

---

## 4. SEO tecnica

### 4.1 robots.txt — contiene 3 errori reali [MISURATO]

```
User-agent: AdsBot
Disallow: /
```
**`AdsBot` non è un product token valido di Google.** I token reali sono `AdsBot-Google` e `AdsBot-Google-Mobile`. O la regola è morta (rumore inutile), o — se un crawler la interpreta per prefisso — **blocca il controllo qualità delle landing di Google Ads**, con conseguente disapprovazione degli annunci. In entrambi i casi va corretta o rimossa. Non lasciarla ambigua.

```
User-agent: Googlebot-Image
Allow: /wp-content/uploads/
```
Questo gruppo **sostituisce integralmente** il gruppo `*` per Googlebot-Image (RFC 9309: vale un solo gruppo per crawler). Effetto collaterale: Googlebot-Image perde tutte le altre direttive. È inutile (le uploads sono già Allow) e crea un comportamento non ovvio. Da rimuovere.

```
User-agent: Google-Extended  → Disallow: /
User-agent: GPTBot           → Disallow: /
User-agent: CCBot            → Disallow: /
```
**Contraddizione diretta con l'obiettivo 9 del brief.** Dettaglio in §11.

**Manca** (e serve, dati i problemi di §2.3):
```
Disallow: /*?add-to-cart=
Disallow: /*?orderby=
Disallow: /*/feed/
Disallow: /*/page/          # dopo aver sistemato la paginazione, vedi §4.4
```

### 4.2 Sitemap

- 244 file. Rank Math default 200 URL/file ma i file misurati ne contengono 250 e 157 → configurazione a 250. Con la pulizia di §3.1 si scende a ~10 file.
- **`sitemap_index.xml` è servito con `Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private`** [MISURATO]. Corretto dopo l'incidente Rank Math (F-011), ma ora è l'estremo opposto: nessuna cache per Googlebot su un file che cambia una volta a settimana. Meglio `max-age=300, must-revalidate`.
- `product-sitemap1.xml` contiene un URL che fa 301. Da rimuovere.
- ✅ L'estensione `image:image` è presente in tutte le sitemap prodotto: buono, ed è la base su cui costruire §13.3.
- **Manca una sitemap news/sessioni recenti.** Con 1-2 sessioni/settimana, una sitemap dedicata alle ultime 20 sessioni (≪50 URL, `lastmod` accurato) è un canale di scoperta molto più efficace di un index da 244 file. **Questa è probabilmente la singola mossa più diretta contro il problema di indicizzazione documentato nel repo.**

### 4.3 Cache e CDN [MISURATO]

| Risorsa | TTFB | LiteSpeed | Cloudflare |
|---|---|---|---|
| Home | 124 ms | HIT | DYNAMIC |
| Pagina vuota | 442 ms | MISS | DYNAMIC |
| Landing sessione | **2.163 ms** | MISS | DYNAMIC |
| Immagine .webp | — | — | **HIT**, `max-age=604800`, `Age: 391633` |

Due letture:

1. **Cloudflare non cachea l'HTML.** `cf-cache-status: DYNAMIC` ovunque. Stai pagando Cloudflare e usandolo come proxy per le sole immagini. Una **Cache Rule** che cachi l'HTML delle landing località/sessione con `Edge TTL` 1h + purge via API nella pipeline `generate-json` (che già fa purge LiteSpeed — O-003) porterebbe il TTFB da 2.1 s a <100 ms globalmente. **Alto impatto, difficoltà media, e riusa infrastruttura che hai già.**

2. **2.1 secondi di TTFB su cache MISS è il killer dell'LCP.** E ogni sessione nuova è per definizione MISS. Le pagine che devono indicizzarsi in fretta sono esattamente quelle più lente. Googlebot riduce il crawl rate quando i tempi di risposta salgono: **c'è un loop di retroazione negativa tra lentezza e mancata indicizzazione**, e sta girando adesso.

**Azione immediata:** warmup automatico post-pubblicazione — dopo `generate-json`, fai `curl` di landing località + landing sessione + prime N pagine per popolare LiteSpeed prima che arrivi Googlebot. È uno script da 20 righe.

### 4.4 Paginazione

Stato: `/page/N/` restituisce 200 con contenuto identico, `noindex`, **senza canonical**. Da correggere:
- se la paginazione resta AJAX-only → `/page/N/` con N>1 deve restituire **404** (non 200), oppure canonical a page/1;
- meglio: **paginazione server-side reale** con `<a href>`, 60 foto per pagina, indicizzabile solo page/1, e link testuali per fascia oraria.

### 4.5 Immagini

- Formato WebP ✅, `loading="lazy"` presente ✅, cache 7 giorni su CDN ✅.
- **`alt` duplicato su tutta la galleria sessione**: un solo valore per 18 immagini diverse, e per giunta riferito a un file che non è nessuna delle 18. [MISURATO] Bug grave e a costo di fix quasi nullo. Per un business fotografico, l'`alt` è il testo principale.
- Sulla landing località l'`alt` è invece scritto bene: `"Motociclista fotografato a Bocca Serriola - FotoMoto.Click"` ✅. Prova che il template esiste e non è applicato ovunque.
- **Nessun `alt` contiene mai: modello di moto, colore, fascia oraria, condizioni, direzione di marcia.** Con 53k immagini, un template `alt` ricco è generabile automaticamente dai metadati che già possiedi.
- Nessun preload dell'immagine LCP sulle landing.

### 4.6 Duplicazioni e thin content — riepilogo

| Tipo | Quantità | Gravità |
|---|---|---|
| Pagine WP vuote indicizzabili | 1.604 | 🔴 critica |
| Prodotti senza testo unico oltre il template | ~53.600 | 🔴 critica |
| Landing sessione senza contenuto | 52 | 🔴 critica |
| `/page/N/` infinite | ∞ | 🟠 alta |
| Feed RSS per categoria (`/foto/{loc}/feed/` → 200) | 57+ | 🟡 media |
| product_tag duplicati delle categorie | 4 | 🟡 media |
| Pagine editoriali senza H1 (contatti, link, collabora, scopri-qualità) | 4 | 🟡 media |

### 4.7 JavaScript SEO

Stack: Elementor + Elementor Pro + WooCommerce + Complianz + Site Kit. **42 tag script sulla pagina prodotto**, 24 sulla sessione [MISURATO].

Il rischio concreto non è il rendering (Google renderizza), è che **il contenuto principale della galleria non è nel DOM iniziale e non è raggiungibile senza interazione utente**. Googlebot non scrolla. Regola: *tutto ciò che deve posizionarsi deve esistere nell'HTML al primo byte.*

---

## 5. Contenuti

### 5.1 Qualità attuale, per tipo di pagina

| Pagina | Parole | H1 | Giudizio |
|---|---|---|---|
| `/foto/bocca-serriola/` | ~800 | ✅ | **Ottima.** Ha H1, 7 H2, FAQ, sezioni tematiche. È il modello da replicare |
| `/domande-frequenti/` | 1.360 | ✅ | Buona |
| `/dietro-fotomoto-click/` | ~900 | ✅ | Buona, umana, credibile. 6 H2 narrativi |
| Home | 1.180 | ❌ **assente** | Buona nei contenuti, rotta nella struttura |
| `/passi-e-valichi/` | poche | ✅ | Thin. 1 solo H2, nessuna descrizione dei passi |
| `/cerca-foto-archivio/` | ~300 | ✅ | Thin. `<meta description>` = "ARCHIVIO FOTO" |
| `/contatti/` | 546 | ❌ | Manca H1 |
| `/scopri-la-qualita.../` | 617 | ❌ | Manca H1 |
| `/collabora-con-noi/` | 503 | ❌ | Manca H1 |
| `/link/` | 283 | ❌ | Manca H1 |
| Landing sessione ×52 | **0** | ❌ | **Vuote** |
| Prodotti ×53.683 | template | ❌ | Nessuna unicità |

**L'asimmetria è il dato interessante:** sai scrivere bene (la pagina Bocca Serriola e "Dietro FotoMoto.Click" lo dimostrano). Il problema è che il 99,9% delle pagine è generato da template che non producono testo.

### 5.2 Cannibalizzazioni

- `/passi-e-valichi/` vs `/gallerie-foto/` (301) vs `/cerca-foto-archivio/` competono sullo stesso intento "trova le gallerie". Vanno differenziate: hub tassonomico / strumento di ricerca.
- `product_tag/spino` vs `/foto/spino/` → stesso intento, due URL.
- Landing località vs landing sessione: la sessione è vuota, quindi oggi non cannibalizza — ma quando la riempirai, deve targetizzare `foto moto {passo} {data}` e non `foto moto {passo}`.

### 5.3 Keyword mapping — cosa presidi e cosa no

**Presidiato (parzialmente):**

| Query | Pagina | Stato |
|---|---|---|
| `foto moto bocca serriola` | `/foto/bocca-serriola/` | posizionata ma **sotto Motoevasioni** |
| brand `fotomoto click` | home | ok |

**Non presidiato — e qui c'è il volume vero:**

| Cluster | Query esempio | Pagina che dovrebbe esistere | Esiste? |
|---|---|---|---|
| Intento di recupero generico | "mi hanno fotografato in moto sul passo, dove trovo la foto" | Pagina dedicata all'intento | ❌ |
| Passo come destinazione | "bocca serriola in moto", "passo bocca serriola strada" | `/passi/bocca-serriola/` | ❌ |
| Itinerari | "itinerari moto Umbria", "passi in moto Appennino" | `/guide/` | ❌ |
| Meteo/condizioni | "bocca serriola meteo moto", "strada aperta" | `/passi/{p}/condizioni/` | ❌ |
| Foto tematiche | "foto moto in piega", "moto in curva foto" | `/gallerie/moto-in-piega/` | ❌ |
| Per modello | "foto ducati panigale in piega" | `/moto/{modello}/` | ❌ |
| Professionale | "fotografo moto professionale", "servizio fotografico moto" | Pagina servizi | ❌ |
| B2B / eventi | "fotografo motoraduno", "foto trackday", "fotografo evento moto" | `/servizi/eventi/` | ❌ |
| Regalo | "regalo per motociclista", "idea regalo biker" | Landing stagionale | ❌ |
| Stampa | "stampa foto moto su tela", "quadro moto personalizzato" | Pagina prodotto stampa | ❌ |
| Internazionale | "motorcycle photos italian passes", "Motorradfotos Italien Pass" | `/en/`, `/de/` | ❌ |

**Non hai un blog. Non hai contenuto pre-giro. Non hai una sola pagina che parli dei passi come luoghi.** Motoevasioni ha un magazine; PicMood ha un blog con classifiche dei passi. Tu hai zero.

---

## 6. Local SEO

### 6.1 Realtà, senza illusioni

Non hai una sede fisica aperta al pubblico. **Google Business Profile con "punto foto Bocca Serriola" non è conforme alle linee guida** (serve un luogo con presenza stabile e contatto in-person). Ti sconsiglio esplicitamente di provarci: sospensione del profilo e danno al brand. Molti consulenti te lo proporrebbero; è un cattivo consiglio.

Puoi invece aprire **un GBP legittimo come attività ad area di servizio** con l'indirizzo reale (nascosto) e area di servizio = province coperte, categoria "Fotografo" / "Servizio fotografico". Quello è conforme e ti dà: pannello brand, recensioni Google reali, foto, Maps.

### 6.2 La vera Local SEO per questo modello: entità geografiche, non schede aziendali

Il passo è un'**entità reale nel Knowledge Graph** (esiste in OSM, spesso in Wikipedia/Wikidata). La strategia corretta è: **diventare la fonte più autorevole su quell'entità**, non registrare una scheda.

Struttura per ogni passo (una pagina, replicabile):

```
/passi/bocca-serriola/
  H1: Bocca Serriola in moto — la guida completa
  - Dati oggettivi: quota, lunghezza, versanti, comuni, provincia, coordinate
  - Mappa interattiva + traccia GPX scaricabile          ← link magnet
  - Descrizione della strada per motociclisti (curve, fondo, traffico, pericoli)
  - "Il punto foto": dove siamo posizionati e perché
  - Stagionalità e chiusure
  - DATI DAL TUO ARCHIVIO: quante moto passano, in che ore, in che mesi   ← unico al mondo
  - Galleria curata 20-30 scatti migliori con caption reali
  - Cosa c'è intorno: bar, rifugi, punti sosta, distributori
  - FAQ
  - CTA: trova le tue foto
  Schema: Place + Mountain + TouristAttraction + geo + ImageObject + FAQPage
```

### 6.3 Opportunità Local — lista operativa

**A. Passi che già copri (5) — priorità massima**
1. `/passi/bocca-serriola/` — è l'89% del tuo archivio e stai perdendo la SERP. Prioritaria in assoluto.
2. `/passi/capannelle/`
3. `/passi/terminillo/` (entità forte: comprensorio sciistico, molto cercata)
4. `/passi/spino/`
5. `/passi/viamaggio/` — oggi non ha nemmeno un path prodotto proprio (§2.1)

**B. Long tail geografico derivato — 1 pagina ciascuno**
6. `foto moto Umbria` — `/foto-moto-umbria/`
7. `foto moto Marche`
8. `foto moto Appennino umbro-marchigiano`
9. `foto moto provincia di Perugia`
10. `fotografo moto Città di Castello`
11. `fotografo moto Rieti`
12. `foto moto Valtiberina`

**C. Long tail di intento — le query che nessuno presidia perché non le pensa**
13. "chi fotografa i motociclisti sui passi" → **directory nazionale** (§13.6)
14. "mi hanno fotografato in moto dove trovo la foto"
15. "foto moto senza watermark come ottenerla"
16. "quanto costa una foto in moto sul passo"
17. "come si chiama il fotografo di Bocca Serriola"
18. "foto moto domenica {data}" — pagine data-driven auto-generate

**D. Combinatorie località × tema (scalabili dal dataset, ~15-20 pagine per passo)**
19-40. `foto moto in piega {passo}`, `foto moto tornanti {passo}`, `foto moto tramonto {passo}`, `moto in curva {passo}`, `foto moto pioggia {passo}`, `foto moto gruppo {passo}`, `foto moto d'epoca {passo}`, `foto moto sportive {passo}`, `foto maxienduro {passo}`, `foto moto custom {passo}`…

**E. Espansione territoriale — passi non coperti ma cercati**
41-60. Anche senza fotografarli: guida al passo + "non fotografiamo ancora qui, segnalacelo". Cattura domanda informazionale e crea segnale di espansione. Candidati: Passo della Calla, Passo del Muraglione, Passo della Consuma, Bocca Trabaria, Passo di Viamaggio (versante opposto), Valico di Scheggia, Forca Canapine, Passo Godi, Passo delle Radici, Passo della Cisa, Passo dei Mandrioli, Passo della Futa, Passo del Cerreto, Colle Fauniera…

**F. Locali fisici — la mossa local più sottovalutata**
61. **Ogni passo ha un bar/rifugio.** Accordo: cartello + QR al punto foto, loro ti linkano dal sito/Facebook, tu li citi nella pagina passo con link. Ottieni: link locale rilevante, menzione di brand, e — cosa più importante — il **momento di scoperta nel posto giusto**.
62. Concessionarie e officine locali: pagina "partner" reciproca.
63. Motoclub del territorio: sponsorizzazione foto in cambio di link.

### 6.4 Le SERP che stai già perdendo

Per `foto moto Bocca Serriola motociclisti` [MISURATO, 26-07-2026]:
1. Motoevasioni — pagina passo
2. Motoevasioni — pagina sessione (`/passo-di-bocca-serriola/serriola-10-maggio-2025/`)
3. Motoevasioni — photopass-biker
4. **FotoMoto.Click** — `/foto/bocca-serriola/`
…
8. Instagram `@boccaserriola_photopassbiker`

Motoevasioni fotografa **il tuo passo principale** e ti batte con: pagina passo da 1.200-1.500 parole, pagine sessione **con data leggibile in italiano nell'URL**, e un account Instagram dedicato per passo.

---

## 7. Analisi competitiva

### 7.1 Motoevasioni.it — il competitor diretto e attualmente superiore in SERP

**Punti di forza:**
- Pagina passo ricca: ~1.200-1.500 parole, 7 H2, FAQ, storytelling ("Qui l'aria cambia")
- Gallerie organizzate anno → mese → giorno → **fascia oraria da 30 minuti**
- URL sessione leggibili: `/passo-di-bocca-serriola/serriola-10-maggio-2025/`
- Consegna **JPG + RAW** — argomento di qualità fortissimo verso i fotografi/appassionati
- **Tag NFC personalizzati**: il biker con il tag accede a un'area riservata → retention hardware, altissima frizione all'abbandono
- Ecosistema: Evasion Box (esperienze), Biker Points, magazine, shop, network
- Copre Spino, Viamaggio, **Bocca Serriola** — sovrapposizione totale con te

**Punti deboli attaccabili:**
- Consegna via **WhatsApp + TransferNow**: non è un e-commerce, è manuale. **Non scala.** Tu hai una pipeline automatica: è il tuo vantaggio strutturale e non lo stai comunicando da nessuna parte.
- Nessun download immediato
- Nessuna pagina prodotto indicizzabile per singola foto
- Nessun dato aggregato

### 7.2 PicMood — il competitor di scala

96 località, 1.8M foto, società maltese, mappa interattiva, blog con classifiche, recensioni 4.8/5, URL `/shop/place/{luogo}/date/{YYYY-MM-DD}`.

**Forza:** scala geografica e automazione.
**Debolezza:** nessuna identità, nessun autore, nessuna storia. **Zero E-E-A-T.** È un marketplace anonimo. Tu hai un volto e una storia — è la tua leva.

### 7.3 BikersMood / FotoCisa

Fondato 2019 da Victor Agachi al Passo della Cisa. **Ha copertura editoriale su Moto.it e RoadBookMag.** Usa un account Instagram per passo (`@passodelcerreto_bikersmood`).

**Insight competitivo più importante di tutto l'audit:** i tuoi due competitor principali hanno **Digital PR sulla stampa moto nazionale**. Tu no. E hanno **account social geolocalizzati per passo**, che si posizionano nella SERP del passo. Tu hai un solo account nazionale.

### 7.4 BikeriX

Concorrente minore, stesso modello. Da monitorare.

### 7.5 Gap sfruttabili (dove nessuno di loro è)

| Gap | Chi lo presidia | Sfruttabilità |
|---|---|---|
| Contenuto informativo sul passo come destinazione | Motoevasioni parzialmente | Alta |
| Dati aggregati sul traffico motociclistico | **Nessuno** | **Altissima — è tuo in esclusiva** |
| Metadati IPTC / Google Images "Licensable" | **Nessuno** | **Altissima** |
| Mercato internazionale (EN/DE/NL) | **Nessuno** | **Altissima** |
| Video slow-motion come categoria SEO | Nessuno | Alta (ne hai già 12) |
| Collezioni per modello di moto | **Nessuno** | Alta |
| Schema.org avanzato | Nessuno | Media-alta |
| Presenza in risposte AI | Nessuno | Alta |

---

## 8. E-E-A-T

### 8.1 Situazione

Hai la materia prima migliore possibile — un fondatore reale, con volto, nome e una storia vera — e **non è codificata in nessun modo che una macchina possa leggere**.

[MISURATO] `/dietro-fotomoto-click/`: ottima pagina narrativa, foto `emilio-latini-fondatore-fotomoto-click.webp`, 6 H2. Schema presente: **solo `BreadcrumbList`**. Nessun `Person`, nessun `AboutPage`, nessun `sameAs`.

Sulla home: `Organization` con `sameAs` verso Facebook e Instagram, `email`, `telephone`. Nessun `founder`, nessun indirizzo, nessuna P.IVA nel markup.

C'è una sezione "Recensioni" in home **senza alcun markup** e senza fonte verificabile.

### 8.2 Cosa fare — in ordine di ritorno

1. **Entità `Person` per Emilio Latini**, con `@id` stabile, `jobTitle`, `knowsAbout`, `sameAs` (Instagram, Facebook, LinkedIn), collegata come `founder` e `author` dell'Organization. Ripetuta su ogni pagina che scrive.
2. **`author` e `creator` su ogni foto.** Ogni `ImageObject` deve avere `creator: {@id: .../#emilio}` e `copyrightNotice`. È vero, è verificabile, ed è **esattamente il segnale di "esperienza di prima mano" che Google dichiara di premiare**: sei letteralmente stato lì, con la macchina in mano, quel giorno a quell'ora.
3. **Prova di presenza operativa.** Il componente "Dove trovarci questo weekend" già esiste in home. È E-E-A-T puro: nessun aggregatore può dire dove sarà fisicamente domenica. Va reso una pagina propria, storicizzata (`/dove-siamo/`) con archivio delle presenze passate → diventa la prova pubblica di 54 uscite reali.
4. **Recensioni verificabili di terza parte.** Trustpilot o Google (via GBP service-area). ⚠️ Nota onesta: **Google non mostra più rich result di recensione per markup self-serving su Organization/LocalBusiness.** Le stelle in SERP non le ottieni mettendo `AggregateRating` sul tuo sito. Le ottieni con un profilo esterno reale. Non farti vendere il contrario.
5. **Trasparenza legale:** P.IVA, ragione sociale e indirizzo visibili in footer e in `Organization`. Oggi non ci sono nel markup.
6. **Pagina metodo/attrezzatura:** che corpo macchina, che ottiche, che flusso di lavoro, perché 6000×4000 a 240 DPI. È E-E-A-T tecnico e allo stesso tempo argomento di vendita contro il RAW di Motoevasioni.
7. **Policy privacy fotografica in evidenza.** Già presente nella pagina prodotto ed è scritta bene — è un segnale di trust notevole in un settore dove fotografi persone senza consenso preventivo. **Merita una pagina propria e visibilità**, non di stare in fondo a un prodotto.

---

## 9. UX (e il suo effetto indiretto sul ranking)

### 9.1 Il difetto UX più costoso del sito

**Un motociclista che ha percorso il passo alle 15:30 atterra sulla landing sessione e vede le foto dalle 09:10.** Nell'HTML ci sono 18 foto su 1.430; deve scrollare attraverso ~1.400 immagini per trovarsi. Non c'è selettore orario su quella pagina, non c'è paginazione, non c'è filtro. [MISURATO]

Questo è simultaneamente:
- il peggior problema di conversione del sito,
- il peggior problema di crawl del sito,
- e la causa dei peggiori segnali comportamentali (pogo-sticking verso la SERP).

**Fix:** selettore fascia oraria server-side in cima alla galleria sessione, con URL propri (`?ora=15-16` o `/15-16/`), 40-60 foto per blocco, immagini reali nell'HTML.

### 9.2 Altri interventi

| Intervento | Effetto |
|---|---|
| **Ricerca per fascia oraria in evidenza su ogni landing** | Tempo-alla-foto da minuti a secondi |
| **Riconoscimento moto assistito** (filtra per colore/tipo moto) | Riduzione drastica dello scroll |
| **"Ho trovato la mia foto" / "Non la trovo"** — feedback a 1 click | Dato di prodotto + recupero utenti persi via email |
| **Contatore live** "1.430 foto pubblicate, ultimo aggiornamento 21:58" | Fiducia, riduce l'abbandono in fase di pubblicazione |
| **Anteprima grande con lightbox e zoom** | Un biker vuole vedere la propria moto in dettaglio prima di comprare |
| **Confronto prima/dopo Premium** | Già presente sul prodotto ✅ — portarlo in galleria |
| **Salva e condividi la selezione** (link a una selezione di foto) | Condivisione tra compagni di giro = link + traffico |
| **"Eri in gruppo?"** — mostra foto vicine nel tempo | Aumenta AOV in modo naturale (già presente come "Momenti vicini") |
| **Pagina 404 utile** con finder integrato | Recupero |
| **Preload immagine LCP + dimensioni esplicite** | CLS/LCP |

### 9.3 Ritorno e condivisione

Il modello ha un ciclo naturale di ritorno: chi passa una volta sul passo ci ripassa. Oggi non c'è nulla che lo catturi.
- **Alert "quando sarai fotografato di nuovo"**: l'utente lascia l'email, riceve avviso quando pubblichi una sessione sul suo passo. Costo zero, retention alta.
- **Ogni foto acquistata dovrebbe arrivare con un permalink condivisibile e una card social pronta.** I gruppi Facebook dei passi esistono (ne ho visto uno per Bocca Serriola in SERP): sono il tuo canale di distribuzione naturale.

---

## 10. CRO — il funnel completo

| Stadio | Stato | Problema | Intervento |
|---|---|---|---|
| **Home** | Buona (1.180 parole, 7 sezioni) | Nessun H1; 8 link a un 301; il finder non è linkato | H1; fix link; finder in hero |
| **Hub passi** | Thin | 1 solo H2, nessuna descrizione | Riscrittura + mappa |
| **Landing località** | **Ottima** | manca `max-image-preview:large` | Fix meta robots |
| **Landing sessione** | **Rotta** | 0 parole, 18/1430 foto, TTFB 2.1s | §9.1 — priorità assoluta |
| **Galleria** | Rotta | infinite scroll non crawlabile, nessun filtro orario | Server-side + filtro |
| **Prodotto** | **Buona** | manca H1; video senza VideoObject | Fix minori |
| **Carrello** | noindex ✅ | non verificato in profondità | — |
| **Checkout** | noindex ✅ | non verificato | Verificare guest checkout, Apple/Google Pay |
| **Post-acquisto** | Upsell Premium +15€ ✅ | nessun loop di ritorno | Richiesta recensione + alert nuove sessioni |
| **Email** | non verificabile dall'esterno | — | Sequenza: consegna → recensione → alert |
| **Ritorno** | assente | nessun account, nessun alert | §9.3 |

**Sul pricing** (10€/foto, 9€ da 5, 8€ da 10, video 15€, Premium +15€): la struttura è sensata. Mancano:
- **Pacchetto sessione completa** ("tutte le tue foto di oggi") — l'unità naturale è il passaggio, non la foto;
- **Bundle foto+video** (hai solo 12 video: sono un prodotto sottosfruttato);
- **Stampa fisica** — margine alto, e Motoevasioni la offre;
- **Gift card**, per il cluster "regalo per motociclista" (stagionalità dicembre, oggi non presidiata).

---

## 11. AI Search

### 11.1 Il fatto scomodo

Il tuo `robots.txt` **blocca `GPTBot`, `CCBot` e `Google-Extended`**. Hai chiesto come farti citare da ChatGPT, Claude, Gemini e Perplexity. **Stai bloccando in ingresso proprio quei sistemi.**

Distinzione precisa, perché conta:

| Bot | Cosa controlla | Stato | Effetto reale |
|---|---|---|---|
| `GPTBot` | Training + indice di OpenAI | 🔴 bloccato | ChatGPT non ti conosce |
| `OAI-SearchBot` | Citazioni in ChatGPT Search | 🟢 non menzionato → consentito | Ok, ma senza contenuto da citare |
| `Google-Extended` | Gemini / grounding Vertex | 🔴 bloccato | Gemini non ti usa |
| `CCBot` | Common Crawl → dataset di quasi tutti | 🔴 bloccato | Assente dalla base di conoscenza dell'intero settore |
| `PerplexityBot` | Perplexity | 🟢 consentito | Ok |
| `ClaudeBot` | Anthropic | 🟢 consentito | Ok |
| Googlebot | Ricerca **e AI Overviews** | 🟢 consentito | ✅ Nota: **bloccare Google-Extended NON ti esclude dagli AI Overviews** |

**Decisione da prendere consapevolmente**, perché c'è un trade-off vero: aprire ai crawler AI significa che il tuo contenuto testuale alimenta modelli senza compenso. Ma tu non vendi testo: **vendi file ad alta risoluzione che restano dietro un paywall**. Il testo è marketing. Per un business che vuole essere raccomandato come *servizio*, il calcolo è netto: **apri**.

Raccomandazione: rimuovi i blocchi su `GPTBot` e `Google-Extended`. Su `CCBot` puoi decidere separatamente. **Mantieni bloccati** i crawler che scaricano immagini per training, se identificabili — questo è il confine giusto: testo aperto, immagini protette.

### 11.2 Come farsi citare davvero

I sistemi AI citano contenuti che sono **estraibili, attribuibili, e unici**. Cosa fare:

1. **Risposte dirette in cima alla pagina.** Ogni pagina passo deve aprire con 2-3 frasi che rispondono letteralmente alla domanda ("Sì, a Bocca Serriola c'è un fotografo. FotoMoto.Click fotografa il passo ogni weekend da aprile a settembre; le foto sono online entro 24 ore su fotomoto.click.").
2. **Fatti verificabili e numerici.** Gli LLM citano numeri. "53.683 foto, 54 sessioni, 5 passi, dal marzo 2025" è citabile. "Il miglior servizio fotografico" non lo è.
3. **`llms.txt`** in root: indice curato in Markdown di cosa sei e quali pagine contano. Costo: 30 minuti.
4. **Schema.org denso** — è la forma più affidabile di estrazione (§12).
5. **Presenza su fonti terze.** Gli LLM ripetono ciò che leggono altrove. Motoevasioni e BikersMood sono su Moto.it e RoadBookMag; per questo compaiono nelle risposte. **La Digital PR è la vera SEO per AI.**
6. **Wikidata**: crea l'entità per FotoMoto.Click e collegala ai passi. È il grafo che quasi tutti i sistemi consultano.
7. **Contenuto in inglese.** La maggior parte dei modelli ragiona meglio in inglese e cita più volentieri fonti in inglese. Una versione EN ti rende citabile per "motorcycle photography Italian mountain passes".

---

## 12. Schema.org

### 12.1 Situazione attuale [MISURATO]

| Pagina | Schema presente | Manca |
|---|---|---|
| Home | Organization, WebSite+SearchAction, AboutPage, ImageObject, ContactPoint | LocalBusiness/ProfessionalService, Person, ItemList località, Event |
| `/foto/{loc}/` | CollectionPage, FAQPage, BreadcrumbList, Organization, ImageObject | Place, ItemList, Person |
| `/foto/{loc}/{data}/` | CollectionPage, BreadcrumbList | **Event, ItemList, ImageObject, Place** |
| Prodotto foto | Product, Offer, Brand, ItemPage, ImageObject ×3, Breadcrumb | **license, acquireLicensePage, creator, contentLocation, dateCreated** |
| Prodotto video | Product, Offer, ItemPage | **VideoObject** |
| `/dietro-.../` | solo BreadcrumbList | **Person, AboutPage** |
| Pagine vuote ×1604 | solo BreadcrumbList | irrilevante — vanno eliminate |

### 12.2 Cosa implementare, incluso l'insolito

**Standard ma mancante:**
- `ProfessionalService` / `LocalBusiness` con `areaServed`, `priceRange`, `openingHours` (stagionali)
- `Person` per Emilio, con `sameAs` e `knowsAbout`
- `ItemList` su ogni collezione, con `numberOfItems` reale
- `BreadcrumbList` ✅ già ok
- `Offer` con `priceValidUntil`, `shippingDetails` (digitale), `hasMerchantReturnPolicy` — Google li richiede per i rich result Product

**Non comune e ad alto valore per questo modello:**

1. **`ImageObject` con `license` + `acquireLicensePage` + `creditText` + `creator` + `copyrightNotice`.**
   → Abilita il badge **"Licenziabile"** in Google Images, con link diretto alla pagina d'acquisto. **Per un business che vende fotografie, questa è la singola funzione SERP più rilevante che esista, e in questo settore in Italia non la usa nessuno.** §13.3.

2. **`Event` per la sessione fotografica.** Una sessione *è* un evento: `startDate`, `endDate`, `location: Place(passo)`, `organizer`, `about`, `image`. Trasforma 52 pagine vuote in 52 entità evento. E il componente "Dove trovarci questo weekend" diventa un `Event` futuro → possibile presenza nelle esperienze evento di Google.

3. **`Place` / `Mountain` / `TouristAttraction`** per ogni passo, con `geo`, `elevation`, `containedInPlace`, `sameAs` → Wikipedia/Wikidata/OSM. Ti aggancia al Knowledge Graph.

4. **`Dataset`** per l'archivio (`/passi/{p}/dati/`): `distribution`, `temporalCoverage`, `spatialCoverage`, `variableMeasured`. Ti rende indicizzabile in **Google Dataset Search** — un indice dove non c'è letteralmente nessun concorrente del tuo settore. Bizzarro, e proprio per questo prezioso.

5. **`VideoObject`** sui 12 video: `thumbnailUrl`, `uploadDate`, `duration`, `contentUrl`/`embedUrl`, `regionsAllowed`. Sblocca video rich result e Google Video.

6. **`CreativeWork` / `Photograph`** con `dateCreated` (data+ora reale dello scatto), `contentLocation`, `exifData`. Semanticamente più corretto di `Product` per la fotografia, e usabile in parallelo.

7. **`Service`** con `serviceType: "Fotografia motociclistica"`, `areaServed`, `hasOfferCatalog`.

8. **`SpecialAnnouncement`** — utilizzabile per comunicare "passo chiuso / sessione rinviata per maltempo". Insolito, ma calzante.

9. **`SearchAction` con `potentialAction` puntato al finder reale**, non a `/?s=` (che oggi è in `Disallow` nel robots.txt — [MISURATO] contraddizione: dichiari a Google una SearchAction verso un URL che gli vieti di crawlare).

10. **`speakable`** sulle risposte brevi delle pagine passo, per l'estrazione vocale/AI.

---

## 13. Opportunità inesplorate — la sezione che conta

### 13.1 💣 Deindicizzare per posizionarsi

Già argomentata in §3.1. La ripeto qui perché è la mossa numero uno e sembra sbagliata.

53.683 pagine con domanda di ricerca zero non sono "più contenuto". Sono un **diluente**. Google alloca crawl e attenzione in proporzione al valore percepito medio. Riducendo l'indice a ~2.500 URL densi ottieni: crawl budget ×22, qualità media per pagina che si alza di un ordine di grandezza, e — soprattutto — **le sessioni nuove che si indicizzano in ore invece che mai**.

È il contrario di ciò che dice l'intuizione. È anche il rimedio diretto al problema per cui questo repository esiste.

### 13.2 🚀 Il dataset: da fotografo a fonte di dati

Possiedi 53.683 osservazioni di traffico motociclistico reale: passo, data, ora esatta, direzione, e — con computer vision sul tuo stesso archivio — modello e colore della moto. **Nessun ente, nessuna rivista, nessun competitor ha questo dato.**

Contenuti derivabili, tutti veri e tutti impossibili da copiare:
- `/passi/{p}/dati/` — "Bocca Serriola in numeri": moto/ora, giorno più affollato, orario di punta, curva stagionale, primo e ultimo weekend di stagione
- "A che ora passano più moto su Bocca Serriola" — query informazionale reale, risposta esclusiva
- "Il weekend più trafficato del 2026 sui passi dell'Appennino centro-italiano"
- "Quando inizia la stagione moto: cosa dicono 53.000 passaggi"
- "Le 20 moto più fotografate sui passi dell'Italia centrale nel 2026" — **notiziabile, la stampa moto lo pubblica**
- Report annuale scaricabile → link magnet permanente

Questo è **contenuto da original research**: la categoria che Google e i sistemi AI citano più volentieri, perché è primaria.

**Impatto SEO 10 | Difficoltà 6 | 3-6 settimane | ROI altissimo | P1**

### 13.3 💎 IPTC + "Licenziabile" in Google Images

Google mostra in Google Images un badge **"Licenziabile"** con link "Ottieni questa immagine" per le immagini che dichiarano licenza — via metadati IPTC (`Web Statement of Rights`, `Licensor URL`) o via `ImageObject` (`license`, `acquireLicensePage`).

Tu produci migliaia di immagini l'anno. **In Italia, nel settore fotografia motociclistica, non lo fa nessuno.** Significa:
- un CTA d'acquisto **dentro Google Images**, che è dove un motociclista cerca "moto in piega bocca serriola";
- attribuzione esplicita del credito (E-E-A-T);
- un canale che i concorrenti non presidiano.

Implementazione: `exiftool` nella pipeline di export (già automatizzata) + campi schema. **Il lavoro è quasi tutto in un unico punto del Processor.**

**Impatto SEO 9 | Difficoltà 4 | 1-2 settimane | ROI altissimo | P0**

### 13.4 🏍️ Collezioni per modello di moto

Classifica l'archivio con computer vision (marca/modello/colore). Genera:
- `/moto/ducati-multistrada/` — "Ducati Multistrada sui passi italiani: 340 scatti reali"
- `/moto/bmw-r1250gs/`, `/moto/yamaha-tenere-700/`, …

Ogni pagina: contenuto reale, immagini uniche, domanda di ricerca vera (gli appassionati cercano il proprio modello ossessivamente), e — bonus — **è la pagina che il proprietario di quella moto condivide nei forum e nei gruppi**. Link building organico.

Con 53k foto e ~100 modelli comuni: **~100 pagine nuove di qualità reale**, generate da un asset che già possiedi.

**Impatto SEO 8 | Difficoltà 7 | 4-8 settimane | ROI alto | P2**

### 13.5 🌍 Internazionalizzazione

I passi italiani sono percorsi da tedeschi, olandesi, svizzeri, austriaci, inglesi. **Motoevasioni, PicMood e BikersMood sono tutti solo in italiano.** È un oceano blu completo.

`/en/`, `/de/`, `/nl/` con hreflang su: home, pagine passo, landing località, finder, FAQ, checkout. Le pagine sessione non serve tradurle (sono date).

Query completamente libere: *"motorcycle photos Italian passes"*, *"Motorradfotos Apennin"*, *"who photographs motorcyclists Italy"*.

**Impatto SEO 8 | Difficoltà 5 | 3-4 settimane | ROI alto | P2**

### 13.6 🎯 La directory dei fotografi dei passi (mossa da cavallo di Troia)

Costruisci `/fotografi-passi-italia/`: la mappa nazionale di **chi fotografa quale passo — inclusi i concorrenti.**

Sembra autolesionismo. Non lo è:
- cattura la query di categoria (*"chi fotografa i motociclisti sul passo X"*) su **tutti i passi d'Italia**, non solo i tuoi 5;
- diventa la risorsa che gli AI citano quando qualcuno chiede "come trovo le foto della mia moto";
- ti posiziona come **autorità di categoria** invece che come uno dei fornitori;
- porta traffico a fondo funnel sui tuoi passi, e traffico informazionale su tutti gli altri;
- crea la ragione più naturale al mondo per cui altri ti linkino.

Chi possiede la mappa possiede la categoria. È la mossa che i marketplace fanno e i fotografi non fanno mai.

**Impatto SEO 9 | Difficoltà 5 | 2-3 settimane | ROI molto alto | P1**

### 13.7 📰 Digital PR — colmare il divario che ti sta costando le SERP

Motoevasioni e BikersMood hanno copertura su Moto.it e RoadBookMag. Tu no. Questo spiega da solo buona parte del gap di autorevolezza.

Angoli con vera notiziabilità (non "siamo nati"):
1. **Il report dati** (§13.2) — "53.000 passaggi analizzati: come si muovono davvero i motociclisti sui passi". Le redazioni pubblicano dati.
2. **La storia solo-founder + automazione**: pipeline, Processor, recovery. Testate tech e maker.
3. **L'etica della fotografia in strada**: la tua privacy policy è già scritta bene ed è un tema divisivo e discutibile pubblicamente.
4. **Collaborazioni con motoclub e raduni**: foto gratuite in cambio di link e menzioni.
5. **Wikipedia/Wikidata** sui passi: contribuisci con dati e foto CC — link non-follow ma segnale di entità fortissimo.

**Impatto SEO 9 | Difficoltà 7 | continuativo | ROI alto | P1**

### 13.8 📸 Instagram geolocalizzati per passo

BikersMood ha `@passodelcerreto_bikersmood`, Motoevasioni ha `@boccaserriola_photopassbiker` — **e quest'ultimo compare nella SERP di Bocca Serriola sopra molte pagine web.** Tu hai un unico account nazionale.

Un account per passo: `@boccaserriola_fotomotoclick`, ecc. Occupa la SERP di entità, cattura la ricerca sociale, e crea un canale locale. Costo marginale quasi zero: le foto le hai già.

**Impatto SEO 6 | Difficoltà 2 | 1 settimana | ROI alto | P1**

### 13.9 ⚡ Il "passaggio" come oggetto pubblico permanente

Cambio concettuale: la pagina foto non è una scheda prodotto, è **il record permanente di un passaggio**. Data, ora esatta, passo, direzione, meteo di quel momento, quante moto sono transitate in quell'ora.

Anche senza acquisto, quella pagina ha valore per chi c'era. Diventa condivisibile, citabile, linkabile. È il contrario di una thin page: è un **dato con contesto**.

Applicalo alle 20-40 foto/sessione che scegli di indicizzare (§3.1 opzione B): diventano contenuto vero, non duplicati.

### 13.10 🔁 Il loop del ritorno

Il motociclista che compra una foto **ripasserà su quel passo**. Nessuno sta costruendo questo loop.
- Alert email "sarò a Bocca Serriola domenica" → traffico prevedibile e ricorrente
- "Il tuo anno in moto": a dicembre, tutte le foto che ti abbiamo scattato nel 2026 → recap emotivo, altissima condivisione, picco di vendite fuori stagione
- Programma fedeltà: 5° foto in regalo

Non è SEO diretta, ma genera brand search — che è il segnale che Google considera più difficile da falsificare.

### 13.11 🏔️ Il punto foto come luogo

Un cartello fisico + QR al punto di scatto. Il motociclista che passa **sa** che è stato fotografato e dove cercare. Elimina la fase di scoperta, ed è l'unica cosa che nessun competitor digitale può copiare a distanza. In combinazione con l'accordo col bar del passo (§6.3-F), presidi il territorio fisico e digitale insieme.

---

## 14. Roadmap

### Legenda
**Impatto SEO** 1-10 · **Difficoltà** 1-10 · **ROI**: rapporto valore/sforzo · **Priorità**: P0 subito → P3 dopo

### 🔴 PRIMA DI TUTTO — bloccante

| # | Azione | Imp. | Diff. | Tempo | ROI | Pri |
|---|---|---|---|---|---|---|
| **0** | **Risolvere "Impossibile recuperare" sulle sitemap in GSC.** Entrambe le sitemap inviate non sono mai state lette da Google (0 pagine rilevate, "Ultima lettura" vuota). Ipotesi principale: regola WAF/Cloudflare che restituisce 403 a certi client — durante il crawl del 26-07 il fetcher ha ricevuto 403 su `/` e su `sitemap_index.xml` mentre curl con UA browser riceveva 200. Vedi [evidenze §1](2026-07-26-gsc-ga-evidence.md#1--scoperta-critica--google-non-riesce-a-leggere-le-sitemap) | **10** | 3 | 1-2 g | ⭐⭐⭐⭐⭐ | **P0 assoluta** |

### 🟢 QUICK WINS — 0-2 settimane

| # | Azione | Imp. | Diff. | Tempo | ROI | Pri |
|---|---|---|---|---|---|---|
| 1 | **Eliminare/deindicizzare le 1.604 pagine vuote `fotomotoclick-*`** e rimuoverle dal page-sitemap | 9 | 2 | 1 g | ⭐⭐⭐⭐⭐ | **P0** |
| 2 | **Correggere gli `alt` duplicati** nelle gallerie sessione (template con passo+data+ora+numero) | 8 | 2 | 1 g | ⭐⭐⭐⭐⭐ | **P0** |
| 3 | **Aggiungere H1** su home, prodotto, sessione, contatti, link, collabora, scopri-qualità | 6 | 1 | 4 h | ⭐⭐⭐⭐⭐ | **P0** |
| 4 | **Sostituire gli 8 link home → `/gallerie-foto/` (301)** con il target reale | 4 | 1 | 15 min | ⭐⭐⭐⭐⭐ | **P0** |
| 5 | **Correggere `<meta description>` delle sessioni** (oggi "Foto del 19-07-2026 \|") | 6 | 2 | 4 h | ⭐⭐⭐⭐⭐ | **P0** |
| 6 | **robots.txt**: rimuovere `AdsBot`, rimuovere gruppo `Googlebot-Image`, aggiungere disallow su `?add-to-cart`, `?orderby`, `/feed/` | 6 | 1 | 1 h | ⭐⭐⭐⭐⭐ | **P0** |
| 7 | **Sbloccare GPTBot e Google-Extended** (decisione consapevole, §11.1) | 7 | 1 | 15 min | ⭐⭐⭐⭐⭐ | **P0** |
| 8 | **`max-image-preview:large` anche sulla landing località** | 5 | 1 | 15 min | ⭐⭐⭐⭐⭐ | **P0** |
| 9 | **`/page/N/` (N>1) → 404 o canonical a page/1** | 7 | 2 | 4 h | ⭐⭐⭐⭐⭐ | **P0** |
| 10 | **Rimuovere `/gallerie-foto/` da product-sitemap1** | 3 | 1 | 15 min | ⭐⭐⭐⭐ | P1 |
| 11 | **Warmup cache post-pubblicazione** (curl delle landing dopo `generate-json`) | 8 | 3 | 1 g | ⭐⭐⭐⭐⭐ | **P0** |
| 12 | **Sitemap dedicata "ultime 20 sessioni"** con lastmod accurato | 8 | 3 | 1 g | ⭐⭐⭐⭐⭐ | **P0** |
| 13 | **Popolare `evidence/gsc/`**: export Rendimento + Copertura, 16 mesi | 9 | 2 | 2 h | ⭐⭐⭐⭐⭐ | **P0** |
| 14 | **`Person` + `founder` schema** per Emilio | 6 | 2 | 3 h | ⭐⭐⭐⭐ | P1 |
| 15 | **`VideoObject`** sui 12 video | 6 | 3 | 1 g | ⭐⭐⭐⭐ | P1 |
| 16 | **`llms.txt`** | 5 | 1 | 30 min | ⭐⭐⭐⭐ | P1 |
| 17 | **Instagram per passo** (5 account) | 6 | 2 | 1 sett | ⭐⭐⭐⭐ | P1 |
| 18 | **Rimuovere/noindex i 4 `product_tag`** duplicati | 4 | 1 | 30 min | ⭐⭐⭐⭐ | P1 |
| 19 | **Fix `SearchAction`** → punta al finder reale, non a `/?s=` (bloccato in robots) | 4 | 1 | 30 min | ⭐⭐⭐⭐ | P1 |

### 🟡 ALTO IMPATTO — 2-8 settimane

| # | Azione | Imp. | Diff. | Tempo | ROI | Pri |
|---|---|---|---|---|---|---|
| 20 | **Riscrivere il template landing sessione**: H1, 150-250 parole data-driven, ItemList, Event schema, selettore fascia oraria server-side | 10 | 5 | 2 sett | ⭐⭐⭐⭐⭐ | **P0** |
| 21 | **Curated indexing**: 20-40 foto/sessione indicizzabili con caption unica, resto `noindex` + fuori sitemap | 10 | 5 | 2 sett | ⭐⭐⭐⭐⭐ | **P0** |
| 22 | **IPTC + `license`/`acquireLicensePage`** → badge Licenziabile in Google Images | 9 | 4 | 1-2 sett | ⭐⭐⭐⭐⭐ | **P0** |
| 23 | **Paginazione server-side reale** con link testuali per fascia oraria | 8 | 5 | 1-2 sett | ⭐⭐⭐⭐ | P1 |
| 24 | **Cloudflare Cache Rule sull'HTML** + purge API nella pipeline | 8 | 4 | 1 sett | ⭐⭐⭐⭐⭐ | **P0** |
| 25 | **Correggere i 3.463 prodotti con path-data** (primary category → località) | 7 | 6 | 1-2 sett | ⭐⭐⭐⭐ | P1 |
| 26 | **5 pagine passo `/passi/{p}/`** complete (1.200+ parole, mappa, GPX, dati, FAQ, Place schema) | 9 | 6 | 3-4 sett | ⭐⭐⭐⭐⭐ | **P0** |
| 27 | **Riscrivere `/passi-e-valichi/`** come hub con mappa | 6 | 3 | 3 g | ⭐⭐⭐⭐ | P1 |
| 28 | **Link sessione ↔ sessione** (precedente/successiva sullo stesso passo) + archivio per anno/mese | 7 | 3 | 1 sett | ⭐⭐⭐⭐⭐ | P1 |
| 29 | **`/dove-siamo/`** con archivio presenze + `Event` schema | 7 | 4 | 1 sett | ⭐⭐⭐⭐ | P1 |
| 30 | **Directory fotografi dei passi** (§13.6) | 9 | 5 | 2-3 sett | ⭐⭐⭐⭐⭐ | P1 |
| 31 | **GBP service-area** + Trustpilot + loop recensioni post-acquisto | 7 | 4 | 2 sett | ⭐⭐⭐⭐ | P1 |
| 32 | **12-15 pagine long-tail geografiche** (Umbria, Marche, province, Valtiberina…) | 7 | 4 | 2-3 sett | ⭐⭐⭐⭐ | P2 |
| 33 | **Alert email "nuova sessione sul tuo passo"** | 7 | 4 | 1-2 sett | ⭐⭐⭐⭐⭐ | P1 |
| 34 | **Pacchetto sessione completa + bundle foto/video + gift card** | 6 | 4 | 2 sett | ⭐⭐⭐⭐ | P2 |
| 35 | **Pagina metodo/attrezzatura** (E-E-A-T tecnico) | 5 | 2 | 3 g | ⭐⭐⭐⭐ | P2 |

### 🔴 GAME CHANGER — 2-6 mesi

| # | Azione | Imp. | Diff. | Tempo | ROI | Pri |
|---|---|---|---|---|---|---|
| 36 | **Il dataset: `/passi/{p}/dati/` + report annuale + `Dataset` schema + Google Dataset Search** (§13.2) | 10 | 6 | 1-2 mesi | ⭐⭐⭐⭐⭐ | **P1** |
| 37 | **Digital PR sistematica** — Moto.it, RoadBookMag, Dueruote, testate tech; angolo dati (§13.7) | 9 | 7 | continuo | ⭐⭐⭐⭐⭐ | **P1** |
| 38 | **Internazionalizzazione EN/DE/NL** con hreflang (§13.5) | 8 | 5 | 1-2 mesi | ⭐⭐⭐⭐⭐ | P2 |
| 39 | **Collezioni per modello di moto** via computer vision (§13.4) | 8 | 7 | 2 mesi | ⭐⭐⭐⭐ | P2 |
| 40 | **Collezioni tematiche curate** (in piega, tramonto, pioggia, gruppo, epoca) | 7 | 5 | 1 mese | ⭐⭐⭐⭐ | P2 |
| 41 | **Espansione a 10-15 nuovi passi** con pagine pubblicate prima della copertura fotografica | 9 | 8 | 3-6 mesi | ⭐⭐⭐⭐ | P2 |
| 42 | **Riconoscimento moto assistito nel finder** (filtro per colore/tipo) | 7 | 8 | 2-3 mesi | ⭐⭐⭐⭐ | P3 |
| 43 | **Cartelli fisici + QR ai punti foto + accordi con i bar dei passi** (§13.11, §6.3-F) | 7 | 5 | 1-2 mesi | ⭐⭐⭐⭐⭐ | P1 |
| 44 | **"Il tuo anno in moto"** — recap annuale personalizzato | 6 | 6 | 1 mese | ⭐⭐⭐⭐ | P3 |
| 45 | **Wikidata + contributi Wikipedia/OSM sui passi** | 6 | 4 | 3 sett | ⭐⭐⭐⭐ | P2 |
| 46 | **Migrazione a `/passi/{passo}/…`** con redirect controllati | 6 | 9 | 2-3 mesi | ⭐⭐ | P3 |

---

## 15. I primi 10 giorni, in ordine

Se domani mattina puoi fare una cosa sola alla volta, questo è l'ordine. È costruito perché ogni passo renda più efficace il successivo.

> **Aggiornato il 26-07-2026 dopo l'accesso a GSC/GA4.** I punti 1 e 3 originali (esportare GSC, verificare i clic dei prodotti) sono **già fatti**: vedi [2026-07-26-gsc-ga-evidence.md](2026-07-26-gsc-ga-evidence.md). Al loro posto entra il fix delle sitemap, che è bloccante.

1. **Risolvi "Impossibile recuperare" sulle sitemap** (azione 0). Finché Google non legge le sitemap, tutto il resto è cieco.
2. **Elimina le 1.604 pagine vuote** e rigenera le sitemap.
3. **Investiga le 1.129 pagine 404 con convalida non riuscita** e il pattern URL duplicato `/gallerie/fotomotoclick-*` (voci N1 e N2 delle evidenze).
4. **Correggi gli `alt`** delle gallerie.
5. **robots.txt**: pulizia + sblocco crawler AI.
6. **H1 ovunque** + fix meta description sessioni + fix degli 8 link 301.
7. **Warmup cache** post-`generate-json` (il TTFB di 2,1 s è il freno più diretto).
8. **Sitemap "ultime sessioni"**.
9. **Riscrivi il template landing sessione.** È la pagina che decide se il tuo modello funziona in SEO.
10. **Pagina `/passi/bocca-serriola/` completa.** È l'89% del tuo archivio ed è la SERP che stai perdendo contro Motoevasioni.

Da lì: IPTC/Licenziabile (§13.3) e il dataset (§13.2) sono le due leve che ti spostano da "un fotografo con un sito" a "la fonte di riferimento sulla fotografia motociclistica dei passi italiani".

---

## 16. Critica onesta — cosa non condivido dell'impostazione attuale

Il brief chiedeva di criticare. Cinque punti.

1. **Hai automatizzato la produzione prima di aver definito cosa doveva essere pubblico.** La pipeline è eccellente e genera 1.500 URL a sessione. Ma nessuno ha mai deciso *quali di quegli URL meritino di esistere per un motore di ricerca*. L'automazione ha amplificato una scelta architetturale mai presa consapevolmente. È il classico caso in cui l'ingegneria supera la strategia.

2. **L'investigation sull'indicizzazione ha cercato la causa nel posto sbagliato — pur facendolo benissimo.** Rank Math, sitemap stale, cache: tutto vero, tutto corretto, tutto documentato con rigore. Ma erano problemi di *trasporto*. Il problema è di *carico*: stai chiedendo a Google di processare 55.000 URL per servirtene 70, con landing sessione a 2,1 secondi e zero contenuto. Nessun fix di sitemap può compensarlo. La buona notizia è che il metodo epistemico del repo è esattamente quello giusto per verificare anche questa ipotesi — e questo audit ti dà l'evidenza da registrare.

3. **Stai trattando le foto come prodotti perché WooCommerce le tratta così.** È l'unica scelta veramente strutturale del progetto, ed è stata ereditata da un default di piattaforma, non decisa.

4. **Hai un fondatore reale, una storia vera e 16 mesi di presenza fisica sui passi — e li stai comunicando meno di quanto faccia un marketplace anonimo maltese.** Il tuo unico vantaggio incopiabile è l'unica cosa che non stai capitalizzando.

5. **Bloccare i crawler AI mentre si chiede di essere citati dai sistemi AI** è una contraddizione operativa che vale la pena risolvere consapevolmente, in un senso o nell'altro.

---

*Documento prodotto il 26-07-2026. Le misurazioni HTTP riflettono lo stato del sito a quella data. Le affermazioni marcate [MISURATO] sono riproducibili; quelle marcate [STIMA] richiedono verifica prima di essere trattate come fatti.*
