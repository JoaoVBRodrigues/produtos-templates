# Template de Página de Captura — WordPress + Elementor
### Produto próprio genérico (para venda em marketplace e/ou portfólio)

> Esta é a versão **desvinculada de cliente** do projeto de página de captura. O objetivo aqui não é entregar para alguém, e sim construir um ativo seu: reutilizável em várias propostas de freela, vendável como template, e apresentável como case de portfólio.

---

## 1. Visão do produto

**O que é:** um template de landing page de captura de leads, construído em WordPress + Elementor, pronto para ser "reskinado" (trocar cores, textos e imagens) em minutos para qualquer nicho que use esse formato — grupos de ofertas, infoprodutos, afiliados, imobiliárias, eventos, etc.

**Por que vale a pena como produto:** você já validou a demanda real — apareceu como pedido de cliente pago (81 propostas concorrentes num único anúncio mostra que existe procura constante por esse tipo de página). Construir uma versão própria e genérica permite:
1. Reutilizar em **futuras propostas de freela** sem começar do zero (proposta mais rápida = mais competitiva).
2. Vender como **template pronto** em marketplaces.
3. Usar como **peça de portfólio** ("veja uma landing page de alta conversão que eu desenvolvi").

---

## 2. Escolha do nicho (decisão importante antes de começar)

Um template "genérico demais" compete mal em marketplaces — pesquisas de mercado mostram que sem nicho claro ou diferencial visual forte, o produto fica perdido em páginas de resultado distantes. Escolha **um** nicho de partida (dá para criar variações depois):

| Nicho | Público | Observação |
|-------|---------|------------|
| Grupos de ofertas/cupons | Afiliados, revendedores | Foi a demanda real que você já recebeu — comece por aqui |
| Infoprodutos/lançamentos | Produtores digitais | Alta demanda, mas mais concorrido |
| Imobiliárias (captura de lead de imóvel) | Corretores | Nicho B2B, ticket de venda do template pode ser maior |
| Eventos/webinars | Organizadores de evento | Precisa de campos de formulário diferentes (data, horário) |

> 💡 Recomendação: comece pelo nicho de **grupos de ofertas/cupons**, já que você tem experiência real recém-adquirida com ele. Depois de validado, duplique a estrutura trocando conteúdo/demo para os outros nichos — isso vira uma "linha de produtos" em vez de um único template.

---

## 3. Requisitos Funcionais (RF) do template

| ID | Requisito |
|----|-----------|
| RF01 | Página construída 100% em Elementor (compatível com a versão free, com Pro como opcional) |
| RF02 | Seções: hero, prova social (depoimentos/números), oferta/benefícios, formulário, FAQ, rodapé |
| RF03 | Formulário de captura com campos configuráveis (nome, e-mail e/ou WhatsApp) |
| RF04 | Integração pronta com pelo menos um serviço de automação (Webhook genérico, Zapier/Make, ou envio de e-mail via SMTP) — documentado para o comprador plugar o dele |
| RF05 | Página de "obrigado" com placeholder de redirecionamento configurável |
| RF06 | Widget de contador regressivo reutilizável (data configurável) |
| RF07 | Totalmente responsivo |
| RF08 | Meta tags de SEO e Open Graph configuráveis via campos do Elementor/plugin de SEO |
| RF09 | Placeholder de pixel de rastreamento (Meta Pixel / GA4) fácil de trocar o ID |
| RF10 | Checkbox de consentimento (LGPD/GDPR) genérico, com texto editável |
| RF11 | **Conteúdo 100% demo/placeholder** — nenhuma referência a cliente real, marca real ou dado real |
| RF12 | Kit de estilo global (Elementor) com 2-3 variações de paleta prontas (ex.: escura, clara, alto contraste) para demonstrar flexibilidade na vitrine do marketplace |

## 4. Requisitos Não Funcionais (RNF)

