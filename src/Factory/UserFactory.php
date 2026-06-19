<?php

declare(strict_types=1);

namespace App\Factory;

class UserFactory
{
    public function __construct(
        private readonly \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createFromDto(\App\DTO\UserRequestDTO $dto): \App\Entity\User
    {
        $user = new \App\Entity\User(
            $dto->login,
            $dto->phone,
            $dto->password
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);

        $user->setPassword($hashedPassword);

        return $user;
    }
}
