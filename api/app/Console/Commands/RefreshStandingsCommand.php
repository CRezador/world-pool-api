<?php

namespace App\Console\Commands;

use App\Services\StandingsServices\StandingsWriteService;
use Illuminate\Console\Command;

class RefreshStandingsCommand extends Command
{
    protected $signature = 'standings:refresh';
    protected $description = 'Atualiza a classificação da Copa do Mundo a partir da API football-data.org';

    public function __construct(private StandingsWriteService $standingsWriteService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $this->standingsWriteService->refresh();
            $this->info('✓ Classificação atualizada com sucesso.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Erro ao atualizar classificação: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
