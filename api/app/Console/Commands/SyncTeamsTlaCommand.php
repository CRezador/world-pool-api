<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SyncTeamsTlaCommand extends Command
{
    protected $signature = 'teams:sync-tla {--dry-run : Mostra o que seria alterado sem gravar}';
    protected $description = 'Popula a coluna tla dos times com o código usado pela football-data.org (preserva o code interno)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $response = Http::withHeader('X-Auth-Token', config('services.football_data.token'))
            ->get('https://api.football-data.org/v4/competitions/WC/standings');

        if (!$response->successful()) {
            $this->error("Falha ao buscar classificação da API: {$response->status()}");
            return Command::FAILURE;
        }

        // tla externa por grupo: ['A' => Collection<['tla','name']>, ...]
        $apiByGroup = collect($response->json('standings', []))
            ->filter(fn($s) => ($s['type'] ?? null) === 'TOTAL')
            ->mapWithKeys(function ($standing) {
                $group = str_replace('Group ', '', $standing['group'] ?? '');
                $teams = collect($standing['table'] ?? [])
                    ->map(fn($row) => [
                        'tla'  => $row['team']['tla'] ?? null,
                        'name' => $row['team']['shortName'] ?? $row['team']['name'] ?? '?',
                    ])
                    ->filter(fn($t) => $t['tla']);

                return [$group => $teams];
            });

        if ($apiByGroup->isEmpty()) {
            $this->error('A API não retornou classificação (standings vazio). Sem dados para sincronizar.');
            return Command::FAILURE;
        }

        $localByGroup = Team::with('group')->get()->groupBy(fn($t) => $t->group?->name);

        $updates  = [];          // [Team, novaTla]
        $warnings = [];

        foreach ($localByGroup as $letter => $localTeams) {
            $apiTeams = $apiByGroup->get($letter, collect());

            // 1ª passada: casa direto onde code == tla
            $apiByTla   = $apiTeams->keyBy('tla');
            $matchedTla = [];

            foreach ($localTeams as $team) {
                if ($apiByTla->has($team->code)) {
                    $updates[] = [$team, $team->code];
                    $matchedTla[$team->code] = true;
                }
            }

            // sobras dos dois lados, no mesmo grupo
            $remainingLocal = $localTeams->reject(fn($t) => isset($matchedTla[$t->code]))->values();
            $remainingApi   = $apiTeams->reject(fn($t) => isset($matchedTla[$t['tla']]))->values();

            // 2ª passada: se sobrou exatamente 1 de cada, é o divergente — pareia por eliminação
            if ($remainingLocal->count() === 1 && $remainingApi->count() === 1) {
                $updates[] = [$remainingLocal->first(), $remainingApi->first()['tla']];
                continue;
            }

            // qualquer ambiguidade restante: não arrisca, reporta para ajuste manual
            foreach ($remainingLocal as $team) {
                $opcoes = $remainingApi->pluck('tla')->implode(', ') ?: '(nenhuma)';
                $warnings[] = "Grupo {$letter}: '{$team->name}' (code {$team->code}) sem match único. Candidatas na API: {$opcoes}";
            }
        }

        // saída
        $changed = collect($updates)->filter(fn($u) => $u[0]->tla !== $u[1]);

        $this->table(
            ['Grupo', 'Time', 'code', 'tla atual', 'tla nova'],
            collect($updates)->map(fn($u) => [
                $u[0]->group?->name,
                $u[0]->name,
                $u[0]->code,
                $u[0]->tla ?? '—',
                $u[1] . ($u[0]->tla === $u[1] ? '' : '  ←'),
            ])->all()
        );

        foreach ($warnings as $w) {
            $this->warn("⚠ {$w}");
        }

        if ($dryRun) {
            $this->info("[dry-run] {$changed->count()} time(s) seriam atualizados. Nada foi gravado.");
            return $warnings ? Command::FAILURE : Command::SUCCESS;
        }

        foreach ($updates as [$team, $tla]) {
            if ($team->tla !== $tla) {
                $team->update(['tla' => $tla]);
            }
        }

        $this->info("✓ {$changed->count()} time(s) atualizados.");

        return $warnings ? Command::FAILURE : Command::SUCCESS;
    }
}
