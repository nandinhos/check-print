# Crystalline Design System — Manual de Padrões para Agentes de IA

> **Para agentes de IA:** Este documento é o guia canônico de implementação do Crystalline Design System.
> Leia-o integralmente antes de aplicar qualquer estilização. Siga as regras exatamente como descritas.
> Quando em dúvida, prefira sempre o padrão mais simples e consistente com o que já existe.

---

## Índice

1. [Visão Geral e Pré-requisitos](#1-visão-geral-e-pré-requisitos)
2. [Fundação — CSS Setup](#2-fundação--css-setup)
3. [Design Tokens — Referência Completa](#3-design-tokens--referência-completa)
4. [Tipografia](#4-tipografia)
5. [Espaçamento e Layout](#5-espaçamento-e-layout)
6. [Classes Utilitárias Globais](#6-classes-utilitárias-globais)
7. [Componentes UI](#7-componentes-ui)
   - [Button](#71-button)
   - [Card](#72-card)
   - [Input](#73-input)
   - [Checkbox](#74-checkbox)
   - [Toggle](#75-toggle)
   - [Badge](#76-badge)
   - [Avatar](#77-avatar)
   - [IconBox](#78-iconbox)
   - [Statistic](#79-statistic)
   - [LogEntry](#710-logentry)
   - [Dropdown](#711-dropdown)
   - [Modal](#712-modal)
8. [Componentes de Layout](#8-componentes-de-layout)
   - [Sidebar](#81-sidebar)
   - [Header](#82-header)
   - [NavItem](#83-navitem)
9. [Sistema de Temas (Dark / Light)](#9-sistema-de-temas-dark--light)
10. [Padrões de Composição](#10-padrões-de-composição)
11. [Regras de Ouro para o Agente](#11-regras-de-ouro-para-o-agente)
12. [Referência Rápida — Cheatsheet](#12-referência-rápida--cheatsheet)

---

## 1. Visão Geral e Pré-requisitos

### O que é o Crystalline?

Crystalline é um design system premium baseado em **glass-morphism** (vidro fosco / frosted glass). Seu DNA visual é:

- Superfícies translúcidas com `backdrop-blur` e opacidade
- Bordas sutis como luz difratada através do vidro
- Gradientes suaves e sombras profundas
- Tipografia com hierarquia forte usando 3 famílias distintas
- Tema duplo (Light: Crystalline / Dark: Obsidian) comutável em runtime

### Stack obrigatória

| Tecnologia | Versão mínima | Finalidade |
|---|---|---|
| **Tailwind CSS** | 4.x (com `@theme`) | Sistema de classes e tokens |
| **Alpine.js** | 3.x | Interatividade (dropdown, modal, toggle) |
| **Material Symbols** (Google) | Outlined | Ícones em todos os componentes |
| **Inter** | 400, 500, 600, 700 | Fonte sans-serif padrão |
| **Space Grotesk** | 500, 600, 700 | Fonte display (títulos) |
| **JetBrains Mono** | 400, 500 | Fonte mono (código, logs, dados) |

### Como carregar as fontes (HTML `<head>`)

```html
<!-- Cole isso no <head> do layout principal -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- Material Symbols (ícones) -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
```

### Como carregar o Alpine.js

```html
<!-- Via CDN (desenvolvimento) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 2. Fundação — CSS Setup

Cole o bloco abaixo no arquivo CSS principal do projeto (normalmente `app.css` ou `global.css`).
Este bloco define TODOS os tokens do sistema. Sem ele, nada funciona.

```css
/* ============================================================
   CRYSTALLINE DESIGN SYSTEM — Fundação CSS
   Copie este bloco integralmente para o CSS principal do projeto.
   Tailwind CSS 4.x com @theme é obrigatório.
   ============================================================ */

@import "tailwindcss";

/* Declara a variante dark baseada na classe .dark no HTML */
@custom-variant dark (&:where(.dark, .dark *));

/* ---------------------------------------------------------
   @theme — Registra os tokens no sistema do Tailwind 4.x
   Isso faz com que classes como `text-brand-500`, `bg-surface`,
   `shadow-crystal` funcionem nativamente como qualquer classe Tailwind.
   --------------------------------------------------------- */
@theme {
    /* Fontes — mapeiam para as variáveis CSS definidas no :root */
    --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
    --font-display: "Space Grotesk", ui-sans-serif, system-ui, sans-serif;
    --font-mono: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

    /* Escala de cores da marca (referencia as variáveis CSS do :root) */
    --color-brand-50:  var(--brand-50);
    --color-brand-100: var(--brand-100);
    --color-brand-200: var(--brand-200);
    --color-brand-300: var(--brand-300);
    --color-brand-400: var(--brand-400);
    --color-brand-500: var(--brand-500);
    --color-brand-600: var(--brand-600);
    --color-brand-700: var(--brand-700);
    --color-brand-800: var(--brand-800);
    --color-brand-900: var(--brand-900);
    --color-brand-950: var(--brand-950);

    /* Superfícies semânticas — mudam automaticamente com o tema */
    --color-surface: var(--bg-surface);
    --color-panel:   var(--bg-panel);
    --color-accent:  var(--accent-color);

    /* Sombras nomeadas — usadas como `shadow-crystal` e `shadow-soft` */
    --shadow-crystal: var(--shadow-crystal-val);
    --shadow-soft:    var(--soft-shadow-val);
}

/* ---------------------------------------------------------
   @layer base — Tokens de tema e reset do body
   --------------------------------------------------------- */
@layer base {

    /* TEMA LIGHT (padrão — sem nenhuma classe no HTML) */
    :root {
        /* Superfícies */
        --bg-surface: #F8FAFC;                   /* Fundo da página */
        --bg-panel:   rgba(255, 255, 255, 0.5);  /* Cards, painéis */
        --bg-header:  rgba(255, 255, 255, 0.65); /* Header sticky */

        /* Texto */
        --text-main:      #0F172A; /* Primário — slate-900 */
        --text-secondary: #475569; /* Secundário — slate-600 */
        --text-muted:     #94A3B8; /* Silenciado — slate-400 */

        /* Bordas de vidro */
        --border-glass:  rgba(255, 255, 255, 0.8); /* Borda principal de painéis */
        --border-subtle: rgba(0, 0, 0, 0.05);      /* Borda fina, quase invisível */

        /* Cores de marca */
        --brand-blue: #3B82F6;
        --brand-teal: #14B8A6;
        --accent-color: var(--brand-blue);

        /* Sombras */
        --shadow-crystal-val: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
        --soft-shadow-val:    0 4px 20px -2px rgba(0, 0, 0, 0.05),
                              0 2px 10px -2px rgba(0, 0, 0, 0.03);

        /* Escala de azul da marca (Light) */
        --brand-50:  #eff6ff;
        --brand-100: #dbeafe;
        --brand-200: #bfdbfe;
        --brand-300: #93c5fd;
        --brand-400: #60a5fa;
        --brand-500: #3b82f6; /* ← Primária da marca */
        --brand-600: #2563eb;
        --brand-700: #1d4ed8;
        --brand-800: #1e40af;
        --brand-900: #1e3a8a;
        --brand-950: #172554;
    }

    /* TEMA DARK (ativo quando <html class="dark">) */
    .dark {
        /* Superfícies — fundo quase preto, como obsidiana */
        --bg-surface: #020617;                  /* Quase preto */
        --bg-panel:   rgba(2, 6, 23, 0.4);     /* Painel translúcido escuro */
        --bg-header:  rgba(2, 6, 23, 0.6);     /* Header mais opaco */

        /* Texto — invertido */
        --text-main:      #F8FAFC;
        --text-secondary: #94A3B8;
        --text-muted:     #475569;

        /* Bordas — quase imperceptíveis sobre fundo escuro */
        --border-glass:  rgba(255, 255, 255, 0.05);
        --border-subtle: rgba(255, 255, 255, 0.02);

        /* Cores de marca — mais claras para contrastar com fundo escuro */
        --brand-blue: #60A5FA;
        --brand-teal: #2DD4BF;
        --accent-color: var(--brand-blue);

        /* Sombras — mais profundas no dark */
        --shadow-crystal-val: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
        --soft-shadow-val:    0 4px 20px -2px rgba(0, 0, 0, 0.3),
                              0 2px 10px -2px rgba(0, 0, 0, 0.2);

        /* Escala de azul da marca (Dark) — invertida */
        --brand-50:  #081222;
        --brand-100: #0c1c38;
        --brand-200: #10264e;
        --brand-300: #143064;
        --brand-400: #183a7a;
        --brand-500: #3b82f6; /* ← mantido igual para consistência */
        --brand-600: #60a5fa;
        --brand-700: #93c5fd;
        --brand-800: #bfdbfe;
        --brand-900: #dbeafe;
        --brand-950: #eff6ff;
    }

    /* BODY — configuração base */
    body {
        background-color: var(--bg-surface);
        color: var(--text-main);
        font-family: var(--font-sans);
        /* Suavização de fontes — obrigatório para o visual premium */
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        /* Transição suave ao trocar tema */
        transition: color 0.3s ease, background-color 0.3s ease;

        /* Gradiente ambiental sutil — dá profundidade à página */
        background-image:
            radial-gradient(at 0% 0%,   rgba(59, 130, 246, 0.03) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.03) 0px, transparent 50%);
    }

    .dark body {
        /* Gradiente mais presente no dark para criar atmosfera */
        background-image:
            radial-gradient(at 0% 0%,   rgba(59, 130, 246, 0.05) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(20, 184, 166, 0.05) 0px, transparent 50%);
    }
}

/* ---------------------------------------------------------
   @layer components — Classes reutilizáveis nomeadas
   --------------------------------------------------------- */
@layer components {

    /* FROSTED GLASS — padrão base de todas as superfícies translúcidas
       Use em painéis, headers, dropdowns, qualquer elemento "flutuante". */
    .frosted-glass {
        backdrop-filter: blur(28px) saturate(190%);
        background-color: var(--bg-panel);
        border: 1px solid var(--border-glass);
        box-shadow: var(--shadow-crystal-val);
    }

    /* FROSTED HEADER — versão para header/navbar fixo no topo */
    .frosted-header {
        position: sticky;
        top: 0;
        z-index: 50;
        background-color: var(--bg-header);
        backdrop-filter: blur(32px) saturate(200%);
        border-bottom: 1px solid var(--border-glass);
    }

    /* PREMIUM CARD — superfície principal de cards com efeito de difração
       Inclui sombras internas que simulam luz passando pelo vidro.
       Tem hover com scale sutil e sombra aumentada. */
    .premium-card {
        backdrop-filter: blur(28px) saturate(190%);
        background-color: var(--bg-panel);
        border: 1px solid var(--border-glass);
        border-radius: 1rem;
        box-shadow:
            inset 0 1px 1px rgba(255, 255, 255, 0.1),   /* Reflexo superior */
            inset 0 0 20px rgba(255, 255, 255, 0.02),   /* Brilho interno */
            0 8px 32px -12px rgba(0, 0, 0, 0.5);        /* Sombra de profundidade */
        transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-card:hover {
        transform: scale(1.005); /* Elevação sutil no hover */
        box-shadow:
            inset 0 1px 1px rgba(255, 255, 255, 0.15),
            inset 0 0 30px rgba(255, 255, 255, 0.04),
            0 12px 40px -12px rgba(0, 0, 0, 0.6);
        border-color: rgba(255, 255, 255, 0.12);
    }

    /* MONO TEXT — para logs, código, métricas e dados técnicos */
    .mono-text {
        font-family: var(--font-mono);
        letter-spacing: -0.025em; /* Comprime levemente para dados numéricos */
    }
}

/* ---------------------------------------------------------
   SCROLLBAR CUSTOMIZADA — discreta, combina com o tema
   --------------------------------------------------------- */
::-webkit-scrollbar       { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }

::-webkit-scrollbar-thumb {
    background-color: #cbd5e1; /* slate-300 */
    border-radius: 9999px;
    transition: background-color 0.2s;
}

.dark ::-webkit-scrollbar-thumb {
    background-color: #3f3f46; /* zinc-700 */
}

::-webkit-scrollbar-thumb:hover      { background-color: #94a3b8; } /* slate-400 */
.dark ::-webkit-scrollbar-thumb:hover { background-color: #52525b; } /* zinc-600 */
```

---

## 3. Design Tokens — Referência Completa

### 3.1 Paleta de Cores

#### Cores da Marca (Brand Scale)

A marca usa azul como cor primária. A escala é invertida no dark mode para criar profundidade.

| Token | Light | Dark | Uso típico |
|---|---|---|---|
| `brand-50` | `#eff6ff` | `#081222` | Fundos de hover muito suaves |
| `brand-100` | `#dbeafe` | `#0c1c38` | Backgrounds de destaque passivo |
| `brand-200` | `#bfdbfe` | `#10264e` | Borders sutis com cor |
| `brand-300` | `#93c5fd` | `#143064` | Ícones inativos com cor |
| `brand-400` | `#60a5fa` | `#183a7a` | Hover states |
| `brand-500` | `#3b82f6` | `#3b82f6` | **Primária — uso geral** |
| `brand-600` | `#2563eb` | `#60a5fa` | Gradientes, deep emphasis |
| `brand-700` | `#1d4ed8` | `#93c5fd` | Texto sobre fundo claro |
| `brand-800` | `#1e40af` | `#bfdbfe` | Cabeçalhos em contexto claro |
| `brand-900` | `#1e3a8a` | `#dbeafe` | Texto principal dark brand |
| `brand-950` | `#172554` | `#eff6ff` | Máximo contraste brand |

#### Cores de Status / Semânticas

Use estas cores consistentemente em todo o sistema para comunicar estado:

```
✅ Sucesso / Positivo:  emerald-500  (#10B981)
⚠️  Aviso / Alerta:     amber-500   (#F59E0B)
❌ Erro / Destrutivo:   rose-500    (#F43F5E)
ℹ️  Informação / Neutro: slate-500   (#64748B)
```

#### Superfícies Semânticas (variam com o tema)

Sempre use variáveis CSS, **nunca** valores hex fixos para superfícies:

```
var(--bg-surface)    → Fundo da página
var(--bg-panel)      → Cards, painéis, dropdowns
var(--bg-header)     → Header/navbar fixo
var(--text-main)     → Texto principal
var(--text-secondary)→ Texto de suporte
var(--text-muted)    → Labels, placeholders, texto silenciado
var(--border-glass)  → Borda de qualquer superfície de vidro
var(--border-subtle) → Divisores internos muito sutis
var(--accent-color)  → Cor de acento (alias de brand-blue)
```

### 3.2 Sombras

```
shadow-crystal → Sombra principal de cards e painéis
shadow-soft    → Sombra suave para elementos menores (inputs, badges)
```

Em CSS puro (para sistemas sem Tailwind 4):
```css
/* Light */
box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);

/* Dark */
box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
```

---

## 4. Tipografia

### 4.1 Famílias de Fonte

O sistema usa **três famílias distintas** com papéis bem definidos:

| Família | Variável CSS | Classe Tailwind | Quando usar |
|---|---|---|---|
| **Inter** | `var(--font-sans)` | `font-sans` | Corpo de texto, labels, inputs, UI geral |
| **Space Grotesk** | `var(--font-display)` | `font-display` | Títulos, valores de métricas, logo |
| **JetBrains Mono** | `var(--font-mono)` | `font-mono` | Código, logs, dados técnicos, timestamps |

### 4.2 Hierarquia Tipográfica

```
PÁGINA
├── Título de seção principal  → font-display text-xl font-bold tracking-tight
├── Subtítulo / Label          → font-sans text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)]
├── Corpo de texto             → font-sans text-sm text-[var(--text-secondary)]
├── Texto de destaque          → font-sans text-sm font-bold text-[var(--text-main)]
├── Valor de métrica           → font-display text-xl font-black tracking-tight
├── Label de badge/tag         → text-[10px] font-bold uppercase tracking-wider
└── Texto técnico/log          → font-mono text-[11px]
```

### 4.3 Pesos de Fonte

```
font-normal  → 400  → Corpo de texto longo
font-medium  → 500  → Texto de suporte levemente enfatizado
font-semibold→ 600  → Labels, subtítulos
font-bold    → 700  → Títulos, botões, estados ativos
font-black   → 900  → Valores de métricas, KPIs, display numbers
```

### 4.4 Regras de Letter Spacing

```
tracking-tight   → -0.025em → Títulos display, números grandes
tracking-normal  →  0em     → Corpo de texto
tracking-wide    → 0.025em  → —
tracking-wider   → 0.05em   → Labels de seção
tracking-widest  → 0.1em    → Labels uppercase de pequeno porte (badges, subtítulos)
```

---

## 5. Espaçamento e Layout

### 5.1 Unidade Base

Tailwind usa 4px como unidade base (`spacing-1 = 4px`).
No Crystalline, os espaçamentos mais usados:

```
p-2   → 8px   → Padding interno mínimo (badges, ícones)
p-3   → 12px  → Cards de estatística, elementos compactos
p-4   → 16px  → Padding padrão de seções
p-5   → 20px  → Padding de cards principais
p-6   → 24px  → Padding generoso
gap-2 → 8px   → Espaço entre ícone e label
gap-3 → 12px  → Espaço entre elementos de um card
gap-4 → 16px  → Espaço entre itens de lista
gap-6 → 24px  → Espaço entre cards no grid
```

### 5.2 Border Radius

O sistema usa border-radius generoso para o visual premium:

```
rounded-lg   → 0.5rem  → Hover states, elementos menores
rounded-xl   → 0.75rem → Ícones, tags, elementos médios
rounded-2xl  → 1rem    → Inputs, dropdowns, elementos de formulário
rounded-3xl  → 1.5rem  → Modais, cards grandes
rounded-full → 50%     → Avatares circle, toggles, badges de status
```

### 5.3 Grid de Layout

```html
<!-- Grid padrão de 3 colunas (cards de dashboard) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Grid de 4 colunas (estatísticas compactas) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

<!-- Layout sidebar + conteúdo -->
<div class="flex min-h-screen">
    <aside><!-- 280px sidebar --></aside>
    <main class="flex-1 overflow-auto"><!-- conteúdo --></main>
</div>
```

---

## 6. Classes Utilitárias Globais

Estas classes são definidas no CSS e representam padrões compostos do sistema.
**Use-as em vez de replicar manualmente as propriedades.**

### `.frosted-glass`

Superfície de vidro fosco. Padrão para qualquer elemento "flutuante".

```html
<!-- Sidebar, dropdown, painel lateral, tooltip container -->
<div class="frosted-glass rounded-2xl p-4">
    Conteúdo
</div>
```

### `.frosted-header`

Para navbars e headers fixos no topo da página.

```html
<header class="frosted-header">
    Conteúdo do header
</header>
```

### `.premium-card`

Card com efeito de difração de luz, hover com scale. Use como wrapper de seções de conteúdo.

```html
<div class="premium-card p-5">
    Conteúdo do card
</div>
```

### `.mono-text`

Para qualquer texto técnico: timestamps, hashes, valores numéricos, código, logs.

```html
<span class="mono-text text-sm">192.168.1.1</span>
<span class="mono-text text-[11px] text-[var(--text-muted)]">2024-01-15 14:30:00</span>
```

---

## 7. Componentes UI

> **Para agentes:** Cada componente é um Blade Component do Laravel (`x-ui.nome`).
> Se aplicando em outro framework, use o código base como referência visual e replique o padrão de classes.

### 7.1 Button

**Propósito:** Ação primária ou secundária do usuário. Nunca use `<a>` estilizado como botão para ações — use `<button>`.

#### Props

| Prop | Tipo | Padrão | Valores aceitos |
|---|---|---|---|
| `variant` | string | `primary` | `primary`, `secondary`, `success`, `frosted`, `ghost`, `danger` |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg` |
| `icon` | string | `null` | Nome do Material Symbol (ex: `add`, `delete`) |
| `iconRight` | string | `null` | Nome do Material Symbol (para ícone à direita) |
| `loading` | bool | `false` | Exibe spinner e desabilita o botão |

#### Anatomia Visual

```
[ícone-esquerda] [texto do slot] [ícone-direita]
     20px               ↑              20px
               font-bold text-sm
```

#### Classes base (aplicadas em todos os botões)

```
inline-flex items-center justify-center gap-2
font-bold tracking-tight rounded-xl
transition-all duration-300
active:scale-[0.97]          ← feedback visual de clique
disabled:opacity-50 disabled:pointer-events-none
ring-offset-2 dark:ring-offset-[#020617]
focus:outline-none focus:ring-2
border
```

#### Variantes — Classes completas

```php
// PRIMARY — azul gradiente, para a ação mais importante da tela
'bg-gradient-to-b from-brand-500 to-brand-600 text-white
 shadow-[0_4px_12px_rgba(59,130,246,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)]
 hover:from-brand-400 hover:to-brand-500
 hover:shadow-[0_6px_20px_rgba(59,130,246,0.4),inset_0_1px_1px_rgba(255,255,255,0.3)]
 ring-brand-500/40 border-transparent dark:border-brand-400/20'

// SECONDARY — translúcido, para ações secundárias
'bg-white/40 dark:bg-white/5 text-slate-900 dark:text-slate-100
 border-slate-200/50 dark:border-white/5
 shadow-sm backdrop-blur-md
 hover:bg-white/60 dark:hover:bg-white/10
 ring-brand-500/20'

// SUCCESS — verde, para confirmações e ações positivas
'bg-gradient-to-b from-emerald-500 to-emerald-600 text-white
 shadow-[0_4px_12px_rgba(16,185,129,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)]
 hover:from-emerald-400 hover:to-emerald-500
 hover:shadow-[0_6px_20px_rgba(16,185,129,0.4)]
 ring-emerald-500/40 border-transparent dark:border-emerald-400/20'

// FROSTED — ultra-translúcido, para botões sobre imagens ou fundos ricos
'bg-white/10 dark:bg-white/5 text-slate-900 dark:text-white
 border-white/20 dark:border-white/5
 shadow-[0_4px_12px_rgba(0,0,0,0.05),inset_0_1px_1px_rgba(255,255,255,0.4)]
 backdrop-blur-2xl saturate-150
 hover:bg-white/20 dark:hover:bg-white/10
 ring-brand-500/20'

// GHOST — mínimo, sem fundo; para ações terciárias
'text-[var(--text-secondary)] hover:text-[var(--text-main)]
 hover:bg-white/40 dark:hover:bg-white/5
 ring-slate-200/20 backdrop-blur-sm border-transparent'

// DANGER — vermelho, APENAS para ações destrutivas (deletar, remover)
'bg-gradient-to-b from-rose-500 to-rose-600 text-white
 shadow-[0_4px_12px_rgba(244,63,94,0.3),inset_0_1px_1px_rgba(255,255,255,0.2)]
 hover:from-rose-400 hover:to-rose-500
 hover:shadow-[0_6px_20px_rgba(244,63,94,0.4)]
 ring-rose-500/40 border-transparent dark:border-rose-400/20'
```

#### Tamanhos

| Size | Padding | Font size | Gap | Quando usar |
|---|---|---|---|---|
| `xs` | `px-3 py-1.5` | `text-[10px]` | `gap-1.5` | Ações em tabelas, espaços muito compactos |
| `sm` | `px-4 py-2` | `text-xs` | `gap-2` | Ações secundárias, toolbars |
| `md` | `px-6 py-2.5` | `text-sm` | `gap-2` | **Padrão** — maioria dos botões |
| `lg` | `px-8 py-3.5` | `text-base` | `gap-2.5` | CTAs de destaque, heroes |

#### Exemplos de uso (Blade)

```blade
{{-- Ação principal --}}
<x-ui.button variant="primary" icon="save">
    Salvar Alterações
</x-ui.button>

{{-- Com ícone à direita e loading --}}
<x-ui.button variant="primary" icon-right="arrow_forward" :loading="$processing">
    Continuar
</x-ui.button>

{{-- Ação destrutiva —  sempre confirme antes de executar --}}
<x-ui.button variant="danger" icon="delete" size="sm">
    Excluir
</x-ui.button>

{{-- Botão fantasma para ações terciárias --}}
<x-ui.button variant="ghost" icon="close">
    Cancelar
</x-ui.button>
```

#### Regras

- **Primary** = máximo 1 por seção/modal. É a ação mais importante.
- **Danger** = sempre acompanhe de confirmação (modal ou dialog).
- **Ghost** = use para cancelar, fechar, ações sem consequência.
- Nunca coloque dois botões `primary` lado a lado. O segundo deve ser `secondary`.

---

### 7.2 Card

**Propósito:** Container de seção de conteúdo. A unidade visual fundamental do layout.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `title` | string | `null` | Título interno do card (`font-display text-sm font-bold`) |
| `subtitle` | string | `null` | Rótulo acima do título (`text-[10px] uppercase tracking-widest`) |
| `glow` | bool | `true` | Ativa o glow interativo que segue o mouse |

#### Efeito de Glow Interativo

O card rastreia a posição do mouse via Alpine.js e projeta um gradiente radial translúcido
que cria a ilusão de uma luz interna seguindo o cursor. Isso é o principal diferencial visual do sistema.

```javascript
// Alpine.js interno — não modifique
x-data="{
    handleMouseMove(e) {
        let rect = this.$el.getBoundingClientRect();
        this.$el.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
        this.$el.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
    }
}"
@mousemove="handleMouseMove"
```

```css
/* O glow usa CSS custom properties definidas dinamicamente */
background: radial-gradient(
    600px circle at var(--mouse-x) var(--mouse-y),
    var(--glow-color),   /* definir esta variável na página ou inline */
    transparent 40%
);
```

#### Estrutura HTML gerada

```html
<div class="relative premium-card p-5 group flex flex-col h-full">

    <!-- Camada de glow (pointer-events-none, não interfere nos cliques) -->
    <div class="pointer-events-none absolute -inset-px opacity-0 transition-opacity
                duration-500 group-hover:opacity-100"
         style="background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y),
                var(--glow-color), transparent 40%); z-index: 0; border-radius: inherit;">
    </div>

    <!-- Header opcional do card -->
    <div class="mb-4 shrink-0">
        <h3 class="text-sm font-bold text-[var(--text-main)] font-display tracking-tight">
            Título
        </h3>
        <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1">
            Subtítulo
        </p>
    </div>

    <!-- Slot de conteúdo (z-10 para ficar sobre o glow) -->
    <div class="relative z-10 flex-1 flex flex-col">
        <!-- conteúdo aqui -->
    </div>
</div>
```

#### Exemplos de uso (Blade)

```blade
{{-- Card simples com conteúdo --}}
<x-ui.card title="Usuários Ativos" subtitle="Últimas 24h">
    <p class="text-3xl font-black font-display">1.284</p>
</x-ui.card>

{{-- Card sem glow (para conteúdos estáticos sem interação) --}}
<x-ui.card title="Logs do Sistema" :glow="false">
    <!-- lista de logs -->
</x-ui.card>

{{-- Card com classes extras (ocupa a largura toda) --}}
<x-ui.card title="Gráfico de Vendas" class="col-span-2">
    <!-- gráfico -->
</x-ui.card>
```

#### Cor do Glow

Para customizar a cor do glow, defina a variável CSS `--glow-color` no elemento pai ou no style inline:

```html
<!-- Glow azul (padrão da marca) -->
<x-ui.card style="--glow-color: rgba(59,130,246,0.15)">

<!-- Glow emerald (para cards de sucesso/métricas positivas) -->
<x-ui.card style="--glow-color: rgba(16,185,129,0.15)">

<!-- Glow rose (para alertas ou dados críticos) -->
<x-ui.card style="--glow-color: rgba(244,63,94,0.15)">
```

---

### 7.3 Input

**Propósito:** Campo de texto para formulários. Design unificado com o sistema de vidro.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `label` | string | `null` | Label acima do campo |
| `icon` | string | `null` | Ícone Material Symbol à esquerda do campo |
| `error` | string | `null` | Mensagem de erro (muda borda para rose) |
| `type` | string | `text` | Tipo HTML do input |

#### Estados visuais

```
Normal:  borda var(--border-glass), fundo branco/40 (light) ou zinc-900/40 (dark)
Focus:   ring-4 ring-brand-500/10, border-brand-500/50, fundo opaco (branco / zinc-900)
Erro:    border-rose-500/50, ring-rose-500/10, ícone de error à direita
```

#### Classes do input

```
w-full
bg-white/40 dark:bg-zinc-900/40
border border-[var(--border-glass)]
backdrop-blur-md
rounded-2xl
text-sm font-medium text-[var(--text-main)]
placeholder:text-[var(--text-muted)]
focus:ring-4 focus:ring-brand-500/10
focus:border-brand-500/50
focus:bg-white dark:focus:bg-zinc-900
transition-all shadow-sm
py-3 px-4                       ← sem ícone
py-3 pl-12 pr-4                 ← com ícone à esquerda
```

#### Label

```
block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider ml-1
```

#### Ícone interno

```
material-symbols-outlined
absolute left-4 top-1/2 -translate-y-1/2
text-[var(--text-muted)] text-[20px]
transition-colors group-focus-within:text-brand-500   ← muda de cor no focus
```

#### Exemplos de uso (Blade)

```blade
{{-- Input básico --}}
<x-ui.input label="E-mail" type="email" placeholder="seu@email.com" />

{{-- Com ícone --}}
<x-ui.input label="Buscar" icon="search" placeholder="Pesquisar..." />

{{-- Com estado de erro --}}
<x-ui.input
    label="Senha"
    type="password"
    icon="lock"
    error="A senha deve ter ao menos 8 caracteres"
    wire:model.live="password"
/>

{{-- Vinculado ao Livewire --}}
<x-ui.input label="Nome Completo" wire:model="name" />
```

---

### 7.4 Checkbox

**Propósito:** Seleção binária em formulários. Com suporte a label e descrição.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `label` | string | `null` | Texto principal ao lado do checkbox |
| `description` | string | `null` | Texto explicativo abaixo do label |

#### Anatomia visual

```
[☐] Label principal em font-bold
    Descrição em text-xs text-secondary
```

#### Classes do input `[type="checkbox"]`

```
peer relative h-5 w-5 cursor-pointer appearance-none
rounded-md
border border-[var(--border-subtle)]
bg-white/40 dark:bg-black/20
transition-all
before:content-[''] before:absolute before:top-1/2 before:left-1/2
before:block before:h-12 before:w-12
before:-translate-y-1/2 before:-translate-x-1/2
before:rounded-full before:bg-brand-500
before:opacity-0 before:transition-opacity
hover:before:opacity-10 dark:hover:before:opacity-20    ← ripple effect
checked:border-brand-500 checked:bg-brand-500           ← estado selecionado
shadow-sm
```

#### Exemplos de uso (Blade)

```blade
{{-- Checkbox simples --}}
<x-ui.checkbox label="Aceitar termos de uso" />

{{-- Com descrição e vinculado ao Livewire --}}
<x-ui.checkbox
    label="Receber notificações"
    description="Enviaremos alertas por e-mail quando houver novidades"
    wire:model="notifications"
/>
```

---

### 7.5 Toggle

**Propósito:** Interruptor on/off. Para configurações binárias (habilitar/desabilitar feature).

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `label` | string | `null` | Texto ao lado do toggle |
| `description` | string | `null` | Descrição explicativa |

#### Anatomia visual

```
[○        ] Label   ← desligado (slate-200/800)
[        ●] Label   ← ligado (brand-500)
```

#### Classes do track (div visual)

```
w-11 h-6
bg-slate-200 dark:bg-slate-800
rounded-full
peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/30
peer-checked:after:translate-x-full peer-checked:after:border-white
after:content-[''] after:absolute after:top-[2px] after:left-[2px]
after:bg-white after:border-gray-300 after:border
after:rounded-full after:h-5 after:w-5
after:transition-all
peer-checked:bg-brand-500
hover:peer-checked:bg-brand-400
shadow-inner dark:shadow-[inset_0_2px_4px_rgba(0,0,0,0.4)]
```

#### Exemplos de uso (Blade)

```blade
{{-- Toggle simples --}}
<x-ui.toggle label="Modo manutenção" />

{{-- Com descrição e wire:model --}}
<x-ui.toggle
    label="Alertas em tempo real"
    description="Receba notificações instantâneas de eventos críticos"
    wire:model="realTimeAlerts"
/>
```

---

### 7.6 Badge

**Propósito:** Rótulo de status, categoria ou estado. Sempre pequeno e compacto.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `variant` | string | `neutral` | Cor do badge |
| `pulse` | bool | `false` | Adiciona ponto pulsante animado |

#### Variantes disponíveis

| Variant | Background | Texto | Borda | Uso |
|---|---|---|---|---|
| `neutral` | `slate-100/50` | `slate-600` | `slate-200/50` | Status genérico |
| `primary` / `brand` | `brand-500/10` | `brand-600` | `brand-500/20` | Informação da marca |
| `secondary` | `slate-500/10` | `slate-600` | `slate-500/20` | Secundário |
| `success` | `emerald-500/10` | `emerald-600` | `emerald-500/20` | Ativo, OK, Publicado |
| `warning` | `amber-500/10` | `amber-600` | `amber-500/20` | Atenção, Pendente |
| `danger` | `rose-500/10` | `rose-600` | `rose-500/20` | Erro, Inativo, Bloqueado |

#### Classes base (comuns a todos os badges)

```
inline-flex items-center gap-1.5
px-2.5 py-0.5
rounded-lg
text-[10px] font-bold uppercase tracking-wider
```

#### Indicador de pulse (quando `pulse=true`)

```html
<span class="relative flex h-1.5 w-1.5">
    <!-- Anel pulsante externo (animate-ping) -->
    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {cor} opacity-75"></span>
    <!-- Ponto sólido interno -->
    <span class="relative inline-flex rounded-full h-1.5 w-1.5 {cor}"></span>
</span>
```

#### Exemplos de uso (Blade)

```blade
{{-- Status com ponto pulsante --}}
<x-ui.badge variant="success" :pulse="true">Online</x-ui.badge>

{{-- Categoria simples --}}
<x-ui.badge variant="brand">Premium</x-ui.badge>

{{-- Aviso --}}
<x-ui.badge variant="warning">Pendente</x-ui.badge>

{{-- Erro com pulse --}}
<x-ui.badge variant="danger" :pulse="true">Crítico</x-ui.badge>
```

---

### 7.7 Avatar

**Propósito:** Representação visual de um usuário. Com foto ou iniciais.

#### Props

| Prop | Tipo | Padrão | Valores |
|---|---|---|---|
| `src` | string | `null` | URL da imagem |
| `name` | string | `null` | Nome completo (gera iniciais automaticamente) |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl` |
| `status` | string | `null` | `online`, `offline`, `busy`, `away` |
| `shape` | string | `circle` | `circle`, `square` |

#### Tamanhos

| Size | Classe | Font size | Uso |
|---|---|---|---|
| `xs` | `size-6` | `text-[10px]` | Comentários, listas densas |
| `sm` | `size-8` | `text-xs` | Sidebar, itens de lista |
| `md` | `size-10` | `text-sm` | **Padrão** — header, cards |
| `lg` | `size-12` | `text-base` | Perfil, destaque |
| `xl` | `size-16` | `text-xl` | Página de perfil, hero |

#### Indicadores de status

| Status | Cor | Significado |
|---|---|---|
| `online` | `emerald-500` | Ativo agora |
| `offline` | `slate-400` | Desconectado |
| `busy` | `rose-500` | Ocupado / Não perturbe |
| `away` | `amber-500` | Ausente |

O indicador fica posicionado no canto inferior direito com `ring-2 ring-white dark:ring-[#020617]`
para criar separação visual entre o avatar e o ponto colorido.

#### Geração automática de iniciais

- `"João Silva"` → `"JS"`
- `"Maria"` → `"MA"`
- Sempre 2 caracteres, uppercase

#### Exemplos de uso (Blade)

```blade
{{-- Com foto e status --}}
<x-ui.avatar
    src="/storage/avatars/user-1.jpg"
    name="João Silva"
    size="md"
    status="online"
/>

{{-- Sem foto (iniciais) e shape quadrado --}}
<x-ui.avatar name="Ana Lima" size="lg" shape="square" />

{{-- Pequeno, sem status --}}
<x-ui.avatar name="Carlos" size="sm" />
```

---

### 7.8 IconBox

**Propósito:** Caixa de ícone com gradiente colorido. Use para representar categorias, features ou ações.

#### Props

| Prop | Tipo | Padrão | Valores |
|---|---|---|---|
| `icon` | string | — | Nome do Material Symbol (obrigatório) |
| `color` | string | `brand` | `brand`, `emerald`, `rose`, `amber`, `violet`, `cyan`, `indigo`, `orange` |
| `size` | string | `md` | `sm`, `md`, `lg` |

#### Tamanhos

| Size | Dimensão | Ícone |
|---|---|---|
| `sm` | `size-8` | `16px` |
| `md` | `size-10` | `20px` |
| `lg` | `size-12` | `24px` |

#### Classes base (comuns)

```
flex items-center justify-center
rounded-xl
bg-gradient-to-br
border
backdrop-blur-md
transition-all duration-300
group-hover:scale-105    ← cresce se o pai tiver classe `group`
```

#### Padrão de cores (cada cor define: gradient, text, border, inner shadow)

```php
// Exemplo para 'brand' (azul):
'from-brand-500/20 to-brand-500/5
 text-brand-500
 border-brand-500/20
 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1),inset_0_0_12px_rgba(59,130,246,0.1)]'
```

Mapa de cores RGB para inner shadows (use no shadow `inset_0_0_12px`):

```
brand   → rgba(59,130,246,0.1)   (azul)
emerald → rgba(16,185,129,0.1)   (verde)
rose    → rgba(244,63,94,0.1)    (vermelho)
amber   → rgba(245,158,11,0.1)   (amarelo)
violet  → rgba(139,92,246,0.1)   (roxo)
cyan    → rgba(6,182,212,0.1)    (ciano)
indigo  → rgba(99,102,241,0.1)   (índigo)
orange  → rgba(249,115,22,0.1)   (laranja)
```

#### Exemplos de uso (Blade)

```blade
{{-- Ícone de usuários (verde) --}}
<x-ui.icon-box icon="group" color="emerald" size="md" />

{{-- Ícone de alerta (vermelho, grande) --}}
<x-ui.icon-box icon="warning" color="rose" size="lg" />

{{-- Combinado com card --}}
<div class="group premium-card p-5">
    <div class="flex items-center gap-3">
        <x-ui.icon-box icon="analytics" color="brand" />
        <span class="font-bold text-[var(--text-main)]">Analytics</span>
    </div>
</div>
```

---

### 7.9 Statistic

**Propósito:** Exibição de uma métrica com label e tendência. Para dashboards e KPIs.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `label` | string | — | Rótulo da métrica (obrigatório) |
| `value` | string | — | Valor principal (obrigatório) |
| `trend` | string | `null` | Texto de tendência (ex: `+12%`, `↑ 5`) |
| `trendColor` | string | `emerald` | `emerald`, `rose`, `amber`, `brand`, `slate` |

#### Anatomia visual

```
┌─────────────────────────────────┐
│ LABEL DA MÉTRICA    [TREND]     │
│ Valor Principal                 │
└─────────────────────────────────┘
```

#### Classes do container

```
flex items-center justify-between
p-3 rounded-xl
bg-white/40 dark:bg-white/5
border border-[var(--border-glass)]
shadow-sm backdrop-blur-md
```

#### Classes do label

```
text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest
```

#### Classes do valor

```
text-xl font-black font-display tracking-tight text-[var(--text-main)]
```

#### Classes do trend

```
px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tighter
{trendColor background} {trendColor text}
```

#### Exemplos de uso (Blade)

```blade
{{-- Métrica com tendência positiva --}}
<x-ui.statistic label="Uptime" value="99.98%" trend="+0.02%" trend-color="emerald" />

{{-- Métrica com tendência negativa --}}
<x-ui.statistic label="Erros" value="14" trend="+3" trend-color="rose" />

{{-- Métrica sem tendência --}}
<x-ui.statistic label="Usuários" value="1.284" />
```

---

### 7.10 LogEntry

**Propósito:** Linha de log com tipo colorido. Para painéis de logs, terminais, histórico.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `time` | string | — | Timestamp da entrada (obrigatório) |
| `type` | string | — | Tipo do log (obrigatório) |
| `message` | string | — | Mensagem do log (obrigatório) |

#### Tipos de log e cores

| Type | Cor | Uso |
|---|---|---|
| `INFO` | `text-blue-500` | Informações gerais, eventos normais |
| `WARN` | `text-amber-500` | Avisos, itens que merecem atenção |
| `ERROR` | `text-rose-500` | Erros, falhas, exceções |
| `SUCCESS` | `text-emerald-500` | Operações bem-sucedidas |

#### Classes do container

```
flex items-start gap-3
p-2 rounded-lg
hover:bg-white/40 dark:hover:bg-white/5
transition-colors
font-mono text-[11px]
```

#### Exemplos de uso (Blade)

```blade
<x-ui.log-entry time="14:32:01" type="INFO"    message="Sistema inicializado com sucesso." />
<x-ui.log-entry time="14:32:15" type="WARN"    message="Uso de CPU acima de 80%." />
<x-ui.log-entry time="14:32:47" type="ERROR"   message="Falha na conexão com banco de dados." />
<x-ui.log-entry time="14:33:02" type="SUCCESS" message="Backup concluído: 2.3 GB transferidos." />
```

---

### 7.11 Dropdown

**Propósito:** Menu contextual que aparece ao clicar em um trigger.

#### Props

| Prop | Tipo | Padrão | Valores |
|---|---|---|---|
| `align` | string | `right` | `left`, `right`, `top` |
| `width` | string | `48` | `48` (12rem), `64` (16rem) |
| `contentClasses` | string | `py-1` | Classes extras do wrapper de conteúdo |

#### Slots

- `$trigger` — Qualquer elemento que ao ser clicado abre/fecha o dropdown
- `$content` — Os itens do menu

#### Comportamento

- Abre/fecha ao clicar no trigger
- Fecha ao clicar fora (`@click.outside`)
- Fecha ao clicar em qualquer item interno
- Gerencia z-index do card pai para evitar recorte

#### Animação de entrada/saída

```
Entrada: opacity-0 scale-95 → opacity-100 scale-100 (200ms ease-out)
Saída:   opacity-100 scale-100 → opacity-0 scale-95 (75ms ease-in)
```

#### Classes do painel dropdown

```
absolute z-50 mt-2
{width}
rounded-2xl shadow-xl
{alignmentClasses}
frosted-glass overflow-hidden
```

#### Exemplo de uso (Blade)

```blade
<x-ui.dropdown align="right" width="48">
    <x-slot name="trigger">
        <x-ui.button variant="ghost" icon="more_vert" size="sm" />
    </x-slot>

    <x-slot name="content">
        <x-ui.dropdown-link href="{{ route('profile') }}" icon="person">
            Perfil
        </x-ui.dropdown-link>
        <x-ui.dropdown-link href="{{ route('settings') }}" icon="settings">
            Configurações
        </x-ui.dropdown-link>
        <div class="border-t border-[var(--border-subtle)] my-1"></div>
        <x-ui.dropdown-link
            href="{{ route('logout') }}"
            class="text-rose-500 hover:text-rose-600"
            icon="logout"
        >
            Sair
        </x-ui.dropdown-link>
    </x-slot>
</x-ui.dropdown>
```

---

### 7.12 Modal

**Propósito:** Diálogo sobreposto à página. Para confirmações, formulários e detalhes.

#### Props

| Prop | Tipo | Padrão | Valores |
|---|---|---|---|
| `name` | string | — | Identificador único do modal (obrigatório) |
| `show` | bool | `false` | Controla visibilidade inicial |
| `maxWidth` | string | `2xl` | `sm`, `md`, `lg`, `xl`, `2xl` |

#### Abertura e fechamento

```javascript
// Abrir de qualquer lugar da página:
window.dispatchEvent(new CustomEvent('open-modal', { detail: 'nome-do-modal' }))

// Fechar:
window.dispatchEvent(new CustomEvent('close-modal', { detail: 'nome-do-modal' }))

// Blade / Livewire — via botão:
<x-ui.button @click="$dispatch('open-modal', 'nome-do-modal')">
    Abrir Modal
</x-ui.button>

// Livewire (do PHP):
$this->dispatch('open-modal', 'nome-do-modal');
```

#### Comportamento automático

- Bloqueia scroll da página (`overflow-y-hidden` no body) ao abrir
- Fecha ao pressionar `Escape`
- Fecha ao clicar no backdrop
- Trap de foco (Tab/Shift+Tab circula apenas dentro do modal)
- Se `focusable` estiver presente, foca o primeiro elemento focável automaticamente

#### Estilos do modal

```
// Backdrop:
bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm

// Painel:
bg-white/90 dark:bg-slate-900/80
backdrop-blur-3xl
rounded-3xl
shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)]
dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.7)]
border border-white/50 dark:border-white/10
ring-1 ring-slate-900/5 dark:ring-white/5
```

#### Animação

```
Entrada: opacity-0 + translate-y-4 + scale-95 → opaque + translate-0 + scale-100 (300ms ease-out)
Saída:   reverse (200ms ease-in)
```

#### Exemplo de uso (Blade)

```blade
{{-- Trigger --}}
<x-ui.button @click="$dispatch('open-modal', 'confirm-delete')" variant="danger">
    Excluir Conta
</x-ui.button>

{{-- Modal --}}
<x-ui.modal name="confirm-delete" max-width="md" focusable>
    <div class="p-6">
        <h2 class="text-lg font-bold font-display text-[var(--text-main)] mb-2">
            Confirmar Exclusão
        </h2>
        <p class="text-sm text-[var(--text-secondary)] mb-6">
            Esta ação não pode ser desfeita. Todos os dados serão permanentemente removidos.
        </p>
        <div class="flex gap-3 justify-end">
            <x-ui.button variant="ghost" @click="$dispatch('close-modal', 'confirm-delete')">
                Cancelar
            </x-ui.button>
            <x-ui.button variant="danger" wire:click="deleteAccount">
                Sim, excluir
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
```

---

## 8. Componentes de Layout

### 8.1 Sidebar

**Propósito:** Navegação lateral fixa. Sempre à esquerda, com largura de 280px.

#### Anatomia

```
┌──────────────┐
│  LOGO / NOME │  ← topo com ícone e nome da aplicação
├──────────────┤
│  [SEÇÃO]     │  ← label de seção uppercase tracking-widest
│  ▸ NavItem   │  ← itens de navegação
│  ▸ NavItem   │
├──────────────┤
│  perfil user │  ← card de usuário na base (avatar + nome + badge)
└──────────────┘
```

#### Classes do container

```
frosted-glass
flex flex-col
w-[280px] min-h-screen
rounded-r-3xl my-2 ml-2
```

#### Seção de label de grupo

```html
<p class="text-[9px] font-black uppercase tracking-[0.2em] text-[var(--text-muted)] px-4 mb-2">
    NOME DA SEÇÃO
</p>
```

---

### 8.2 Header

**Propósito:** Barra de navegação superior fixa. Altura de 80px (`h-20`).

#### Anatomia

```
┌─────────────────────────────────────────────────────────────┐
│ [Título da Página]   [🔍 Buscar...]   [🌙] [🔔] [Avatar ▾] │
└─────────────────────────────────────────────────────────────┘
```

#### Classes do container

```
frosted-header h-20 px-6
flex items-center justify-between gap-4
```

#### Campo de busca no header

```html
<div class="flex-1 max-w-md">
    <div class="relative group">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2
                     text-[var(--text-muted)] text-[18px] group-focus-within:text-brand-500
                     transition-colors">
            search
        </span>
        <input type="search"
               placeholder="Buscar... ⌘K"
               class="w-full bg-white/30 dark:bg-white/5 border border-[var(--border-glass)]
                      backdrop-blur-md rounded-xl text-sm pl-9 pr-4 py-2
                      text-[var(--text-main)] placeholder:text-[var(--text-muted)]
                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500/30
                      focus:bg-white dark:focus:bg-zinc-900/80 transition-all" />
    </div>
</div>
```

---

### 8.3 NavItem

**Propósito:** Item de navegação na sidebar. Tem estado ativo com glow e inativo.

#### Props

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `label` | string | — | Texto do item (obrigatório) |
| `icon` | string | — | Material Symbol (obrigatório) |
| `active` | bool | `false` | Estado ativo (página atual) |
| `href` | string | `#` | URL de destino |

#### Estado INATIVO

```
text-slate-600 dark:text-slate-400
hover:text-slate-900 dark:hover:text-white
hover:bg-slate-100 dark:hover:bg-white/5
```

#### Estado ATIVO

```
bg-brand-500/10 dark:bg-brand-500/20
text-brand-700 dark:text-brand-800
shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]
ring-1 ring-brand-500/30

+ Barra vertical de acento (4px wide, brand-500, com glow)
+ Gradiente horizontal from-brand-500/10 to-transparent
+ Ícone com animate-pulse
```

#### Acento vertical (ativo)

```html
<div class="absolute left-0 w-1.5 h-7 bg-brand-500 rounded-r-full
            shadow-[0_0_20px_rgba(59,130,246,0.8)] z-10">
</div>
```

#### Label do item

```
text-xs font-black tracking-tight uppercase
group-hover:translate-x-1 transition-transform duration-300
```

#### Exemplo de uso (Blade)

```blade
{{-- Item ativo --}}
<x-layouts.nav-item
    label="Dashboard"
    icon="dashboard"
    href="{{ route('dashboard') }}"
    :active="request()->routeIs('dashboard')"
/>

{{-- Item inativo --}}
<x-layouts.nav-item
    label="Relatórios"
    icon="bar_chart"
    href="{{ route('reports') }}"
/>
```

---

## 9. Sistema de Temas (Dark / Light)

### Como o tema funciona

O tema é controlado pela presença da classe `dark` no elemento `<html>`.

```html
<!-- Light mode (padrão) -->
<html lang="pt-BR">

<!-- Dark mode (Obsidian) -->
<html lang="pt-BR" class="dark">
```

### Persistência com localStorage

```javascript
// Verificar tema salvo ao carregar a página (adicione no <head> ANTES do CSS)
(function() {
    const theme = localStorage.getItem('theme') || 'light';
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();

// Função de toggle
function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}
```

### Botão de toggle de tema (referência)

```html
<button onclick="toggleTheme()"
        class="p-2 rounded-xl text-[var(--text-muted)] hover:text-[var(--text-main)]
               hover:bg-white/40 dark:hover:bg-white/10 transition-all"
        title="Alternar tema">
    <!-- Ícone light (visível no dark mode) -->
    <span class="material-symbols-outlined hidden dark:block">light_mode</span>
    <!-- Ícone dark (visível no light mode) -->
    <span class="material-symbols-outlined dark:hidden">dark_mode</span>
</button>
```

### Regra fundamental para o agente

**NUNCA use cores hardcoded (hex ou rgb) para textos, fundos e bordas.**
Sempre use as variáveis CSS do sistema:

```
✅ CORRETO:   class="text-[var(--text-main)] bg-[var(--bg-panel)]"
✅ CORRETO:   class="text-brand-500"  (token registrado no @theme)
❌ ERRADO:    class="text-[#0F172A] bg-white"
❌ ERRADO:    style="color: #0F172A"
```

---

## 10. Padrões de Composição

### 10.1 Card de Estatística (Dashboard)

```blade
<div class="group premium-card p-5">
    <div class="flex items-start justify-between mb-4">
        <x-ui.icon-box icon="people" color="emerald" />
        <x-ui.badge variant="success" :pulse="true">Ativo</x-ui.badge>
    </div>
    <p class="text-3xl font-black font-display tracking-tight text-[var(--text-main)]">
        1.284
    </p>
    <p class="text-xs text-[var(--text-muted)] mt-1">Usuários ativos hoje</p>
    <div class="mt-3 pt-3 border-t border-[var(--border-subtle)]">
        <x-ui.statistic label="vs ontem" value="+127" trend="+11%" trend-color="emerald" />
    </div>
</div>
```

### 10.2 Linha de Tabela / Lista com Avatar

```blade
<div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/40 dark:hover:bg-white/5
            transition-colors group">
    <x-ui.avatar name="Ana Lima" size="sm" status="online" />
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-[var(--text-main)] truncate">Ana Lima</p>
        <p class="text-xs text-[var(--text-muted)] truncate">ana@empresa.com</p>
    </div>
    <x-ui.badge variant="brand">Admin</x-ui.badge>
    <x-ui.dropdown align="right" width="48">
        <x-slot name="trigger">
            <x-ui.button variant="ghost" icon="more_vert" size="xs" />
        </x-slot>
        <x-slot name="content">
            <x-ui.dropdown-link icon="edit">Editar</x-ui.dropdown-link>
            <x-ui.dropdown-link icon="delete" class="text-rose-500">Remover</x-ui.dropdown-link>
        </x-slot>
    </x-ui.dropdown>
</div>
```

### 10.3 Formulário em Modal

```blade
<x-ui.modal name="create-user" max-width="lg" focusable>
    <div class="p-6 space-y-6">
        <!-- Header do modal -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold font-display text-[var(--text-main)]">
                    Novo Usuário
                </h2>
                <p class="text-sm text-[var(--text-muted)]">
                    Preencha os dados abaixo
                </p>
            </div>
            <x-ui.button variant="ghost" icon="close"
                         @click="$dispatch('close-modal', 'create-user')" />
        </div>

        <!-- Corpo do formulário -->
        <div class="space-y-4">
            <x-ui.input label="Nome Completo" icon="person" placeholder="João Silva"
                         wire:model="name" />
            <x-ui.input label="E-mail" type="email" icon="email" placeholder="joao@email.com"
                         wire:model="email" />
            <x-ui.toggle label="Enviar convite por e-mail"
                          description="O usuário receberá um link de acesso"
                          wire:model="sendInvite" />
        </div>

        <!-- Footer com ações -->
        <div class="flex gap-3 justify-end pt-2 border-t border-[var(--border-subtle)]">
            <x-ui.button variant="ghost" @click="$dispatch('close-modal', 'create-user')">
                Cancelar
            </x-ui.button>
            <x-ui.button variant="primary" icon="person_add" wire:click="createUser"
                          :loading="$processing">
                Criar Usuário
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
```

### 10.4 Grid de Dashboard 3 colunas

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
    <x-ui.card title="Receita Total" subtitle="Este mês" style="--glow-color: rgba(16,185,129,0.15)">
        <!-- conteúdo -->
    </x-ui.card>

    <x-ui.card title="Novos Usuários" subtitle="Últimas 24h">
        <!-- conteúdo -->
    </x-ui.card>

    <x-ui.card title="Taxa de Conversão" subtitle="Semana atual">
        <!-- conteúdo -->
    </x-ui.card>

    {{-- Card que ocupa 2 colunas --}}
    <x-ui.card title="Histórico de Atividade" subtitle="Últimos 30 dias" class="lg:col-span-2">
        <!-- gráfico ou tabela -->
    </x-ui.card>

    <x-ui.card title="Logs Recentes" subtitle="Tempo real" :glow="false">
        <div class="space-y-0.5">
            <x-ui.log-entry time="14:32" type="INFO"  message="Deploy concluído" />
            <x-ui.log-entry time="14:31" type="WARN"  message="Alta latência detectada" />
            <x-ui.log-entry time="14:29" type="ERROR" message="Timeout na API externa" />
        </div>
    </x-ui.card>
</div>
```

### 10.5 Header de seção com ação

```blade
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold font-display tracking-tight text-[var(--text-main)]">
            Usuários
        </h1>
        <p class="text-sm text-[var(--text-muted)] mt-0.5">
            Gerencie os membros da sua equipe
        </p>
    </div>
    <div class="flex items-center gap-3">
        <x-ui.button variant="secondary" icon="file_download" size="sm">
            Exportar
        </x-ui.button>
        <x-ui.button variant="primary" icon="person_add">
            Novo Usuário
        </x-ui.button>
    </div>
</div>
```

---

## 11. Regras de Ouro para o Agente

Estas são as regras mais importantes. Violá-las quebra a consistência visual do sistema.

### Regra 1 — Use sempre variáveis CSS para superfícies

```
✅ text-[var(--text-main)]       ❌ text-slate-900
✅ bg-[var(--bg-panel)]          ❌ bg-white
✅ border-[var(--border-glass)]  ❌ border-white/80
```

### Regra 2 — Use sempre `brand-*` para cores da marca

```
✅ text-brand-500    ❌ text-blue-500
✅ bg-brand-500/10   ❌ bg-blue-500/10
✅ ring-brand-500/30 ❌ ring-blue-500/30
```

### Regra 3 — Fontes têm papéis definidos

```
font-display → títulos, valores de métricas, logo (Space Grotesk)
font-sans    → todo o resto (Inter)
font-mono    → dados técnicos, logs, timestamps (JetBrains Mono)
```

### Regra 4 — Backdrop-blur é obrigatório em superfícies translúcidas

Se um elemento tem fundo com opacidade, **deve** ter `backdrop-blur-md` (ou similar).
Sem blur, o efeito de vidro não funciona.

```
✅ bg-white/40 backdrop-blur-md   ← correto
❌ bg-white/40                     ← fundo sem blur (efeito quebrado)
```

### Regra 5 — Hierarquia de z-index

```
z-10  → Conteúdo interno de cards (acima do glow layer)
z-30  → Header fixo
z-40  → Sidebar
z-50  → Dropdowns, tooltips
z-50  → Modal backdrop + painel
```

### Regra 6 — Bordas sempre translúcidas

Nunca use bordas sólidas em superfícies frosted. Use sempre com opacidade:

```
✅ border border-[var(--border-glass)]      ← variável contextual
✅ border border-white/10                   ← translúcida
✅ border border-brand-500/20               ← cor com opacidade
❌ border border-gray-200                   ← sólida (quebra o visual)
```

### Regra 7 — Danger sempre com confirmação

Botões e ações com `variant="danger"` ou ícone `delete` SEMPRE devem ter um passo
de confirmação intermediário (modal ou inline confirm). Nunca execute destruição diretamente.

### Regra 8 — Loading state em ações assíncronas

Qualquer botão que dispara operação async deve usar o prop `loading`:

```blade
<x-ui.button variant="primary" :loading="$isProcessing" wire:click="save">
    Salvar
</x-ui.button>
```

### Regra 9 — dark: sempre pareado com light

Ao escrever classes Tailwind manuais, sempre defina o par light/dark:

```
✅ bg-white/40 dark:bg-zinc-900/40
✅ text-slate-900 dark:text-slate-100
✅ border-slate-200/50 dark:border-white/10
```

### Regra 10 — Transition em todo elemento interativo

Qualquer elemento que muda de estado visualmente (hover, focus, checked):

```
transition-all duration-300   ← para mudanças compostas
transition-colors              ← apenas cor
```

---

## 12. Referência Rápida — Cheatsheet

### Cores semânticas (copie diretamente)

```html
<!-- Texto -->
<p class="text-[var(--text-main)]">Principal</p>
<p class="text-[var(--text-secondary)]">Secundário</p>
<p class="text-[var(--text-muted)]">Muted</p>

<!-- Backgrounds -->
<div class="bg-[var(--bg-surface)]">Página</div>
<div class="bg-[var(--bg-panel)] backdrop-blur-md">Painel</div>

<!-- Bordas -->
<div class="border border-[var(--border-glass)]">Glass border</div>
<div class="border border-[var(--border-subtle)]">Subtle border</div>
```

### Padrão de card manual (sem Blade component)

```html
<div class="premium-card p-5">
    <h3 class="text-sm font-bold font-display tracking-tight text-[var(--text-main)]">
        Título
    </h3>
    <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1">
        Subtítulo
    </p>
    <div class="mt-4">
        <!-- conteúdo -->
    </div>
</div>
```

### Padrão de label uppercase

```html
<p class="text-[9px] font-black uppercase tracking-[0.2em] text-[var(--text-muted)]">
    Seção
</p>
```

### Padrão de separador

```html
<div class="border-t border-[var(--border-subtle)] my-3"></div>
```

### Padrão de ícone Material Symbol

```html
<!-- Ícone padrão (outlined, 20px) -->
<span class="material-symbols-outlined text-[20px] text-[var(--text-muted)]">home</span>

<!-- Ícone colorido com marca -->
<span class="material-symbols-outlined text-[20px] text-brand-500">star</span>

<!-- Ícone de status -->
<span class="material-symbols-outlined text-[20px] text-emerald-500">check_circle</span>
<span class="material-symbols-outlined text-[20px] text-rose-500">error</span>
<span class="material-symbols-outlined text-[20px] text-amber-500">warning</span>
```

### Status de cor — decisão rápida

```
Positivo / Sucesso / Ativo    → emerald (verde)
Atenção / Pendente / Aviso    → amber (amarelo)
Negativo / Erro / Inativo     → rose (vermelho)
Informação / Neutro / Padrão  → brand (azul) ou slate (cinza)
```

### Tamanhos de fonte padrão do sistema

```
text-[9px]   → Labels de seção (caps)
text-[10px]  → Badges, subtítulos uppercase
text-[11px]  → Logs, mono text
text-xs      → Labels de input, texto secundário compacto
text-sm      → Corpo de texto padrão, títulos de card
text-base    → Botões lg, texto de destaque
text-xl      → Valores de métricas (com font-display)
text-3xl+    → Display numbers, heroes
```

---

*Crystalline Design System — versão 1.0*
*Para uso em projetos Laravel + Livewire + Tailwind CSS 4.x*