| ID | Requisito |
|----|-----------|
| RNF01 | Performance: nota alta em PageSpeed Insights (é o primeiro filtro que revisores de marketplace e compradores aplicam) |
| RNF02 | Código limpo e comentado — você vai (re)ler isso meses depois ao adaptar para outro nicho |
| RNF03 | Zero dependência de plugins pagos obrigatórios (facilita aprovação em marketplace e reduz suporte) |
| RNF04 | Pronto para tradução (i18n) — mesmo que a venda inicial seja pt-BR, isso amplia mercado |
| RNF05 | Documentação de instalação e customização incluída no pacote |
| RNF06 | Licença compatível com GPL (ver seção 10) |

---

## 5. Stack técnica

- WordPress + Elementor (free como base; Elementor Pro apenas se o Template Kit exigir Header/Footer customizados)
- Tema base: **Hello Elementor** (leve, oficial, feito para não conflitar com o builder — é também o que a própria Elementor recomenda como base de templates comercializáveis)
- Plugins de apoio para a demo: SEO (Rank Math), cache (LiteSpeed/WP Rocket), formulário (Elementor Forms nativo)

---

## 6. Arquitetura e nome do produto

Antes de codar, defina um **nome de produto** (fica mais fácil de vender e de citar no portfólio do que "página de captura genérica"). Sugestões de naming a considerar: algo curto, em inglês (padrão de marketplace), remetendo a "captura/conversão/leads" — ex.: *LeadSnap*, *CaptaKit*, *GrabPage*, *OfferGate*. Escolha um e use-o consistentemente no child theme, no repositório e no material de venda.

```
[nome-do-produto]-child/
├── style.css              → cabeçalho do child theme (Theme Name: [Nome do Produto])
├── functions.php          → enqueue de assets + snippets reutilizáveis (pixel placeholder, countdown)
├── screenshot.png         → obrigatório para marketplace
├── demo-content/
│   └── [nome-do-produto]-demo.xml   → conteúdo de demonstração exportado (WordPress importer)
└── assets/
    └── custom.css
```

Mantenha o **conteúdo de demonstração** (textos, imagens placeholder, dados fictícios) separado do código — isso facilita gerar novas variações de nicho reaproveitando o mesmo child theme.

---

## 7. Boas práticas

- Nunca deixe dado real de cliente (nome, logo, número de WhatsApp real) em nenhuma versão pública/vendável — troque tudo por conteúdo fictício.
- Use imagens com licença CC0 ou compradas com licença de redistribuição (bancos como Unsplash/Pexels geralmente **não** permitem redistribuição do arquivo original em um template — leia os termos antes de empacotar).
- Versione desde o commit inicial em um repositório próprio (privado até decidir onde vender).
- Grave um vídeo curto de demonstração (30–60s) desde o início do projeto — maioria dos compradores de template decide pelo preview em vídeo antes de ler qualquer descrição.

### `.gitignore` sugerido
```gitignore
wp-config.php
wp-content/uploads/
wp-content/cache/
*.log
.htaccess
node_modules/
```

---

## 8. Roadmap de funcionalidades + Conventional Commits (em inglês)

| # | Funcionalidade | Commit sugerido |
|---|----------------|-------------------|
| 1 | Setup do child theme genérico | `feat: initialize generic lead capture child theme` |
| 2 | Kit de estilo global (paleta 1) | `feat: add default global style kit` |
| 3 | Seção hero | `feat: build hero section` |
| 4 | Seção de benefícios/oferta | `feat: add benefits section` |
| 5 | Prova social (depoimentos/números) | `feat: add social proof section` |
| 6 | Formulário de captura configurável | `feat: add configurable lead capture form` |
| 7 | Placeholder de integração via webhook | `feat: add generic webhook integration placeholder` |
| 8 | Página de obrigado configurável | `feat: add configurable thank you page` |
| 9 | Widget de countdown reutilizável | `feat: add reusable countdown widget` |
| 10 | Seção de FAQ | `feat: add faq section` |
| 11 | Meta tags SEO/OG configuráveis | `feat: add configurable seo and og meta tags` |
| 12 | Checkbox de consentimento genérico | `feat: add generic consent checkbox` |
| 13 | Segunda paleta de cores (variação escura) | `feat: add dark color palette variant` |
| 14 | Terceira paleta (alto contraste) | `feat: add high contrast palette variant` |
| 15 | Exportação do conteúdo demo (XML) | `chore: export demo content for import` |
| 16 | Otimização de performance | `perf: optimize assets and enable caching` |
| 17 | Documentação de instalação/uso | `docs: write installation and customization guide` |
| 18 | Gravação e edição do vídeo de preview | `docs: add product preview video` |
| 19 | Variação de nicho #2 (ex.: imobiliária) | `feat: add real estate niche content variant` |
| 20 | Submissão ao marketplace escolhido | `chore: prepare marketplace submission package` |

