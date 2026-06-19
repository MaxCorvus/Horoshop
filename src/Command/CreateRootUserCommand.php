<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:create-root',
    description: 'Creates the first root user'
)]
class CreateRootUserCommand extends \Symfony\Component\Console\Command\Command
{
    public function __construct(
        private readonly \App\Repository\UserRepositoryInterface $userRepository,
        private readonly \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output): int
    {
        $user = new \App\Entity\User('root', '00000000', 'rootpass');
        $user->setRootRole();

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'rootpass');
        $user->setPassword($hashedPassword);

        $this->userRepository->create($user);

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
