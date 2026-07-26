# Pagina `/passi/bocca-serriola/` — note di implementazione

Roadmap master audit **azione 26 (P0)**. Rif. §6.2 (struttura pagina passo) e §6.4 (SERP persa
contro Motoevasioni). Bocca Serriola è **~89% dell'archivio** ed è la SERP dove oggi Motoevasioni
occupa posizione 1–2.

`index.html` è un **mockup navigabile** completo di copy e schema. Il dev riprende testo, struttura
e JSON-LD dentro il template WordPress/Elementor (nuovo livello URL `/passi/`, che **affianca** il
transazionale `/foto/`, senza migrare nulla — master audit §3.2).

## Meta
- **Title:** `Bocca Serriola in moto: la guida completa al valico (730 m) | FotoMoto.Click`
- **Description:** vedi `<meta name="description">` nel file.
- **Canonical:** `https://fotomoto.click/passi/bocca-serriola/`
- **robots:** `index, follow, max-image-preview:large` (le pagine passo DEVONO indicizzarsi).

## Keyword mappate (cluster non presidiato oggi — master audit §5.3)
| Intento | Query esempio | Coperta dalla pagina |
|---|---|---|
| Passo come destinazione | "bocca serriola moto", "bocca serriola strada" | H1 + sez. "La strada" |
| Recupero foto | "mi hanno fotografato in moto bocca serriola dove trovo la foto" | Hero + FAQ + CTA |
| Chi fotografa | "chi fotografa i motociclisti a bocca serriola" | FAQ 1 |
| Dato oggettivo | "bocca serriola altitudine", "quanto è alto" | "Il valico in breve" + FAQ 3 |
| Versanti | "bocca serriola da apecchio / città di castello" | sez. versanti + FAQ 4 |

## Dati VERIFICATI (26-07-2026) usati nel copy
Fonti: [Wikipedia — Bocca Serriola](https://it.wikipedia.org/wiki/Bocca_Serriola),
[climbfinder — versante Città di Castello](https://climbfinder.com/en/climbs/valico-di-bocca-serriola-citta-di-castello),
[climbfinder — versante Apecchio](https://climbfinder.com/en/climbs/valico-di-bocca-serriola-apecchio).
- 730 m · SS257 Apecchiese · Città di Castello (PG) ↔ Apecchio (PU) · Umbria/Marche
- Spartiacque Val Tiberina / Val Biscubio (bacino Candigliano) · nome locale "La Cima"
- Salita CdC: 15,9 km, +423 m, 2,7% media, ~6,4% max, 3 tornanti
- Salita Apecchio: 7,6 km, +247 m, 3,3% media, 7,9% max, 1 tornante

## Da popolare prima della pubblicazione (marcati `[DA POPOLARE]` nel file)
1. **Coordinate esatte** del punto foto (Google Maps/OSM) → aggiornare `geo` nello schema.
2. **Punto foto**: descrizione precisa (km, versante, riferimento) + foto cartello/QR se presente.
3. **Dati archivio** (§13.2): ora di punta, weekend più affollato, primo/ultimo weekend stagione —
   **calcolati dal dataset**, non inventati. Il totale "48.000+" deriva dal conteggio path
   `/foto/bocca-serriola/` (master audit §2.1: 48.226).
4. **Galleria**: 20–30 scatti curati con alt/caption unici + ImageObject "Licenziabile"
   (`execution-kit/schema/image-licensable.jsonld.html`).
5. **Dintorni**: nomi reali di bar/rifugio al passo, distributori, officine (utili anche per
   link locali — §6.3-F).

## Internal linking (all'atto della pubblicazione)
- Dalla home e da `/passi/` → link a `/passi/bocca-serriola/`.
- Da `/foto/bocca-serriola/` (landing località) → link alla pagina passo, e viceversa.
- Dalla pagina passo → `/cerca-foto-archivio/` (già presente come CTA) e `/dove-siamo/`.
- Sostituisce l'occasione persa: oggi la home spreca 8 link su un 301 (§3.3).

## Verifica post-pubblicazione
- Google Rich Results Test → Place + FAQPage validi.
- GSC URL Inspection → richiesta di indicizzazione della nuova pagina.
- Monitorare la query "foto moto bocca serriola" e "bocca serriola moto" vs Motoevasioni (§6.4).
