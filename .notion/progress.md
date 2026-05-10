# CLAUDE.md — Progresso Semanal

Arquivo de anotações semanais de progresso. Serve também como instrução para a IA sobre como registrar novos pontos.

---

## Como a IA deve agir

### Quando o usuário quiser adicionar um ponto de progresso

Exemplos de gatilho: "adiciona X ao progresso", "anota que fiz X", "registra que terminei X".

1. Identificar se é um **Destaque** (decisão técnica, escolha de abordagem, uso de IA, motivo por trás de algo) ou **Progresso** (o que foi feito, entregável concreto). Em caso de dúvida, perguntar.
2. Adicionar o ponto na semana correta — sempre a semana atual, salvo instrução contrária.
3. Atualizar este arquivo local.
4. Atualizar a página no Notion (ID: `35cbd004-7185-814e-a958-c70ba540d48d`) com o mesmo conteúdo, usando a tool `notion-update-page` com `update_content`.

### Quando o usuário quiser criar uma nova semana

Semanas vão de **quarta a terça**. A IA deve calcular as datas corretas com base na data atual e inserir o novo bloco `<details>` **antes** da semana anterior, mantendo ordem cronológica decrescente (mais recente no topo).

Ao criar a semana, replicar a mesma estrutura no Notion com um novo toggle `## DD/MM a DD/MM/AAAA {toggle="true"}`.

### Regras gerais
- Sempre atualizar os dois lugares: arquivo local **e** Notion.
- Não reformatar o arquivo inteiro — usar edições pontuais para não perder histórico.
- Não alterar semanas passadas a menos que o usuário peça explicitamente.
- Manter o estilo simples: sem markdown excessivo, sem emojis, sem frescura.

---

## Referências

| Recurso | Valor |
|---|---|
| Notion — página de progresso | https://www.notion.so/35cbd0047185814ea958c70ba540d48d |
| Notion — página ID | `35cbd004-7185-814e-a958-c70ba540d48d` |
| Ciclo semanal | Quarta a terça |

---

## Progresso

<details>
<summary><strong>Semana atual — 06/05 a 12/05/2026</strong></summary>

### Destaques
- Documentei as rotas da API com OpenAPI/Swagger. Usei IA para isso pois não achei interessante focar 100% nisso agora, mas queria ter a documentação organizada.

### Progresso
- Criação da documentação OpenAPI das rotas existentes (BOL-25)
- Ajuste nas collections que estavam com variáveis faltando
- Alinhamento da versão do PHP no CI com o Docker (8.4)
- Levantamento e definição das regras de negócio do módulo de Palpites (Guess)

</details>

---

<details>
<summary><strong>29/04 a 05/05/2026</strong></summary>

### Destaques

### Progresso
- Revisão do módulo PoolMember

</details>

---

<details>
<summary><strong>Semanas anteriores</strong></summary>

### Destaques
- Escolha da stack: Laravel (API only) + Vue 3 + TypeScript.
- Docker customizado sem Laravel Sail para ter mais controle da infraestrutura.
- Autenticação com Sanctum usando token pessoal (sem cookie/CSRF).
- Transformers customizados no lugar de API Resources.

### Progresso
- Setup do Docker (app, nginx, db)
- Modelo de dados: groups, teams, matches, users, pools, pool_members, guesses, leaderboard
- Autenticação (registro, login, logout)
- CRUD de bolões (pools)
- Rotas de membros do bolão (PoolMember)
- Rotas de partidas (matches)

</details>
