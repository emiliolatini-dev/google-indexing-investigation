# Modulo "Scopri il passo" — installazione, uso e manutenzione

File: `execution-kit/wpcode-scopri-il-passo.php` · Dati di riferimento: `content/passi-data.json`
Mockup visivi: `content/landing-info-module/` (Bocca Serriola + altre 4).

Il modulo arricchisce le landing località `/foto/{loc}/` con un blocco informativo sul passo
(strada, versanti, dati, FAQ) + schema `Place`/`FAQPage`. Un solo snippet serve **tutte** le
località: rileva il passo dalla pagina e stampa i dati giusti. Contenuto in accordion `<details>`,
tutto nell'HTML → indicizzabile (niente modale JS).

## Come funziona (in breve)
1. Lo snippet registra lo **shortcode** `[scopri_il_passo]`. Registrare lo shortcode non stampa
   nulla da solo: il blocco appare solo dove inserisci lo shortcode.
2. Quando lo shortcode viene reso su una landing, rileva la località in quest'ordine:
   a. attributo esplicito `[scopri_il_passo loc="bocca-serriola"]`;
   b. `get_queried_object()` se la pagina è l'archivio del termine (product_cat) → usa `->slug`;
   c. fallback: primo segmento dopo `/foto/` nell'URL.
3. Cerca lo slug in `fmc_passi_data()`. Se **non** c'è, non stampa niente (nessun errore).
   Quindi le località senza dati semplicemente non mostrano il blocco.

## Installazione (una volta)
1. WordPress → **WPCode → Add Snippet → Add Your Custom Code (New Snippet) → PHP Snippet**.
2. Incolla il contenuto di `wpcode-scopri-il-passo.php`.
3. Insertion: **Auto Insert** non serve (usiamo lo shortcode). Lascia "Run Everywhere".
4. **Salva e attiva.** Finché non inserisci lo shortcode, il sito non cambia.
5. In **Elementor**, nel template della landing località, aggiungi un widget **Shortcode** dove
   vuoi il blocco (consigliato: sotto ricerca/sessioni) con: `[scopri_il_passo]`.

## ⚠️ Regole di sicurezza
- **Testa prima su staging** (`local-staging-assets/`) o su una sola località con
  `[scopri_il_passo loc="bocca-serriola"]` su una pagina di prova.
- WPCode disattiva da solo uno snippet che generi un errore fatale; comunque non pubblicare in
  orario di punta.
- Lo shortcode è togglabile: per rimuovere il blocco ovunque basta disattivare lo snippet.

---

## ➕ Aggiungere una LOCALITÀ NUOVA (procedura da seguire ogni volta)

Quando FotoMoto.Click inizia a coprire un nuovo passo, il blocco si estende **senza toccare il
codice della logica**: si aggiunge solo un record di dati.

### Passo 1 — Ricerca i fatti reali (una volta, ~2-3 h)
Come fatto per gli altri passi. Servono da fonti verificabili (Wikipedia, climbfinder, siti
turistici/moto). Campi da raccogliere:
- **quota** (m s.l.m.), **strada** (numero + nome, es. "SS257 Apecchiese")
- **regioni** e **province**, **comuni** ai versanti
- **spartiacque** / valli collegate, **catena** montuosa
- **coordinate** del punto foto (Google Maps/OSM) — poi `lat`/`lon`
- **profili di salita** dei versanti (km, dislivello, pendenza, tornanti) se disponibili
- 2-4 frasi di **carattere** unico (cosa rende diverso quel passo)
- link **fonti**

Registra i fatti in `content/passi-data.json` (il data layer di riferimento, con le fonti).

### Passo 2 — Aggiungi il record nello snippet
Nel file/snippet, dentro `fmc_passi_data()`, duplica un blocco esistente e compila. **La chiave
dell'array deve essere lo slug esatto della località** (lo stesso di `/foto/{slug}/`):

```php
'nuovo-passo' => array(
    'title'  => 'Titolo evocativo del passo',
    'lead'   => 'Una-due frasi che raccontano il passo (uniche, non generiche).',
    'facts'  => array( '<b>QUOTA m</b> <span>quota</span>', '<b>STRADA</b>', '<b>REGIONE</b>', '<b>…</b>' ),
    'blocks' => array(
        array( 'La strada', '<p>Descrizione strada/versanti…</p>' ),
        array( 'Il valico in breve', '<table class="fmc-tab"><tr><th>Quota</th><td>… m</td></tr>…</table>' ),
    ),
    'faq' => array(
        array( 'Domanda?', 'Risposta.' ),
        array( 'Quanto è alto …?', '… metri, sulla … .' ),
    ),
    'schema' => array(
        'types'  => array( 'Place', 'TouristAttraction' ),   // per un monte: array('Mountain','TouristAttraction')
        'name'   => 'Nome ufficiale del passo',
        'alt'    => 'Nome alternativo o ""',
        'desc'   => 'Descrizione breve per lo schema.',
        'elev'   => '000',                                    // quota come stringa
        'lat'    => 00.0000, 'lon' => 00.0000,                // coordinate verificate
        'sameAs' => 'https://it.wikipedia.org/wiki/…',        // o "" se non esiste
    ),
),
```

### Passo 3 — Nessun'altra modifica
Non serve toccare la logica né aggiungere shortcode nuovi: se lo shortcode `[scopri_il_passo]` è
già nel template della landing (uguale per tutte le località), il nuovo passo mostra
automaticamente il suo blocco appena esiste il record.

### Passo 4 — Verifica
1. Apri `/foto/nuovo-passo/` → il blocco appare con i dati giusti.
2. **Rich Results Test** (search.google.com/test/rich-results) sull'URL → Place + FAQPage validi.
3. GSC → Controllo URL → richiesta di indicizzazione della landing aggiornata.

## Riferimento campi
| Campo | Obbligatorio | Note |
|---|---|---|
| chiave array | sì | = slug di `/foto/{slug}/` |
| `title` | sì | H3 del blocco (evocativo) |
| `lead` | sì | 1-2 frasi introduttive uniche |
| `facts[]` | sì | pill in cima; HTML minimo `<b>…</b> <span>…</span>` |
| `blocks[]` | sì | ogni voce = `array('Titolo accordion', 'HTML corpo')`; il 1° è aperto di default |
| `faq[]` | consigliato | `array('Domanda','Risposta')`; alimenta blocco visibile **e** schema FAQPage |
| `schema` | sì | `types`, `name`, `alt`, `desc`, `elev`, `lat`, `lon`, `sameAs` |

## Manutenzione
- I dati canonici stanno in `content/passi-data.json` (con le fonti). Lo snippet ne è la
  trasposizione PHP: tieni i due allineati quando aggiorni un dato.
- Per correggere un testo/coordinata: modifica il record e ri-salva lo snippet. Nessun altro passo.
