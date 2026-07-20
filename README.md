# LeadSnap — Landing Page de Alta Conversão para WordPress

[![WordPress Child Theme](https://img.shields.io/badge/WordPress%20Child%20Theme-Hello%20Elementor-blue.svg)](https://wordpress.org/themes/hello-elementor/)
[![Elementor Free Compatible](https://img.shields.io/badge/Elementor-Free-green.svg)](https://wordpress.org/plugins/elementor/)
[![LGPD Ready](https://img.shields.io/badge/LGPD-Conformidade-orange.svg)]()

Um template premium de landing page e página de captura de alta performance construído para o ecossistema **WordPress**. Este projeto demonstra o desenvolvimento de um **Tema Filho (Child Theme)** customizado e altamente otimizado para o **Hello Elementor**, utilizando exclusivamente a versão **gratuita** do Elementor e customizações robustas em CSS e PHP para oferecer funcionalidades premium sem custos de licenças adicionais.

---

## 🚀 Diferenciais & Arquitetura Técnica

### 1. Compatibilidade com Elementor Free
Para economizar custos de licenciamento com o Elementor Pro, foram desenvolvidos componentes nativos sob medida em PHP e JavaScript, estendendo as capacidades da versão gratuita do page builder:
- **Formulário de Captura Customizado (`[leadsnap_form]`):** Shortcode em PHP integrado à LGPD com validação de campos (Nome, E-mail, WhatsApp) e redirecionamento dinâmico para a página de Obrigado.
- **Contador Regressivo Inteligente (`[leadsnap_countdown]`):** Widget em JS nativo com suporte a contagem regressiva fixa ou modo Evergreen (reinicia individualmente para cada usuário baseado em cookies/localStorage para criar senso de urgência autêntico).

### 2. Design System Avançado com Variáveis CSS
A estilização é governada por CSS customizado centralizado, facilitando a alteração rápida de paletas de cores, tipografia e espaçamentos sem tocar no page builder:
- **Tema Escuro Nativo (Navy/Orange/Amber):** Combinação moderna com efeitos de *glassmorphism* e brilhos de neon sutil.
- **Responsividade e Alinhamento:** Centralização horizontal e vertical automatizada (via CSS Grid e Flexbox) de contadores, badges de urgência, acordeões de FAQ e rodapés de termos legais.
- **Cor de Fundo Dinâmica:** A cor do `body` é atrelada a variáveis do CSS (`var(--ls-bg-primary) !important`) eliminando bordas ou espaços em branco sob o layout de Canvas do Elementor em telas ultra-wide.

### 3. Otimização e Fallback Estrutural (PHP & WordPress Core)
- **Fallback Seguro:** O `index.php` do tema filho delega dinamicamente a renderização para o arquivo de entrada do tema pai (`Hello Elementor`), permitindo que páginas padrão do WordPress (como Política de Privacidade e Termos de Uso) carreguem seus cabeçalhos e conteúdos perfeitamente em vez de exibirem telas em branco.
- **Links Amigáveis Ativados:** Automação via hooks do WordPress (`after_switch_theme`) para forçar a estrutura de permalinks amigáveis (`/%postname%/`), garantindo rotas limpas como `/obrigado` e `/politica-de-privacidade`.
- **Publicação Automatizada de Páginas Legais:** Rotinas que garantem que as páginas legais necessárias para tráfego pago não fiquem como rascunhos no banco de dados.

---

## 📁 Estrutura do Tema Filho (`wp-content/themes/leadsnap-child/`)

```bash
leadsnap-child/
├── assets/
│   ├── css/           # Estilizações modulares
│   └── js/
│       ├── main.js    # Inicialização e scripts gerais
│       └── countdown.js # Lógica do contador regressivo Evergreen
├── docs/              # Decisões de Arquitetura (ADRs)
│   └── adr/
├── functions.php      # Hooks do WP, enfileiramento de scripts e Shortcode do Formulário
├── index.php          # Arquivo de segurança e delegação ao Tema Pai
└── style.css          # Design System, variáveis de paleta de cores e correções de layout
```

---

## 🛠️ Tecnologias Utilizadas
* **PHP / WordPress API:** Ganchos (`actions` e `filters`), Shortcodes e manipulação de metadados do Elementor.
* **Modern CSS:** CSS Variables, Flexbox, CSS Grid, Media Queries avançadas para responsividade e animações `@keyframes` personalizadas (efeito pulse de CTA).
* **Vanilla JavaScript:** Manipulação de DOM, Cookie Storage e localStorage para persistência de dados de sessão no widget de urgência.
* **Elementor Page Builder:** Configuração avançada de layouts de colunas e injeção dinâmica de shortcodes.
