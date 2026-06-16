<?php

namespace App\Console\Commands;

use App\Repositories\UserRepositories\UserRepository;
use App\Services\UserServices\UserWriteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ChangeUserEmailCommand extends Command
{
    protected $signature = 'user:change-email {email : E-mail atual do usuário} {new-email : Novo e-mail}';
    protected $description = 'Altera o e-mail de um usuário';

    public function __construct(
        private UserRepository $userRepository,
        private UserWriteService $userWriteService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->argument('email');
        $newEmail = $this->argument('new-email');

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->error("Usuário não encontrado para o e-mail: {$email}");
            return Command::FAILURE;
        }

        $validator = Validator::make(
            ['email' => $newEmail],
            ['email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}"]],
            [
                'email.email' => 'Novo e-mail inválido.',
                'email.unique' => 'Novo e-mail já está em uso.',
            ],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first('email'));
            return Command::FAILURE;
        }

        $this->userWriteService->update($user, ['email' => $newEmail]);
        $this->info("✓ E-mail do usuário #{$user->id} alterado de {$email} para {$newEmail}.");

        return Command::SUCCESS;
    }
}
