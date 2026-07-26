# Fix paginazione infinita  (roadmap azione 9)

## Problema misurato (26-07-2026)
```
/foto/bocca-serriola/19-07-2026/          200
/foto/bocca-serriola/19-07-2026/page/2/   200  (stessi 18 prodotti di page/1, noindex, NESSUN canonical)
/foto/bocca-serriola/19-07-2026/page/99/  200  (idem)
```
Tre problemi: spazio URL infinito crawlabile (54 sessioni × ∞ pagine), paginazione rotta
(page/N = page/1), nessun canonical su quelle pagine. Googlebot spreca crawl budget in una
trappola infinita — proprio mentre le sessioni nuove faticano a indicizzarsi.

## Fix interim immediato (1 riga concettuale) — finche' non c'e' paginazione server-side reale
Poiche' `/page/N/` con N>1 mostra contenuto IDENTICO a page/1, la scelta corretta e' il
**canonical a page/1** (non robots-disallow: vogliamo che Google legga il canonical).

### Opzione consigliata: canonical self→page1 sulle pagine paginate
Nel template della landing sessione, quando `is_paged()`:
```php
// functions.php / mu-plugin — solo sulle landing sessione paginate
add_action('wp_head', function () {
    if ( is_tax() && is_paged() ) { // adatta la condizione alla tua tassonomia sessione
        global $wp;
        $base = home_url( preg_replace('#/page/\d+/?$#', '/', $wp->request) . '/' );
        echo '<link rel="canonical" href="' . esc_url($base) . "\">\n";
        echo "<meta name=\"robots\" content=\"noindex,follow\">\n";
    }
}, 1);
```

### Alternativa piu' netta: 404 su page/N (N>1) finche' la paginazione non e' reale
Se preferisci eliminare del tutto lo spazio URL, in `.htaccess` (LiteSpeed rispetta le regole
Apache) — restituisci 410/404 alle pagine paginate delle sessioni:
```apache
# Blocca /foto/<loc>/<data>/page/N/ con N>=2 finche' la paginazione non e' server-side reale
RewriteEngine On
RewriteCond %{REQUEST_URI} ^/foto/[^/]+/[0-9]{2}-[0-9]{2}-[0-9]{4}/page/([2-9]|[0-9]{2,})/?$ [NC]
RewriteRule ^ - [R=410,L]
```
(410 "Gone" e' preferibile a 404: dice a Google "rimuovi, non ri-tentare".)

## Fix definitivo (roadmap azione 23)
Paginazione server-side reale con `<a href>` testuali, 60 foto/pagina, indicizzabile solo
page/1, + selettore per fascia oraria (`/15-16/`). A quel punto: rimuovere il canonical→page1
e permettere l'indicizzazione di page/1, mantenendo `rel=next/prev` non piu' necessari ma i
link testuali crawlabili. Vedi `schema/session-landing.jsonld.html` per l'ItemList.

## Verifica
```bash
curl -s -o /dev/null -w '%{http_code}\n' -A Googlebot https://fotomoto.click/foto/bocca-serriola/19-07-2026/page/2/
# atteso dopo il fix: 410 (opzione .htaccess) oppure 200 con <link rel=canonical> a page/1
```
