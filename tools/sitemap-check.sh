#!/usr/bin/env bash
# sitemap-check.sh  v0.5
# Uso: ./sitemap-check.sh "bocca-serriola/21-06-2026"
#
# Sanity check post-import: product sitemaps + landing URL.
# Script read-only. Non modifica nulla sul server.
#
# Exit code: 0 = tutti i check PASS, 1 = almeno un FAIL.

set -euo pipefail

BASE_URL="https://fotomoto.click"
SESSION_PATH="${1:-}"

if [[ -z "$SESSION_PATH" ]]; then
  echo "Uso: $0 <session-path>  es. bocca-serriola/21-06-2026"
  exit 1
fi

LANDING_PATH="$SESSION_PATH"
PRODUCT_PATTERN="${SESSION_PATH/\//-}"
LANDING_URL="${BASE_URL}/foto/${LANDING_PATH}/"
EXPECTED_CANONICAL="$LANDING_URL"

PASS=0
FAIL=0

check() {
  local label="$1"
  local result="$2"
  if [[ "$result" == "ok" ]]; then
    echo "  [PASS] $label"
    PASS=$((PASS + 1))
  else
    echo "  [FAIL] $label — $result"
    FAIL=$((FAIL + 1))
  fi
}

warn() {
  echo "  [WARN] $1"
}

echo ""
echo "=== SITEMAP SANITY CHECK v0.4 ==="
echo "Sessione       : $SESSION_PATH"
echo "Landing path   : $LANDING_PATH"
echo "Product pattern: $PRODUCT_PATTERN"
echo "Data           : $(date -u +%Y-%m-%dT%H:%M:%SZ)"
echo ""

# -------------------------------------------------------
# FASE 2 — sitemap_index.xml
# -------------------------------------------------------
echo "-- sitemap_index.xml"

IDX_HEADERS=$(curl -sI "${BASE_URL}/sitemap_index.xml")
IDX_STATUS=$(echo "$IDX_HEADERS" | grep -i "^HTTP" | tail -1 | awk '{print $2}')
IDX_CACHE=$(echo "$IDX_HEADERS" | grep -i "^cf-cache-status:" | tr -d '\r' | awk '{print $2}')
IDX_BODY=$(curl -s "${BASE_URL}/sitemap_index.xml")
IDX_COUNT=$(echo "$IDX_BODY" | grep -c "<loc>" || true)

[[ "$IDX_STATUS" == "200" ]] \
  && check "sitemap_index HTTP 200" "ok" \
  || check "sitemap_index HTTP 200" "got $IDX_STATUS"

if [[ "$IDX_CACHE" == "HIT" ]]; then
  warn "CF-Cache-Status: HIT — sitemap potenzialmente cached da Cloudflare"
else
  check "CF-Cache-Status non HIT (got: ${IDX_CACHE:-n/a})" "ok"
fi

[[ "$IDX_COUNT" -gt 0 ]] \
  && check "sitemap_index contiene $IDX_COUNT loc entries" "ok" \
  || check "sitemap_index loc entries" "0 entries — index vuoto"

echo ""

# -------------------------------------------------------
# FASE 3 — Ricerca URL prodotto sessione in product-sitemap*.xml
# (solo URL prodotto WooCommerce, non la landing)
# -------------------------------------------------------
echo "-- Ricerca URL prodotto sessione in product-sitemap*.xml"

SITEMAP_URLS=$(echo "$IDX_BODY" \
  | sed -n 's|.*<loc>\(https://[^<]*product-sitemap[0-9]*\.xml\)</loc>.*|\1|p')

FOUND_IN=""
for SM_URL in $SITEMAP_URLS; do
  SM_BODY=$(curl -s "$SM_URL")
  if echo "$SM_BODY" | grep -q "$PRODUCT_PATTERN"; then
    FOUND_IN="$SM_URL"
    break
  fi
done

if [[ -n "$FOUND_IN" ]]; then
  check "URL prodotto sessione trovati in sitemap" "ok"
  echo "         → $FOUND_IN"
else
  check "URL prodotto sessione trovati in sitemap" \
    "NON trovati in nessun product-sitemap"
fi

echo ""

# -------------------------------------------------------
# FASE 4 — URL landing sessione (HTTP, separata dai product-sitemap)
# -------------------------------------------------------
echo "-- URL landing sessione (verifica HTTP)"

LANDING_HEADERS=$(curl -sI "$LANDING_URL")
LANDING_STATUS=$(echo "$LANDING_HEADERS" | grep -i "^HTTP" | tail -1 | awk '{print $2}')
LANDING_BODY=$(curl -s "$LANDING_URL")

NOINDEX_HEAD=$(echo "$LANDING_BODY" | grep -i "noindex" || true)
X_ROBOTS=$(echo "$LANDING_HEADERS" | grep -i "x-robots-tag" | grep -i "noindex" || true)
CANONICAL=$(echo "$LANDING_BODY" \
  | sed -n 's:.*<link rel="canonical" href="\([^"]*\)".*:\1:p' | head -1)

[[ "$LANDING_STATUS" == "200" ]] \
  && check "Landing HTTP 200" "ok" \
  || check "Landing HTTP 200" "got $LANDING_STATUS"

[[ -z "$NOINDEX_HEAD" ]] \
  && check "Nessun noindex nel <head>" "ok" \
  || check "Nessun noindex nel <head>" "trovato tag noindex"

[[ -z "$X_ROBOTS" ]] \
  && check "Nessun X-Robots-Tag: noindex" "ok" \
  || check "Nessun X-Robots-Tag: noindex" "header noindex presente"

if [[ "$CANONICAL" == "$EXPECTED_CANONICAL" ]]; then
  check "Canonical corrisponde all'URL attesa" "ok"
elif [[ -z "$CANONICAL" ]]; then
  check "Canonical corrisponde all'URL attesa" "tag non rilevato o non parsabile"
else
  check "Canonical corrisponde all'URL attesa" \
    "attesa: $EXPECTED_CANONICAL — trovata: $CANONICAL"
fi

echo ""

# -------------------------------------------------------
# RIEPILOGO
# -------------------------------------------------------
if [[ "$FAIL" -eq 0 ]]; then
  STATUS="PASS"
else
  STATUS="FAIL"
fi

echo "=== RISULTATO: $PASS PASS · $FAIL FAIL · STATUS=$STATUS ==="
echo ""

[[ "$STATUS" == "PASS" ]] && exit 0 || exit 1
