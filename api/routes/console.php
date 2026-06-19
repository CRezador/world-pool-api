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

// A classificação é atualizada em tempo real pelo matches:import sempre que uma
// partida finaliza. Este agendamento diário é só um backstop barato para o caso de
// um "finished" ter sido perdido (ex: deploy/restart durante o fim de um jogo).
Schedule::command('standings:refresh')
    ->daily()
    ->withoutOverlapping();
