# ADR-004: Countdown Evergreen + Data Fixa (Dual Mode)

**Status:** Accepted  
**Date:** 2026-07-19  
**Deciders:** João Rodrigues

---

## Context

Templates de captura de leads frequentemente usam urgência artificial via countdown.
Dois padrões de mercado existem:
1. **Data fixa**: expira em uma data real (promoção com prazo)
2. **Evergreen**: cada visitante vê um timer pessoal que reinicia (cookie-based)

## Decision

Implementar **ambos os modos** no mesmo widget, controlável via atributo shortcode.

```
[leadsnap_countdown target="2026-12-31T23:59:59"]  ← Modo data fixa
[leadsnap_countdown evergreen="1" days="3"]         ← Modo evergreen
```

## Rationale

1. **Flexibilidade**: nichos diferentes precisam de estratégias diferentes
   - Grupos de ofertas com prazo real → modo data fixa
   - Infoprodutos e capturas permanentes → modo evergreen

2. **Mercado**: marketplaces valorizam templates com mais casos de uso cobertos

3. **Implementação**: ambos os modos compartilham o mesmo JS (`countdown.js`),
   diferenciados apenas pela presença de `data-target` vs `data-evergreen`

4. **Cookie storage**: evergreen usa `localStorage` como fallback + `document.cookie`
   com `SameSite=Lax` (compatível com LGPD — não é cookie de rastreamento)

## Cookie Strategy

```
Cookie name:  leadsnap_cdown_{base64(element_id)}
Expires:      N days (same as countdown duration)
Value:        Unix timestamp (ms) — target end time
SameSite:     Lax
Secure:       Not forced (HTTP local dev) — should be HTTPS in production
```

## Consequences

✅ Cobre 100% dos casos de uso de countdown em landing pages  
✅ Zero dependência externa — puro vanilla JS  
✅ Persistência via cookie — mesmo após F5 o timer continua de onde parou  
✅ Acessível — aria-live atualiza a cada minuto (não a cada segundo)  
⚠️ Cookies podem ser bloqueados (modo privado agressivo) → fallback: 48h fixas  
⚠️ Evergreen pode ser percebido como manipulação (documentar uso ético)
