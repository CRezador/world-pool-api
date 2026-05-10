# Convenções da API Laravel

## Estrutura de Services

Services são separados por contexto dentro de um subdiretório por módulo:

```
Services/
└── {Módulo}Services/
    ├── {Módulo}WriteService.php    → create, update, delete
    ├── {Módulo}ReadService.php     → métodos de consulta/listagem
    └── {Módulo}*Service.php        → serviços de domínio específico (ex: ScoringService)
```

**Regras:**
- Cada service deve ter uma responsabilidade única e coesa
- Não criar `BaseService` para compartilhar construtores — dependências diferentes indicam services diferentes
- Validações de domínio reutilizadas entre services (ex: `assertScheduled`) ficam no Repository correspondente, não no service

## Estrutura de Repositories

Repositories fazem acesso a dados e podem conter validações de estado que lançam exceções de negócio quando reutilizadas por múltiplos services:

```php
// Exemplo: MatchRepository
public function assertScheduled(int $matchId): void
{
    if ($this->getStatusById($matchId) !== MatchStatus::SCHEDULED) {
        throw new \Exception('...', 400);
    }
}
```

## Injeção de dependência no Controller

O Controller injeta cada service separadamente via construtor — o Laravel resolve automaticamente:

```php
public function __construct(
    private GuessWriteService $writeService,
    private GuessReadService $readService,
    private GuessScoringService $scoringService,
) {}
```

## Helpers privados nos Services

Métodos privados de guarda (`findOrFail`, `assertOwnership`) ficam no service que os usa — não extrair para traits salvo quando compartilhados entre múltiplos services:

```php
private function findGuessOrFail(int $guessId): Guess { ... }   // 404
private function assertOwnership(Guess $guess, int $userId): void { ... }  // 403
```
