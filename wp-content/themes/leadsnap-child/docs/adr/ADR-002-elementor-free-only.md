# ADR-002: Elementor Free — Zero Dependência de Plugins Pagos

**Status:** Accepted  
**Date:** 2026-07-19  
**Deciders:** João Rodrigues

---

## Context

O template precisa funcionar sem nenhum plugin pago para:
- Facilitar aprovação em marketplaces (ThemeForest rejeita templates com dependências pagas obrigatórias)
- Reduzir fricção de compra para o usuário final
- Manter o custo de entrada zero para quem comprar o template

## Decision

Todo o functionality core do LeadSnap usa exclusivamente **Elementor Free**.  
Elementor Pro é documentado apenas como "opcional para Header/Footer customizados".

Countdown e integrações são implementados em PHP/JS nativo no child theme.

## Rationale

1. **RNF03** do documento de produto: *"Zero dependência de plugins pagos obrigatórios"*
2. **Mercado**: compradores de marketplace evitam templates com licenças adicionais
3. **Countdown widget**: implementado em JS vanilla no `assets/js/countdown.js` — substitui o widget nativo Pro
4. **Formulários**: Elementor Forms Free cobre Nome, E-mail, WhatsApp + Webhook

## Consequences

✅ Positivo: Aprovação mais fácil em marketplaces  
✅ Positivo: Usuário não precisa de Elementor Pro (US$59/ano)  
✅ Positivo: Countdown personalizado é mais flexível que o widget Pro  
⚠️ Negativo: Header/Footer customizados requerem CSS manual (sem Theme Builder Pro)  
⚠️ Negativo: Sem Popup Builder do Pro para exit-intent (documentar como enhancement)

## Implementation Notes

- Countdown: `[leadsnap_countdown target="..."]` ou `[leadsnap_countdown evergreen="1" days="3"]`
- Formulário webhook: Elementor Forms → Actions → Webhook
- Header/Footer: gerenciado pelo Hello Elementor + CSS custom
