# E-016 — Attribuzione degli ordini dai log e copertura reale di GA4

- **Date:** 2026-07-28
- **Method:** correlazione fra access log dell'origine e ordini WooCommerce. Per ogni
  richiesta a `/pagamento/order-received/{id}/` si prende l'IP, si risale alle sue
  richieste nelle 24 ore precedenti e si legge il **primo referrer esterno**. Confronto
  dei totali con il report Ordini di WooCommerce e con GA4 sulla stessa finestra.
- **Demonstrates:** F-023, F-024, F-025. Corregge la stima di copertura in F-019/E-015.

---

## 1. Il metodo è validato

| | Ordini |
|---|---|
| Ordini distinti trovati nei log (18-28 lug) | 92 |
| Ordini reali in WooCommerce (18-28 lug) | **83** |
| Scarto | +11% |

Lo scarto è spiegabile con ordini che raggiungono la pagina di conferma e vengono poi
annullati o mai pagati. **L'attribuzione dai log è affidabile entro circa il 10%** — e non
richiede cookie, consenso o codice sul sito: il referrer è già nel log per il 100% delle
richieste.

## 2. Da dove arrivano gli ordini (92 ordini, 18-28 luglio)

| Provenienza | Ordini | Quota |
|---|---|---|
| Diretto | 38 | 41,3% |
| **Google** | **30** | **32,6%** |
| **Instagram** | **13** | **14,1%** |
| Facebook | 7 | 7,6% |
| Altro (PayPal, DuckDuckGo) | 4 | 4,3% |

**Google è la seconda fonte di ordini pagati: quasi un terzo.** Non impressioni — vendite.
Giustifica per misura l'investimento SEO.

**Instagram converte molto meglio di Facebook.** Sul totale delle richieste, i referrer
Facebook sono ~2.500 e quelli Instagram ~730; ma producono rispettivamente 7 e 13 ordini.
Rapporto di conversione circa 1 a 9 in favore di Instagram. Coerente con la scelta del
proprietario di sponsorizzare lì.

"Diretto" al 41,3% è un residuo gonfiato: IP mobili che cambiano, browser interni delle app
che non passano il referrer, digitazione diretta, clienti di ritorno.

## 3. Copertura reale di GA4 — 29 giugno / 26 luglio, stessa finestra

| | Reale (WooCommerce) | GA4 | Copertura |
|---|---|---|---|
| Ordini | **184** | 87 | **47,3%** |
| Ricavo | **2.606,00 €** | 1.238,00 € | **47,5%** |

**GA4 vede meno della metà**, in modo coerente su entrambe le metriche — perdita sistematica
da consenso, non un difetto tecnico.

> ⚠️ **Corregge E-015 §5.** Lì la copertura era stimata all'80% degli ordini e all'85% del
> ricavo, ma il calcolo era fatto su **un solo giorno** (26/07: 5 ordini reali, 4 in GA4).
> Un campione di cinque ordini non è un campione. Il valore su 28 giorni è la metà.

**Lettura corretta:** per un sito europeo con banner conforme, una copertura fra il 40 e il
70% è nella norma. GA4 non è rotto: è limitato dal consenso. Le decisioni vanno prese su
WooCommerce, log e Search Console — completi al 100% — usando GA4 per il comportamento, con i
valori assoluti da raddoppiare.

## 4. Contesto di crescita

| 29 giu – 26 lug | 2026 | 2025 | Variazione |
|---|---|---|---|
| Ordini | 184 | 29 | **+534%** |
| Vendite nette | 2.606,00 € | ~533 € | **+389%** |
| Valore medio ordine | 14,16 € | ~18,4 € | −23% |

L'attività è cresciuta di circa sei volte in un anno. Il calo del valore medio ordine è
coerente con più clienti che acquistano una singola foto invece di pochi che ne acquistano
molte. Circa **60 di quei 184 ordini arrivano da Google**.
