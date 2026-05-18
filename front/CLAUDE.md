# Convenções do Frontend

## Stack

- Vue 3 + `<script setup lang="ts">` (Composition API)
- Vite + `@tailwindcss/vite` (Tailwind CSS v4)
- Vue Router v5
- Axios (`src/services/api.ts`)

---

## Design System

### Fontes

Importadas via Google Fonts em `src/style.css`. Configuradas em `@theme` como CSS custom properties.

| Classe Tailwind | Fonte            | Uso                                          |
|-----------------|------------------|----------------------------------------------|
| `font-display`  | Barlow Condensed | Títulos, placares, nomes de grupos, ranking  |
| `font-sans`     | Inter            | Todo o restante (padrão do `html`)           |

Pesos disponíveis:
- Barlow Condensed: `font-semibold` (600), `font-bold` (700), `font-extrabold` (800)
- Inter: `font-normal` (400), `font-medium` (500), `font-semibold` (600)

---

### Escala tipográfica

Definida em `@theme` em `src/style.css`. Usar sempre as classes abaixo — não usar as classes padrão do Tailwind (`text-xl`, `text-2xl`, etc.).

| Classe              | Tamanho | Fonte recomendada | Peso sugerido      | Uso típico                    |
|---------------------|---------|-------------------|--------------------|-------------------------------|
| `text-display-2xl`  | 56px    | `font-display`    | `font-extrabold`   | Hero, destaque máximo         |
| `text-display-xl`   | 40px    | `font-display`    | `font-bold`        | Título de página grande       |
| `text-display-lg`   | 32px    | `font-display`    | `font-bold`        | Título de página padrão       |
| `text-heading-md`   | 24px    | `font-display`    | `font-semibold`    | Seção, placar, nome de grupo  |
| `text-heading-sm`   | 20px    | `font-sans`       | `font-semibold`    | Card title                    |
| `text-body-lg`      | 18px    | `font-sans`       | `font-normal`      | Parágrafo largo               |
| `text-body-md`      | 16px    | `font-sans`       | `font-normal`      | Texto base                    |
| `text-body-sm`      | 14px    | `font-sans`       | `font-normal`      | Labels, meta, botões          |
| `text-caption`      | 12px    | `font-sans`       | `font-medium`      | Datas, tags, badges           |

---

### Paleta de cores

Definida em `@theme` em `src/style.css`. Usar sempre os tokens abaixo — não usar as paletas padrão do Tailwind (`green-*`, `orange-*`, `gray-*`).

#### Marca

Azul marinho — cor primária da Copa do Mundo 2026 (EUA, Canadá e México).

| Escala         | Uso                                              |
|----------------|--------------------------------------------------|
| `brand-50–950` | Azul Copa 2026 — cor primária do projeto         |
| `brand-900`    | Header, fundos de marca                          |
| `brand-700/800`| Textos de marca, hover de elementos de marca     |
| `brand-50/100` | Fundos suaves, item ativo na sidebar             |

#### Acento

Vermelho vivo — segunda cor oficial da Copa do Mundo 2026.

| Escala          | Uso                                         |
|-----------------|---------------------------------------------|
| `accent-50–950` | Vermelho — CTAs, ações principais, alertas  |
| `accent-600`    | Botão primário padrão                       |
| `accent-700`    | Hover do botão primário                     |
| `accent-300`    | Focus ring de inputs                        |

#### Verde Copa 2026

Verde vivo da identidade visual da Copa do Mundo 2026 — usado em ações secundárias.

| Escala        | Uso                                              |
|---------------|--------------------------------------------------|
| `copa-50–950` | Verde Copa 2026                                  |
| `copa-600`    | Botão secundário padrão                          |
| `copa-700`    | Hover do botão secundário                        |

#### Ouro

| Escala        | Uso                                           |
|---------------|-----------------------------------------------|
| `gold-50–950` | Âmbar — ranking, troféu, badges de vencedor   |
| `gold-500/600`| Ícones e texto de destaque de conquista       |

#### Apoio (Azul)

| Escala           | Uso                                       |
|------------------|-------------------------------------------|
| `support-50–950` | Links, info, elementos secundários        |

#### Neutros

| Escala            | Uso                                        |
|-------------------|--------------------------------------------|
| `neutral-50–950`  | Substitui `gray-*` em todo o projeto       |
| `neutral-50`      | Fundo de cards, superfícies light          |
| `neutral-100/200` | Bordas, divisores light                    |
| `neutral-400/500` | Texto muted, placeholders                  |
| `neutral-700`     | Texto secundário                           |
| `neutral-900/950` | Fundo dark                                 |

#### Status

Classes geradas a partir de variáveis single-value — sem escala de tons.

| Token      | Cor        | Uso                                   |
|------------|------------|---------------------------------------|
| `success`  | `#16a34a`  | Palpite correto, acerto               |
| `error`    | `#dc2626`  | Derrota, erro, campo inválido         |
| `warning`  | `#d97706`  | Atenção, prazo próximo                |
| `info`     | `#2563eb`  | Informação neutra                     |

Uso com opacidade: `bg-success/15`, `bg-error/10` — funciona normalmente.

---

### Tokens semânticos (dark/light automático)

Definidos em `@layer base` via `prefers-color-scheme`. Não geram classes Tailwind — usar via CSS quando precisar que um elemento mude automaticamente entre temas.

| Variável               | Light             | Dark               |
|------------------------|-------------------|--------------------|
| `--color-bg`           | `#ffffff`         | `neutral-950`      |
| `--color-surface`      | `neutral-50`      | `neutral-900`      |
| `--color-border`       | `neutral-200`     | `neutral-800`      |
| `--color-text`         | `neutral-900`     | `neutral-50`       |
| `--color-text-muted`   | `neutral-500`     | `neutral-400`      |

---

## Regras de estilo

- **Nunca usar** classes padrão de cor do Tailwind (`green-*`, `orange-*`, `gray-*`, `red-*` para erros) — usar os tokens do design system.
- **Nunca usar** classes de tamanho de texto padrão do Tailwind (`text-sm`, `text-lg`, `text-xl`) — usar a escala tipográfica acima.
- CSS solto não é permitido — toda estilização via Tailwind ou variáveis CSS do design system.
- O header é `fixed` — páginas não devem reservar espaço para ele no layout.

## Layout

- **Priorizar `flex` e `grid`** para toda estrutura de layout — linhas, colunas, alinhamento e distribuição de espaço.
- `position: absolute/fixed` é aceito apenas para overlays que precisam sair do fluxo do documento: dropdowns, tooltips, ícones sobrepostos a inputs, modais.
- Nunca usar `absolute` ou `margin: auto` para centralizar elementos que poderiam ser centrados com `flex`/`grid` no pai.
