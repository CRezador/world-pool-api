# Scripts do Composer (Docker)

## Infra
- `composer d:up`      → sobe os containers (build + up em background)
- `composer d:down`    → derruba os containers
- `composer d:logs`    → acompanha logs (todos serviços)

## Laravel (Artisan)
- `composer d:artisan -- <cmd>` → roda qualquer comando do artisan no container
  - exemplo: `composer d:artisan -- route:list`
- `composer d:migrate` → roda migrations
- `composer d:fresh`   → dropa e recria tudo (migrate:fresh)
- `composer d:seed`    → roda DatabaseSeeder (db:seed)
- `composer d:seeder <Name>` → cria um seeder (make:seeder)
- `composer d:tinker`  → abre o tinker (interativo)

## Composer (dentro do container)
- `composer d:composer -- <cmd>` → roda composer dentro do container
  - exemplo: `composer d:composer -- install`