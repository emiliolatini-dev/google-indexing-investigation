# IPTC nel file immagine → badge "Licenziabile" in Google Images  (roadmap azione 22, P0)

Google riconosce i diritti/licenza di un'immagine da DUE fonti coerenti: lo schema
`ImageObject` (vedi `schema/image-licensable.jsonld.html`) **e** i metadati **IPTC** scritti
dentro il file. Scrivere gli IPTC nella pipeline di export e' un intervento in **un unico punto**
del Processor e vale per tutte le foto future.

## Campi IPTC che contano
| Campo IPTC | Serve a | Valore |
|---|---|---|
| `Creator` | attribuzione / E-E-A-T | `Emilio Latini` |
| `Copyright Notice` | proprieta' | `© {anno} Emilio Latini - FotoMoto.Click` |
| `Credit Line` | credito visibile | `FotoMoto.Click` |
| `Web Statement of Rights` | pagina licenza (attiva il badge) | `https://fotomoto.click/licenza-immagini/` |
| `Licensor URL` (Licensor) | link "Ottieni immagine" | URL pagina prodotto della foto |
| `Description` | testo/alt della foto | `Moto a {passo} - {data} ore {ora}` |

## Comando exiftool (una foto)
```bash
exiftool \
  -IPTC:By-line="Emilio Latini" \
  -IPTC:CopyrightNotice="© 2026 Emilio Latini - FotoMoto.Click" \
  -IPTC:Credit="FotoMoto.Click" \
  -IPTC:Caption-Abstract="Moto a Bocca Serriola - 19-07-2026 ore 10:05" \
  -XMP-xmpRights:WebStatement="https://fotomoto.click/licenza-immagini/" \
  -XMP-plus:LicensorURL="https://fotomoto.click/foto/bocca-serriola/fotomotoclick-bocca-serriola-19-07-2026-284-10-05/" \
  -XMP-dc:Creator="Emilio Latini" \
  -XMP-dc:Rights="© 2026 Emilio Latini - FotoMoto.Click" \
  -overwrite_original "foto.webp"
```

## In pipeline (batch, dentro generate-json o export)
Per ogni foto, i valori {passo, data, ora, numero, url_prodotto} li hai gia'. Pseudocodice:
```bash
for f in "$SESSION_DIR"/*.webp; do
  meta=$(map_file_to_meta "$f")   # -> passo, data, ora, slug prodotto
  exiftool \
    -IPTC:By-line="Emilio Latini" \
    -IPTC:CopyrightNotice="© ${YEAR} Emilio Latini - FotoMoto.Click" \
    -IPTC:Credit="FotoMoto.Click" \
    -IPTC:Caption-Abstract="Moto a ${PASSO} - ${DATA} ore ${ORA}" \
    -XMP-xmpRights:WebStatement="https://fotomoto.click/licenza-immagini/" \
    -XMP-plus:LicensorURL="https://fotomoto.click/foto/${PASSO_SLUG}/${SLUG}/" \
    -XMP-dc:Creator="Emilio Latini" \
    -overwrite_original "$f"
done
```

## Prerequisiti e verifica
1. Creare la pagina `/licenza-immagini/` (termini) a cui puntano WebStatement e schema `license`.
2. exiftool preserva i metadati anche dopo la conversione WebP? Verificare che l'ottimizzatore
   immagini (LiteSpeed/plugin) **non strippi** gli IPTC/XMP in fase di ricompressione — spesso lo fa.
   Se li strippa: scrivere gli IPTC DOPO l'ottimizzazione, oppure disattivare lo strip metadati.
   ```bash
   exiftool -Creator -Copyright -LicensorURL -WebStatement https://…/foto.webp   # o sul file locale
   ```
3. Google Rich Results Test sull'URL prodotto → deve rilevare ImageObject con license/acquireLicensePage.
4. Attendere il re-crawl e controllare GSC → Rendimento → Google Immagini (oggi: 29 clic/3 mesi).
