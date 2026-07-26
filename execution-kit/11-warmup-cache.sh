#!/usr/bin/env bash
# warmup-cache.sh — pre-scalda LiteSpeed/Cloudflare dopo la pubblicazione di una sessione.
# Rif. master audit §4.3. Da chiamare in coda a `generate-json` nella pipeline del Processor.
#
# Problema che risolve: la landing sessione ha TTFB ~2,1 s in cache MISS, ed OGNI sessione
# nuova e' per definizione MISS proprio quando Googlebot arriva. Loop negativo tra lentezza
# e mancata indicizzazione. Questo script fa in modo che la prima richiesta reale (Googlebot)
# trovi la pagina gia' calda.
#
# Uso:
#   ./warmup-cache.sh bocca-serriola 19-07-2026
#   ./warmup-cache.sh bocca-serriola 19-07-2026 5   # scalda anche le prime 5 pagine paginate
#
# Requisiti: curl. Idempotente. Non modifica nulla lato server, fa solo GET.

set -euo pipefail

BASE="https://fotomoto.click"
UA="FMC-Warmup/1.0 (+cache warmup)"
LOC="${1:?uso: warmup-cache.sh <localita> <data> [n_pagine]}"
DATE="${2:?uso: warmup-cache.sh <localita> <data> [n_pagine]}"
PAGES="${3:-0}"

warm () {
  local url="$1"
  # due passate: la 1a popola LiteSpeed (MISS→HIT), la 2a verifica.
  local t1 s1 t2 s2
  read -r t1 s1 < <(curl -s -o /dev/null -A "$UA" -w '%{time_total} %{http_code}' "$url"; echo)
  read -r t2 s2 < <(curl -s -o /dev/null -A "$UA" -w '%{time_total} %{http_code}' "$url"; echo)
  printf '  %-70s  pass1=%ss(%s)  pass2=%ss(%s)\n' "$url" "$t1" "$s1" "$t2" "$s2"
}

echo "== Warmup $LOC / $DATE =="
warm "$BASE/"
warm "$BASE/passi-e-valichi/"
warm "$BASE/foto/$LOC/"
warm "$BASE/foto/$LOC/$DATE/"

if [ "$PAGES" -gt 0 ]; then
  for p in $(seq 2 "$((PAGES+1))"); do
    warm "$BASE/foto/$LOC/$DATE/page/$p/"
  done
fi

# Opzionale: purge mirato Cloudflare della landing sessione, cosi' l'edge la ri-cachea calda.
# Richiede CF_ZONE e CF_TOKEN come variabili d'ambiente (token con permesso Cache Purge).
if [ -n "${CF_ZONE:-}" ] && [ -n "${CF_TOKEN:-}" ]; then
  echo "== Cloudflare purge mirato =="
  curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$CF_ZONE/purge_cache" \
    -H "Authorization: Bearer $CF_TOKEN" -H "Content-Type: application/json" \
    --data "{\"files\":[\"$BASE/foto/$LOC/$DATE/\",\"$BASE/foto/$LOC/\"]}" \
    | grep -o '"success":[a-z]*' || true
  echo
  # dopo il purge, ri-scalda una volta
  warm "$BASE/foto/$LOC/$DATE/"
fi

echo "== Fatto. Obiettivo: pass2 con x-litespeed-cache: hit e TTFB < 0,2 s prima che arrivi Googlebot. =="