---

## 9. Prompts de "vibe coding"

**Gerar conteúdo demo neutro:**
```
Escreva textos de demonstração (headline, subheadline, 3 bullets de
benefício, texto de CTA) para uma landing page de captura de leads
do nicho [grupos de ofertas / infoprodutos / imobiliária], em
português, sem citar marca real, prontos para colar em widgets do
Elementor.
```

**Variação de paleta:**
```
No arquivo de Global Colors do Elementor (kit de estilo), gere duas
variações de paleta a partir da paleta base [cores atuais]: uma
versão escura/alto contraste e uma versão clara minimalista, mantendo
a mesma proporção de cor de destaque para os CTAs.
```

**Empacotamento para marketplace:**
```
Liste, em formato de checklist, tudo que preciso revisar em um child
theme do Hello Elementor antes de submeter como Template Kit em um
marketplace: nomenclatura de arquivos, presença de screenshot.png,
ausência de dados reais de terceiros, licenciamento de imagens,
e texto pronto para tradução.
```

---

## 10. Licenciamento (importante para venda)

- Temas/plugins que rodam sobre o WordPress (que é GPL) **precisam ser distribuídos sob licença GPL ou compatível** na parte de código PHP — é regra do próprio WordPress.org e exigência de marketplaces como o ThemeForest/Envato.
- CSS, imagens e fontes podem ter licença separada (split license), desde que deixe isso claro na documentação.
- Verifique a licença de qualquer imagem/ícone/fonte usada na demo antes de empacotar para venda — nem todo banco de imagens "gratuito" permite redistribuição dentro de um produto revendido.

---

## 11. Estratégia de venda

| Canal | Quando usar |
|-------|-------------|
| **ThemeForest (Envato)** | Maior audiência; hoje existem 340+ templates com suporte a Elementor ativos, preço médio de USD 24, comissão do autor a partir de 37,5% (sobe com volume). Revisão pode levar até 10 dias úteis. |
| **Possible With Elementor** | Marketplace oficial da Elementor — menos volume, mas público mais qualificado (quem procura ali já quer Elementor). |
| **TemplateMonster / ElementInvader** | Nichos menores, menos saturados que o ThemeForest. |
| **Venda direta (Gumroad/site próprio)** | Sem comissão de marketplace, mas depende de tráfego próprio — bom complemento se você já participa de comunidades de afiliados/revendedores. |

**Preço de referência (mercado Elementor, dado observado em 2026):** média de USD 24 por licença no ThemeForest, variando de USD 7 a USD 50 conforme complexidade/nicho.

---

## 12. Estratégia de portfólio

Independentemente de vender ou não, use este template como case:
1. Suba uma **demo ao vivo** (subdomínio grátis ou domínio barato) para linkar nas propostas de freela.
2. Escreva um case curto no portfólio: problema → solução → stack usada → resultado esperado (ex.: "página otimizada para conversão de leads via WhatsApp, carregando em menos de 2s").
3. Inclua 2-3 screenshots (desktop + mobile) e, se possível, o vídeo de preview já gravado para a venda.
4. No GitHub (JoaoVBRodrigues), deixe o repositório **privado** se pretende vender, ou público com licença clara se for só portfólio — não deixe público um produto que você ainda pretende comercializar.
