# Pixel Meta — migrazione della marcatura da Iubenda a Complianz

> 27-07-2026. **Il pixel Meta è completamente inattivo**, non parzialmente.
> Correzione minima: 4 righe identiche nello snippet WPCode.

## Il fatto

Lo snippet WPCode che emette il pixel marca tutti i suoi blocchi così:

```html
<script type="text/plain" class="_iub_cs_activate-inline" data-iub-purposes="5">
```

`type="text/plain"` impedisce al browser di eseguire lo script: è il normale meccanismo di
blocco preventivo. A sbloccarlo doveva essere **Iubenda**, che però è stato rimosso nella
migrazione a Complianz.

Verifica sull'HTML servito (27-07-2026):

| Controllo | Esito |
|---|---|
| Script Iubenda caricati (`cdn.iubenda.com`, `_iub_cs_activate` come funzione) | **nessuno** |
| Occorrenze di `_iub_cs_activate-inline` | 2 nella pagina prodotto (le sole marcature rimaste) |
| Complianz presente | sì (`complianz-gdpr-premium`), blocca per `data-category` |
| `data-category` sugli script del pixel | **assente** |

→ Nessuno attiva quegli script. Il pixel non parte per **nessun visitatore**, con o senza
consenso. Coerente con Gestione eventi, che riporta "Nessuna attività ricevuta" nei 28 giorni.

**Conseguenza commerciale:** da quando Iubenda è stato rimosso, le campagne Paid Social
ottimizzano senza alcun segnale di conversione.

## La correzione

Nello snippet WPCode, sostituire **tutte e quattro** le occorrenze:

```diff
- <script type="text/plain" class="_iub_cs_activate-inline" data-iub-purposes="5">
+ <script type="text/plain" data-category="marketing" data-service="facebook">
```

Si trovano in:
1. `fm_meta_pixel_base_and_viewcontent()` — base + PageView + ViewContent
2. `fm_meta_pixel_add_to_cart()` — AddToCart
3. `fm_meta_pixel_purchase()` — Purchase

E aggiornare il commento in testa al file, che oggi dice "Compatibile con Iubenda".

> `type="text/plain"` **va lasciato**: è il meccanismo di blocco, identico nei due sistemi.
> Cambia solo chi lo sblocca. Rimuoverlo farebbe partire il pixel senza consenso.

## Verifica dopo il deploy

In una finestra **anonima** (non da admin loggato, e non con un consenso già memorizzato):

1. Aprire una pagina prodotto → il banner Complianz compare → **accettare** i cookie marketing.
2. In DevTools → Rete, filtrare su `facebook.com/tr`. Devono comparire:
   - `ev=PageView`
   - `ev=ViewContent` con `content_ids` valorizzato
3. Cliccare "Aggiungi questa foto" → deve comparire `ev=AddToCart`.
4. In Gestione eventi Meta → scheda **"Testa gli eventi"**, gli stessi eventi in tempo reale.

**Controprova altrettanto importante:** rifiutare i cookie in una nuova finestra anonima e
verificare che a `facebook.com/tr` **non** vada nulla. Se parte lo stesso, la marcatura non
sta bloccando e c'è un problema di conformità, non di misurazione.

## Cosa NON fare

- Non rimuovere `type="text/plain"` per "farlo funzionare subito": il pixel partirebbe senza
  consenso.
- Non toccare altro nello snippet in questo passaggio. Il pixel è rotto al 100%: prima si
  ripristina il comportamento progettato, poi si verifica, poi si estende.

## Passi successivi, dopo la verifica

| # | Cosa | Perché |
|---|---|---|
| 1 | `InitiateCheckout` sulla pagina di checkout | oggi assente; è il buco fra AddToCart e Purchase, e lo stesso buco esiste in GA4 (F-019: purchase 87 > begin_checkout 78) |
| 2 | Evento `Search` sul finder | `/cerca-foto-moto/` è la pagina n.1 del sito (542 utenti, 3 m 59 s) e non emette alcun evento di ricerca standard |
| 3 | **Conversions API server-side** da WooCommerce | anche riparato, il pixel resta legato al consenso. Misurato il 26/07: GA4 cattura l'80% degli ordini e l'85% del ricavo, ma solo l'8,6% delle pagine e il 16% dei client. Gli eventi che partono dal server non dipendono dal browser. |
| 4 | Rimuovere i residui di Iubenda e rivedere il banner | 16% di accettazione stimata. Da fare **dopo** la correzione, altrimenti ogni misura sul consenso è rumore. |

## Nota di metodo

Questo difetto era invisibile a ogni controllo fatto finora: il codice del pixel è corretto,
l'ID è giusto, le pagine lo contengono. Si vede solo confrontando **chi marca** gli script con
**chi è installato** per attivarli. Una migrazione fra due CMP lascia esattamente questo tipo
di residuo, e non produce nessun errore visibile: solo dati che smettono di arrivare.
