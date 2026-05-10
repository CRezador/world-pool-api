# CLAUDE.md — Integração Jira

Subprojeto que descreve **como o Claude vincula o trabalho deste repositório ao Jira**. Complementa o `CLAUDE.md` da raiz, não substitui.

---

## 🎯 Escopo

Tudo que envolve issues, branches, commits, PRs e atualização de status. Antes de codar qualquer coisa, o Claude deve confirmar a existência de uma issue Jira correspondente (ou criar uma).

---

## 🔌 Configuração

| Item | Variável de ambiente |
|---|---|
| Site | `$JIRA_SITE` |
| Cloud ID | `$JIRA_CLOUD_ID` |
| Projeto | `$JIRA_PROJECT_NAME` |
| Chave do projeto | `$JIRA_PROJECT_KEY` |
| URL de uma issue | `https://$JIRA_SITE/browse/$JIRA_PROJECT_KEY-{n}` |

Valores reais em `.env` na raiz do repositório (não commitado). Exemplo em `.env.example`.

Acesso: via **MCP Atlassian Rovo** (já conectado). Usar tools `Atlassian Rovo:*`. Nunca passar credenciais — o token é resolvido pelo MCP.

---

## 📋 Tipos de issue disponíveis

| Tipo | Quando usar |
|---|---|
| **Epic** | Agrupa várias histórias/tarefas (ex.: "Sistema de palpites"). |
| **Feature** | Funcionalidade ampla. |
| **História** | Demanda voltada ao usuário ("Como usuário, quero..."). |
| **Tarefa** | Trabalho técnico distinto (refactor, setup, infra). |
| **Bug** | Defeito ou comportamento incorreto. |
| **Subtask** | Subdivisão de uma tarefa/história. |
| **Request** | Pedido de design ou outro suporte. |

Em caso de dúvida entre **História** e **Tarefa**: se o entregável é visível ao usuário final, é **História**; se é técnico/interno, é **Tarefa**.

> ⚠️ **Atenção ao criar Subtasks via API:** o `issueTypeName` deve ser `"Subtask"` (inglês), mesmo que a UI do Jira exiba "Subtarefa". Usar "Subtarefa" retorna erro `"Especifique algum tipo de item válido"`. O mesmo vale para outros tipos: sempre usar o `untranslatedName` — `"Task"` não, usar `"Tarefa"`; `"Story"` não, usar `"História"` — conforme mapeado na tabela acima. Em caso de dúvida, consultar `getJiraProjectIssueTypesMetadata` e usar o campo `name` (não `untranslatedName`).

---

## 🔄 Workflow / Status

Status reais do projeto (descobertos via `getTransitionsForJiraIssue`):

```
Tarefas pendentes  →  Em andamento  →  Em análise  →  Concluído
   (To Do)            (In Progress)      (In Review)      (Done)
```

### IDs de transição (para uso programático)

| De → Para | Transition ID | Nome no Jira |
|---|---|---|
| Qualquer → To Do | `11` | Itens Pendentes |
| Qualquer → In Progress | `21` | Em andamento |
| Qualquer → In Review | `31` | In Review |
| Qualquer → Done | `41` | Itens concluídos |

> Todas as transições são **globais** (qualquer estado pode ir pra qualquer estado). Mesmo assim, respeitar a ordem natural do fluxo.

### Quando atualizar o status
- **Começou a codar** → mover para **Em andamento**.
- **Abriu PR** → mover para **Em análise**.
- **PR mergeado em `main`/`develop`** → mover para **Concluído**.
- Caso reabra a tarefa por bug encontrado → voltar para **Em andamento** com comentário explicando.

---

## 🌿 Convenção de branches

Padrão obrigatório:

```
BOL-{numero}-{descricao-curta-em-kebab-case}
```

Exemplos:
- `BOL-23-criar-endpoint-de-palpites`
- `BOL-41-fix-validacao-stage-enum`
- `BOL-7-setup-docker-mysql`

Regras:
- Sempre prefixado com a chave da issue (`BOL-{n}`).
- Descrição em **kebab-case**, sem acentos, no máximo ~60 caracteres.
- Branch saindo de `main` (ou `develop`, se existir).
- Uma branch por issue. Se a issue está grande demais para uma branch só, **quebrar em subtasks** antes.

---

## 💬 Convenção de commits

Padrão **Conventional Commits + chave Jira no escopo**:

```
<tipo>(BOL-{numero}): descrição curta no imperativo
```

Tipos aceitos: `feat`, `fix`, `refactor`, `chore`, `docs`, `test`, `style`, `perf`, `build`, `ci`.

Exemplos:
- `feat(BOL-23): adiciona endpoint POST /pools/{id}/guesses`
- `fix(BOL-41): corrige validação do enum stage em MatchRequest`
- `refactor(BOL-15): extrai PoolMembershipService de PoolService`
- `docs(BOL-2): atualiza README com instruções Docker`

