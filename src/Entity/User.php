<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['login'], message: 'Login already exists')]
class User implements \Symfony\Component\Security\Core\User\UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_ROOT = 'ROLE_ROOT';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'login', type: Types::STRING, length: 8, unique: true, nullable: false)]
    private string $login;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 8, nullable: false)]
    private string $phone;

    #[ORM\Column(name: 'password', type: Types::STRING, nullable: false)]
    private string $password;

    #[ORM\Column(name: 'roles', type: Types::JSON)]
    private array $roles;

    public function __construct(
        string $login,
        string $phone,
        string $password,
        array $roles = [self::ROLE_USER]
    ) {
        $this->login = $login;
        $this->phone = $phone;
        $this->password = $password;
        $this->roles = empty($roles) ? [self::ROLE_USER] : $roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Override]
    public function getRoles(): array
    {
        return array_values(array_unique($this->roles));
    }

    public function setRootRole(): static
    {
        $this->roles = [self::ROLE_ROOT];

        return $this;
    }

    public function setUserRole(): static
    {
        $this->roles = [self::ROLE_USER];

        return $this;
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->login;
    }
}
