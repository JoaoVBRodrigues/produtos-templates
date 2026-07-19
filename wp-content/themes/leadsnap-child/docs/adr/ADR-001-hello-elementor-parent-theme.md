# ADR-001: Hello Elementor como Tema Pai

**Status:** Accepted  
**Date:** 2026-07-19  
**Deciders:** João Rodrigues

---

## Context

O LeadSnap precisa de um tema pai que:
- Não conflite com o Elementor page builder
- Seja aceito em marketplaces (ThemeForest, Possible With Elementor)
- Tenha footprint mínimo de CSS/JS (landing page = performance crítica)
- Seja mantido ativamente e compatível com WordPress 7.x

## Decision

Usar **Hello Elementor** como tema pai do child theme LeadSnap.

## Rationale

1. **É o tema oficial da Elementor** — recomendado explicitamente para Template Kits comercializáveis
2. **Footprint mínimo** — cerca de 5KB de CSS base, sem scripts desnecessários
3. **Zero conflito** — desenhado especificamente para não interferir no builder
4. **Aceito em todos os marketplaces Elementor** — ThemeForest, Possible With Elementor, TemplateMonster
5. **GPL v2** — compatível com nossa licença

## Alternatives Considered

| Alternativa | Razão para rejeição |
|-------------|---------------------|
| Astra | 30KB+ de CSS base; funcionalidades que interferem com Elementor |
| TwentyTwentyfive | Block-first design; conflito com Elementor em contextos de FSE |
| OceanWP | Dependência de plugins proprietários da marca |
| Tema customizado do zero | Risco de incompatibilidade; mais trabalho de manutenção |

## Consequences

✅ Positivo: Template aprovável em marketplaces sem modificação de estrutura  
✅ Positivo: Updates do Hello Elementor não quebram nosso child theme  
⚠️ Negativo: Dependência de Hello Elementor instalado (documentado nos requisitos)
