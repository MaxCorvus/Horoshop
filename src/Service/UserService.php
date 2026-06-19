<?php

declare(strict_types=1);

namespace App\Service;

class UserService
{
    public function __construct(
        private readonly \App\Repository\UserRepositoryInterface $userRepository,
        private readonly \App\Factory\UserFactory $userFactory,
        private readonly \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher,
        private readonly \Symfony\Component\Validator\Validator\ValidatorInterface $validator
    ) {
    }

    public function create(\App\DTO\UserRequestDTO $dto): \App\Entity\User
    {
        $user = $this->userFactory->createFromDto($dto);

        $this->validateEntity($user);

        $this->userRepository->create($user);

        return $user;
    }

    public function update(\App\Entity\User $user, \App\DTO\UserRequestDTO $dto): \App\Entity\User
    {
        $user->setLogin($dto->login);
        $user->setPhone($dto->phone);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);

        $this->validateEntity($user);

        $this->userRepository->save($user);

        return $user;
    }

    public function delete(\App\Entity\User $user): void
    {
        $this->userRepository->remove($user);
    }

    private function validateEntity(\App\Entity\User $user): void
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new \Symfony\Component\Validator\Exception\ValidationFailedException($user, $errors);
        }
    }
}
