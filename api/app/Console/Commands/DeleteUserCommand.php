<?php

namespace App\Console\Commands;

use App\Repositories\UserRepositories\UserRepository;
use App\Services\UserServices\UserWriteService;
use Illuminate\Console\Command;

class DeleteUserCommand extends Command
{
    protected $signature = 'user:delete {email : E-mail do usuário a ser excluído}';
    protected $description = 'Exclui definitivamente um usuário (em cascata: pools próprias, participações e palpites)';

    public function __construct(
        private UserRepository $userRepository,
        private UserWriteService $userWriteService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->error("Usuário não encontrado para o e-mail: {$email}");
            return Command::FAILURE;
        }

        $this->warn("Excluir o usuário #{$user->id} ({$user->name} <{$user->email}>) também apaga, em cascata:");
        $this->line('  - pools das quais ele é dono (e os membros e palpites dessas pools)');
        $this->line('  - suas participações em pools');
        $this->line('  - seus palpites');

        if (!$this->confirm('Esta ação é irreversível. Deseja continuar?')) {
            $this->info('Operação cancelada.');
            return Command::SUCCESS;
        }

        $this->userWriteService->delete($user);
        $this->info("✓ Usuário {$email} excluído com sucesso.");

        return Command::SUCCESS;
    }
}
