# Deploy em produção (VPS)

Guia passo a passo para subir o Bolão Copa numa VPS **zerada** (Debian/Ubuntu),
para quem **não tem prática com Linux**. Siga na ordem. Cada bloco é um comando:
copie, cole no terminal e aperte Enter.

## Visão geral

```
Internet
   │
   ├── https://bolao.caiorezador.com ──► Caddy ──► front (SPA)
   │                                                 └─ /api,/sanctum (proxy interno) ──► api
   │
   └── https://api.caiorezador.com   ──► Caddy ──► api (acesso direto)
                                                     api/front ──► mysql
```

- **Caddy** cuida do HTTPS sozinho (certificado Let's Encrypt automático).
- Só o Caddy expõe portas (80/443). MySQL, API e front ficam numa rede interna.
- O SPA chama `/api` na **mesma origem** (`bolao.*`), então **não há CORS**.
- `api.caiorezador.com` é a API publicada para acesso direto (Postman, etc.).

---

## Pré-requisitos (antes de tocar na VPS)

1. **IP da VPS** (ex.: `203.0.113.10`) e a senha/chave de acesso `root`.
2. Acesso ao painel de **DNS** do domínio `caiorezador.com`.
3. O código no GitHub (este repositório).

---

## Passo 1 — Apontar o DNS (faça isto PRIMEIRO)

O Let's Encrypt só emite o certificado se os domínios já apontarem para a VPS.
No painel de DNS do `caiorezador.com`, crie **dois registros A** com o IP da VPS:

| Tipo | Nome    | Valor (conteúdo)   |
|------|---------|--------------------|
| A    | `bolao` | IP da VPS          |
| A    | `api`   | IP da VPS          |

> A propagação pode levar de alguns minutos a algumas horas. Para conferir, num
> terminal do seu PC: `ping bolao.caiorezador.com` deve responder o IP da VPS.

---

## Passo 2 — Entrar na VPS por SSH

No terminal do seu computador (no Windows use o PowerShell ou o terminal do WSL):

```bash
ssh root@SEU_IP_AQUI
```

Digite `yes` na primeira vez e informe a senha. Você está dentro da VPS.

---

## Passo 3 — Atualizar o sistema e criar um usuário (segurança básica)

Rodar tudo como `root` é arriscado. Vamos criar um usuário comum com `sudo`.

```bash
apt update && apt upgrade -y
adduser deploy                 # crie uma senha quando pedir; o resto pode deixar em branco
usermod -aG sudo deploy
```

Reconecte como esse usuário:

```bash
exit
ssh deploy@SEU_IP_AQUI
```

> Daqui pra frente, comandos administrativos usam `sudo` na frente.

---

## Passo 4 — Firewall (liberar só o necessário)

```bash
sudo apt install -y ufw
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
sudo ufw status
```

Deve liberar **22 (SSH), 80 e 443**. Mais nada precisa ficar aberto.

---

## Passo 5 — Instalar Docker

Usamos o script oficial da Docker (instala Docker Engine + Compose):

```bash
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker deploy
```

Saia e entre de novo para o seu usuário entrar no grupo `docker`:

```bash
exit
ssh deploy@SEU_IP_AQUI
docker --version          # confirma que instalou
docker compose version    # confirma o plugin compose
```

---

## Passo 6 — Baixar o código na VPS

```bash
sudo apt install -y git
git clone https://github.com/CRezador/world-pool-api.git
cd world-pool-api
```

> Se o repositório for **privado**, o `git` vai pedir usuário e senha. Use seu
> usuário do GitHub e, como senha, um **Personal Access Token** (Settings →
> Developer settings → Personal access tokens no GitHub).

---

## Passo 7 — Configurar as variáveis de ambiente (`.env.prod`)

```bash
cp .env.prod.example .env.prod
nano .env.prod
```

No editor `nano`, ajuste pelo menos:

- `FRONT_DOMAIN=bolao.caiorezador.com`
- `API_DOMAIN=api.caiorezador.com`
- `ACME_EMAIL=` seu e-mail real (o Let's Encrypt avisa sobre o certificado nele).
- `DB_PASSWORD=` e `MYSQL_ROOT_PASSWORD=` → **senhas fortes** (invente algo longo).
- `APP_KEY=` deixe **em branco por enquanto** (geramos no próximo passo).

Salvar e sair do `nano`: `Ctrl+O`, `Enter`, depois `Ctrl+X`.

### Gerar a APP_KEY

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm api php artisan key:generate --show
```

Copie a saída inteira (começa com `base64:...`), abra de novo `nano .env.prod` e
cole em `APP_KEY=base64:...`. Salve e saia.

---

## Passo 8 — Subir tudo

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml up -d --build
```

A primeira vez **demora** (baixa imagens, compila o front, instala o PHP). Quando
terminar, veja se está tudo de pé:

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
```

Acompanhe os logs (Ctrl+C sai do log, não derruba os serviços):

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml logs -f
```

> O Caddy tenta emitir o certificado HTTPS automaticamente. Se o DNS já propagou,
> em ~1 minuto os domínios respondem em `https://`. Se aparecer erro de
> certificado, quase sempre é DNS ainda não propagado — espere e tente acessar de
> novo no navegador.

---

## Passo 9 — Verificar

No navegador:

- `https://bolao.caiorezador.com` → deve abrir o app (cadeado de seguro).
- `https://api.caiorezador.com/up` → página de health-check do Laravel (status OK).

As migrations rodam sozinhas quando a API sobe (ver `api/docker/entrypoint.sh`).

---

## Operação do dia a dia

Sempre rode os comandos dentro da pasta `world-pool-api`. Para encurtar, você pode
criar um atalho (opcional):

```bash
echo "alias dcp='docker compose --env-file .env.prod -f docker-compose.prod.yml'" >> ~/.bashrc
source ~/.bashrc
```

Com o atalho `dcp`:

| Ação | Comando |
|------|---------|
| Ver status | `dcp ps` |
| Ver logs | `dcp logs -f` |
| Logs só da API | `dcp logs -f api` |
| Reiniciar | `dcp restart` |
| Parar tudo | `dcp down` |
| Subir de novo | `dcp up -d` |

### Atualizar para uma nova versão do código

```bash
cd ~/world-pool-api
git pull
dcp up -d --build
```

> **Variáveis novas:** `git pull` nunca altera o `.env.prod` (ele não é
> versionado). Se a nova versão introduzir variáveis de ambiente, adicione-as no
> `.env.prod` **antes** de subir. Para descobrir o que falta, compare com o
> exemplo:
>
> ```bash
> diff <(grep -oE '^[A-Z_]+=' .env.prod.example | sort) <(grep -oE '^[A-Z_]+=' .env.prod | sort)
> ```
>
> O que aparecer só do lado do `.env.prod.example` é variável nova a preencher.

### Rodar um comando artisan (ex.: limpar cache)

```bash
dcp exec api php artisan config:clear
```

---

## Backup do banco de dados

Gerar um dump (substitua a senha pela do `.env.prod`):

```bash
dcp exec mysql sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" pool_api' > backup-$(date +%F).sql
```

Restaurar:

```bash
cat backup-2026-06-09.sql | dcp exec -T mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" pool_api'
```

> Os dados do MySQL ficam no volume `mysql_data` e **sobrevivem** a `dcp down` /
> `up`. Só somem se você apagar o volume de propósito (`dcp down -v`).

---

## Problemas comuns

- **Certificado HTTPS não emite** → DNS ainda não aponta para a VPS, ou portas
  80/443 bloqueadas. Confira o Passo 1 e o Passo 4. Veja `dcp logs caddy`.
- **502 / Bad Gateway** → a API/front ainda está subindo ou caiu. `dcp ps` e
  `dcp logs api`.
- **Erro de login / 419 / CSRF** → confira no `.env.prod` se `FRONT_DOMAIN`,
  `SESSION_DOMAIN`, `SANCTUM_STATEFUL_DOMAINS` e `APP_URL` batem com
  `bolao.caiorezador.com`. Depois `dcp restart api`.
- **Mudei o `.env.prod` e nada mudou** → recrie os containers: `dcp up -d`.
