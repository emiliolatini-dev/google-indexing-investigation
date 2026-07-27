# `/gallerie/` — da 404 pesante a 410 leggero

> Creato il 27-07-2026 su [E-015 §2](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md)
> e sul contesto storico fornito dal proprietario (O-007).
> File: [`fmc-gallerie-410.php`](fmc-gallerie-410.php) · **Reversibile:** si cancella il file.

## Il fatto

Fino al 2025 le sessioni più vecchie venivano archiviate in gallerie di sola lettura sotto
`/gallerie/`, fuori da WooCommerce. Tornando a tenere tutto come prodotti, **una parte delle
foto non è stata reimportata**. Misurato il 27-07-2026:

| Slug | `/gallerie/{slug}/` | `/foto/{loc}/{slug}/` |
|---|---|---|
| `fotomotoclick-bocca-serriola-07-09-2025-1-10-13` | **301** | 200 |
| `fotomotoclick-bocca-serriola-07-09-2025-18-10-14` | **301** | 200 |
| `fotomotoclick-bocca-serriola-07-09-2025-292-10-47` | 404 | 404 |
| `fotomotoclick-bocca-serriola-09-08-2025-567-11-35` | 404 | 404 |

**Il redirect esiste già e funziona.** I 1.129 "Non trovata (404)" di GSC — convalida avviata
il 18/06 e fallita il 01/07 — sono esclusivamente le foto senza successore. Non c'è nulla da
redirezionare: il contenuto non esiste più.

La landing della sessione, invece, resta online: `/foto/bocca-serriola/07-09-2025/` → 200.

## Perché intervenire

- Il 404 attuale pesa **139.937 byte**: è una pagina WordPress completa, renderizzata da PHP.
- Il **5%** delle richieste di Googlebot al sito finisce in 404 (statistiche di scansione, 90 gg).
- Due URL di questo namespace sono andati in **timeout a 34.100 ms**.
- Google ritira un 410 dall'indice più in fretta di un 404, che continua a ritentare per mesi.

Impatto onesto: **moderato**. Non sposta il fatturato. Ma costa mezz'ora, è a rischio zero e
chiude una convalida ferma dal 1° luglio.

## ⚠️ La trappola da evitare

```apache
# NON FARE QUESTO
RewriteRule ^gallerie/.+ - [G,L]
```

Restituirebbe 410 a **tutto** il namespace, distruggendo i 301 che oggi funzionano. Il 410 va
dato solo sul ramo in cui la risoluzione è già fallita — da cui l'hook su `template_redirect`
con guardia `is_404()`.

## Installazione

`/home/fotomoto.click/public_html/wp-content/mu-plugins/fmc-gallerie-410.php`

## Verifica obbligatoria dopo l'installazione

Deve confermare **entrambe** le colonne: i 301 intatti e i 404 diventati 410.

```bash
UA="Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)"
for s in fotomotoclick-bocca-serriola-07-09-2025-1-10-13 \
         fotomotoclick-bocca-serriola-07-09-2025-18-10-14 \
         fotomotoclick-bocca-serriola-07-09-2025-292-10-47 \
         fotomotoclick-bocca-serriola-09-08-2025-567-11-35; do
  printf "%-52s " "${s#fotomotoclick-bocca-serriola-}"
  curl -sS -A "$UA" -o /dev/null -w "code=%{http_code} bytes=%{size_download}\n" \
       --max-time 30 "https://fotomoto.click/gallerie/$s/"
done
```

Atteso:

```
07-09-2025-1-10-13     code=301 bytes=0
07-09-2025-18-10-14    code=301 bytes=0
07-09-2025-292-10-47   code=410 bytes=9        <- era 404 / 139.937 byte
09-08-2025-567-11-35   code=410 bytes=9        <- era 404 / 139.937 byte
```

**Se una delle prime due righe non è più 301, disinstallare subito** (cancellare il file):
significa che l'hook sta intercettando anche il ramo buono.

Controllo che il resto del sito non sia toccato:

```bash
curl -sS -o /dev/null -w "home=%{http_code}\n" https://fotomoto.click/
curl -sS -o /dev/null -w "sessione=%{http_code}\n" https://fotomoto.click/foto/bocca-serriola/07-09-2025/
curl -sS -o /dev/null -w "404 generico=%{http_code}\n" https://fotomoto.click/pagina-che-non-esiste-xyz/
```

Il 404 generico deve restare **404**: l'hook agisce solo su `/gallerie/`.

## Dopo

In GSC → Indicizzazione → Pagine → "Non trovata (404)" → **Convalida correzione**. La convalida
precedente è fallita il 01/07 perché il problema non era stato toccato; ora c'è un segnale
esplicito da far verificare.

## ⚠️ Dipendenza da conoscere: la radice `/gallerie/`

L'hook di questo mu-plugin agisce su `template_redirect` a **priorità 0**, quindi arriva prima
del modulo Redirections di Rank Math. Per la **radice** `/gallerie/` — che Rank Math
redirigeva verso `/gallerie-foto/` — questo significa che il 410 vince e il redirect non
scatta mai. È una regressione introdotta il 27-07-2026 e non rilevata subito, perché la
verifica post-deploy aveva controllato i prodotti, la home, la sessione e il 404 generico,
**ma non la radice del namespace**.

Risolta il 27-07-2026 a livello di server, che agisce prima di WordPress — e che risolve
insieme anche la catena a due hop:

```apache
# in .htaccess, nel blocco "Redirect vecchia pagina gallerie", prima di # BEGIN WordPress
RewriteRule ^gallerie/?$ /passi-e-valichi/ [R=301,L]
```

`^gallerie/?$` è ancorato: matcha **solo** la radice. Senza `$` finale distruggerebbe sia i
301 verso i prodotti vivi sia i 410 di questo mu-plugin.

**Se un giorno quella riga viene rimossa dall'`.htaccess`, la radice `/gallerie/` torna a
rispondere 410.** Le due cose vanno lette insieme.

> Nota operativa: dopo la modifica dell'`.htaccess`, LiteSpeed può impiegare qualche istante a
> rileggerlo. Una prima verifica che mostra il vecchio comportamento non significa che la
> regola sia sbagliata — va ripetuta.

## Stato finale verificato (27-07-2026)

| URL | Risposta |
|---|---|
| `/gallerie/` · `/gallerie-foto/` | 301 → `/passi-e-valichi/` (**un solo hop**) |
| `/gallerie/{slug}/` con prodotto esistente | 301 → `/foto/{loc}/{slug}/` |
| `/gallerie/{slug}/` senza prodotto | 410, 9 byte |
| 404 generico | 404 (l'hook agisce solo su `/gallerie/`) |

## Non fare

Non rimuovere il redirect `/gallerie/` → prodotto: serve ai 301 che funzionano e alle foto
ancora vive. Il namespace va lasciato in piedi finché Google smette di chiedere quegli URL.
