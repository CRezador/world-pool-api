<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Importa/atualiza partidas da Copa a cada 5 minutos. withoutOverlapping evita
// que uma execução lenta (API fora do ar, banco travado) acumule processos.
Schedule::command('matches:import')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// A classificação só muda quando partidas terminam, então uma vez por dia basta.
Schedule::command('standings:refresh')
    ->daily()
    ->withoutOverlapping();
