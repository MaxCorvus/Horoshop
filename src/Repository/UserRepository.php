<?php

declare(strict_types=1);

namespace App\Repository;

class UserRepository implements UserRepositoryInterface
{
    private \Doctrine\ORM\EntityRepository $repository;

    public function __construct(
        private readonly \Doctrine\ORM\EntityManagerInterface $em,
    ) {
        $this->repository = $this->em->getRepository(\App\Entity\User::class);
    }

    #[\Override]
    public function create(\App\Entity\User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    #[\Override]
    public function save(\App\Entity\User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    #[\Override]
    public function remove(\App\Entity\User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
