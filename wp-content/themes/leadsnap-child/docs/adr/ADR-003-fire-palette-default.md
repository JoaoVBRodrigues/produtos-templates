# ADR-003: Paleta Fire como Padrão (Deep Navy + Fire Orange)

**Status:** Accepted  
**Date:** 2026-07-19  
**Deciders:** João Rodrigues

---

## Context

A paleta padrão de um template de captura de leads tem impacto direto nas conversões.
A escolha precisa balancear:
- Psicologia de cores para urgência e ação imediata
- Diferenciação visual em marketplaces
- Versatilidade para reskin em outros nichos

## Decision

**Paleta Fire** como padrão:
- Background: `#0B1120` (Deep Navy)
- Accent/CTA: `#FF5722` (Fire Orange)  
- Highlight/Badge: `#FFC107` (Electric Amber)

## Rationale baseado em pesquisa de conversão (2025)

| Cor | Psicologia | Aplicação no LeadSnap |
|-----|-----------|----------------------|
| Deep Navy `#0B1120` | Confiança, premium, reduz ansiedade | Background — cria sensação de exclusividade |
| Fire Orange `#FF5722` | Urgência, energia, ação imediata | CTA principal — maximiza cliques |
| Electric Amber `#FFC107` | Atenção, otimismo, destaque | Badges e countdown — chama atenção sem agressividade |

**Por que não usar vermelho puro para CTA?**
Vermelho puro (`#FF0000`) tem conotação negativa em alguns contextos (erro, perigo).
Fire Orange mantém a urgência sem o risco de percepção negativa.

**Por que fundo escuro?**
- Contraste alto = legibilidade superior
- Sensação premium e exclusividade
- Diferenciação em marketplaces (maioria dos templates usa fundo claro)
- Foco no CTA (o laranja "explode" em fundo escuro)

## Paletas Adicionais

| Paleta | Classe CSS | Uso ideal |
|--------|-----------|-----------|
| Neon Green | `.ls-palette-neon` | Cashback, finanças, saúde |
| Alto Contraste | `.ls-palette-contrast` | WCAG AAA, mercados amplos |

## Consequences

✅ Combinação de alta conversão validada por pesquisa  
✅ Diferenciação visual em marketplaces (tema escuro premium)  
✅ 3 paletas inclusas permitem demonstrar flexibilidade  
⚠️ Tema escuro pode não ser adequado para todos os nichos (ex.: infantil, bem-estar suave)  
→ Documentar paleta clara como opção #3 para esses casos
