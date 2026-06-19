<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    public function create(\App\Entity\User $user): void;
    public function save(\App\Entity\User $user): void;
    public function remove(\App\Entity\User $user): void;
}