Regras:
- Descrição em **português**, no **imperativo** ("adiciona", "corrige"), **sem ponto final**.
- Linha de assunto até **72 caracteres**.
- Corpo opcional explicando o **porquê** (não o "o quê", que o diff já mostra).
- Um commit pode referenciar mais de uma issue se necessário: `feat(BOL-23, BOL-24): ...` — mas é sinal de que talvez devesse ter sido uma issue só.

---

## 📥 Pull Requests

Título do PR segue o mesmo padrão dos commits:

```
feat(BOL-23): adiciona endpoint de palpites
```

Descrição do PR deve conter, no mínimo:

```markdown
## Issue
[BOL-23](https://caiorezador.atlassian.net/browse/BOL-23)

## O que foi feito
- ...

## Como testar
- ...

## Checklist
- [ ] PHPCS passa (backend)
- [ ] ESLint passa (frontend)
- [ ] Migrations rodam do zero (`migrate:fresh --seed`)
- [ ] Issue movida para **Em análise**
```

---

## 🤖 Como o Claude deve atuar

### Antes de começar qualquer trabalho
1. Perguntar: **"Existe uma issue Jira pra isso?"**
2. Se **sim** → buscar com `Atlassian Rovo:getJiraIssue` e ler título, descrição, comentários e status.
3. Se **não** → ofertar a criação da issue **antes** de codar, com tipo apropriado (Tarefa, Bug, História...).
4. Confirmar que entendeu o escopo antes de gerar código.

### Durante o desenvolvimento
- Sugerir o **nome da branch** seguindo o padrão `BOL-{n}-...`.
- Sugerir as **mensagens de commit** já no formato `tipo(BOL-{n}): ...`.
- Ao iniciar codificação, **ofertar mover a issue para "Em andamento"** (transição ID `21`).
- Se identificar problema fora do escopo da issue atual, **não corrigir junto** — sugerir abrir nova issue (Bug ou Tarefa) para tratar separado.

### Em revisão de código
- Sempre referenciar a issue: "Sobre a `BOL-23`, esse trecho aqui...".
- Ao apontar pontos de melhoria, perguntar: *"isso entra no escopo desta issue ou abrimos uma nova?"*.
- Se algo não estiver na issue mas for crítico (bug, segurança), **abrir Bug imediatamente** com vínculo via `createIssueLink` (tipo `Relates` ou `Blocks` conforme o caso).

### Ao finalizar
- Sugerir o título e descrição do PR.
- Ofertar mover a issue para **Em análise** (transição ID `31`).
- Após merge, ofertar mover para **Concluído** (transição ID `41`).

### O que **nunca** fazer sem confirmação
- ❌ Mover status de issue automaticamente.
- ❌ Criar issue sem perguntar.
- ❌ Criar links entre issues sem confirmar relação.
- ❌ Fechar issue (transição para `Concluído`) sem confirmação explícita.
- ❌ Comentar na issue em nome do usuário sem ele aprovar o conteúdo.

---

## 🛠️ Tools MCP relevantes

Sempre carregadas via `tool_search` antes do uso. Nomes que vão aparecer:

| Ação | Tool |
|---|---|
| Buscar issues | `Atlassian Rovo:searchJiraIssuesUsingJql` |
| Ler uma issue | `Atlassian Rovo:getJiraIssue` |
| Criar issue | `Atlassian Rovo:createJiraIssue` |
| Editar issue | `Atlassian Rovo:editJiraIssue` |
| Comentar | `Atlassian Rovo:addCommentToJiraIssue` |
| Listar transições | `Atlassian Rovo:getTransitionsForJiraIssue` |
| Mover status | `Atlassian Rovo:transitionJiraIssue` |
| Vincular issues | `Atlassian Rovo:createIssueLink` |
| Tipos de link | `Atlassian Rovo:getIssueLinkTypes` |
| Buscar geral (Rovo) | `Atlassian Rovo:search` |

### Templates de JQL úteis
```
# Minhas issues abertas
project = BOL AND assignee = currentUser() AND statusCategory != Done

# Issues em revisão
project = BOL AND status = "Em análise"

# Bugs abertos
project = BOL AND issuetype = Bug AND statusCategory != Done ORDER BY created DESC

# Issues criadas na última semana
project = BOL AND created >= -7d ORDER BY created DESC
```

---

## ❓ Perguntas que o Claude deve fazer ao desenvolvedor

Antes de criar issue:
- "Isso é uma **História** (visível ao usuário) ou **Tarefa** (técnico)?"
- "É um **Bug** ou só um ajuste? Se é bug, qual o passo pra reproduzir?"
- "Faz parte de algum **Epic** existente? Se sim, qual?"
- "Qual a prioridade? Bloqueia outra coisa?"

Antes de mover status:
- "Já abriu o PR? Posso mover pra **Em análise**?"
- "PR foi mergeado? Posso fechar a issue?"

Em revisão:
- "Esse problema que encontrei está fora do escopo da `BOL-X`. Abro uma nova issue ou trato aqui mesmo?"
- "Essa refatoração depende de outra coisa? Vale criar um link `Blocks`?"
