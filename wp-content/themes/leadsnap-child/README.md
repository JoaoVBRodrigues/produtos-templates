# LeadSnap — High-Conversion Lead Capture Template

**Version:** 1.0.0 | **License:** GPL-2.0-or-later | **Requires:** WordPress 6.5+, PHP 7.4+

> Template de página de captura de leads construído em WordPress + Elementor Free.
> Pronto para ser customizado em minutos para qualquer nicho.

---

## 📦 O que está incluído

```
leadsnap-child/
├── style.css              → Design system completo (3 paletas)
├── functions.php          → Funcionalidades core + integrações
├── index.php              → Arquivo de segurança
├── screenshot.png         → Preview para marketplace (1200×900)
├── assets/
│   ├── css/
│   │   └── (gerado pelo style.css do tema — não há arquivo separado)
│   └── js/
│       ├── countdown.js   → Contador regressivo (evergreen + data fixa)
│       └── main.js        → Animações e interações
├── demo-content/
│   └── leadsnap-demo.xml  → Importação de conteúdo demo
├── languages/
│   └── leadsnap-child.pot → Arquivo para traduções
└── docs/
    ├── installation-guide.md
    ├── customization-guide.md
    └── adr/
```

---

## 🚀 Instalação (5 passos)

### Pré-requisitos
- WordPress 6.5+ instalado
- [Hello Elementor](https://wordpress.org/themes/hello-elementor/) ativado como tema pai
- [Elementor](https://wordpress.org/plugins/elementor/) (Free) instalado e ativado
- PHP 7.4+

### Passo 1 — Upload do child theme
1. Faça o upload da pasta `leadsnap-child/` para `wp-content/themes/`
2. No WP Admin: **Aparência → Temas → LeadSnap → Ativar**

### Passo 2 — Importar conteúdo demo
1. Instale o plugin [WordPress Importer](https://wordpress.org/plugins/wordpress-importer/)
2. **Ferramentas → Importar → WordPress**
3. Selecione `demo-content/leadsnap-demo.xml`
4. Marque "Download and import file attachments"
5. Clique em "Submit"

### Passo 3 — Instalar plugins recomendados (todos gratuitos)
| Plugin | Finalidade | Obrigatório? |
|--------|-----------|--------------|
| [Elementor](https://wordpress.org/plugins/elementor/) | Page builder | ✅ Sim |
| [Rank Math SEO](https://wordpress.org/plugins/seo-by-rank-math/) | SEO e Open Graph | Recomendado |
| [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/) | Performance | Recomendado |

### Passo 4 — Configurar a página inicial
1. **Configurações → Leitura → Sua página inicial exibe → Uma página estática**
2. Selecione "LeadSnap Demo" como Página Inicial
3. Salve

### Passo 5 — Verificar
Acesse o site — a landing page deve estar visível com todo o conteúdo demo.

---

## 🎨 Trocar a paleta de cores

### Via Elementor (recomendado)
1. Elementor → **Site Settings → Global Colors**
2. Altere as cores mapeadas às variáveis CSS do LeadSnap
3. Salvar → Preview → Pronto ✅

### Via CSS (para desenvolvedores)
Edite as variáveis em `style.css` (seção `:root {}`):

```css
/* Troque apenas estes 3 valores para reskin completo */
--ls-bg-primary:  #0B1120;   /* Fundo principal */
--ls-accent:      #FF5722;   /* CTA e destaques */
--ls-highlight:   #FFC107;   /* Badges e urgência */
```

### Paletas prontas
| Paleta | Classe no `<body>` | Uso ideal |
|--------|-------------------|-----------|
| Fire (padrão) | *(nenhuma)* | E-commerce, grupos de ofertas |
| Verde Neon | `ls-palette-neon` | Cashback, saúde, finanças |
| Alto Contraste | `ls-palette-contrast` | Acessibilidade, mercados amplos |

---

## ⏱️ Configurar o Countdown

### Modo data fixa (Elementor HTML Widget)
```html
[leadsnap_countdown target="2026-12-31T23:59:59"]
```

### Modo Evergreen (reinicia por visitante via cookie)
```html
[leadsnap_countdown evergreen="1" days="3"]
```
Cada visitante terá 3 dias a partir da primeira visita. O cookie é renovado automaticamente.

---

## 📋 Configurar o Formulário

### Conexão via Webhook (Zapier, Make, n8n)
1. No Elementor: edite o widget **Form**
2. **Actions After Submit → Webhook**
3. Cole a URL do seu webhook
4. O payload JSON enviado contém: `name`, `email`, `phone`, `timestamp`, `page_url`

### Integração ActiveCampaign
Adicione ao `wp-config.php`:
```php
define('LEADSNAP_AC_API_URL',   'https://SUACONTA.api-us1.com');
define('LEADSNAP_AC_API_KEY',   'sua-api-key');
define('LEADSNAP_AC_LIST_ID',   '1'); // ID da sua lista
```
O endpoint de ativação é: `POST /wp-admin/admin-ajax.php?action=leadsnap_ac_subscribe`

### Integração Mailchimp
Adicione ao `wp-config.php`:
```php
define('LEADSNAP_MC_API_KEY',       'sua-api-key');
define('LEADSNAP_MC_LIST_ID',       'abc123de'); // Audience ID
define('LEADSNAP_MC_SERVER_PREFIX', 'us21');     // Prefixo do servidor
```
O endpoint de ativação é: `POST /wp-admin/admin-ajax.php?action=leadsnap_mc_subscribe`

---

## 📡 Ativar os Pixels de Rastreamento

### Meta Pixel
1. Abra `functions.php`
2. Localize a função `leadsnap_meta_pixel()`
3. Substitua `YOUR_PIXEL_ID_HERE` pelo seu ID real
4. Descomente o bloco `<script>...</script>`

### Google Analytics 4 (GA4)
1. Abra `functions.php`
2. Localize a função `leadsnap_ga4()`
3. Substitua `G-XXXXXXXXXX` pelo seu Measurement ID
4. Descomente o bloco `<script>...</script>`

---

## ⚡ Performance

Configurações recomendadas no LiteSpeed Cache:
- ✅ Page Cache: On
- ✅ Browser Cache: On
- ✅ CSS Minify: On
- ✅ JS Minify: On
- ✅ Image Lazy Load: On
- ✅ WebP Conversion: On

---

## 🌍 Tradução / i18n

1. Instale [Loco Translate](https://wordpress.org/plugins/loco-translate/)
2. **Loco Translate → Temas → LeadSnap**
3. Use o arquivo `leadsnap-child.pot` como base
4. Crie seu arquivo `.po` e `.mo`

Todas as strings do tema usam `__( 'string', 'leadsnap-child' )` e são ready-to-translate.

---

## 📜 Licença

- **Código PHP:** GNU GPL v2 or later — obrigatório para temas WordPress
- **CSS e JS:** Incluídos sob GPL v2 por simplicidade
- **Fontes:** Syne e DM Sans — [SIL Open Font License 1.1](https://scripts.sil.org/OFL) — Redistribuição permitida
- **Ícones:** Heroicons — [MIT License](https://github.com/tailwindlabs/heroicons/blob/master/LICENSE) — Redistribuição permitida
- **Imagens demo:** Geradas por IA com licença comercial livre — Redistribuição permitida

---

## 🆘 Suporte

- GitHub: [JoaoVBRodrigues/leadsnap-child](https://github.com/JoaoVBRodrigues)
- Documentação completa: `/docs/`

---

*LeadSnap © 2026 João Rodrigues — Built with ♥ and data-driven design*
