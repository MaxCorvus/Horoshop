<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class UserResponseDTO
{
    public const string GROUP_POST = 'user:post';
    public const string GROUP_GET = 'user:get';
    public const string GROUP_PUT = 'user:put';

    public function __construct(
        #[Groups([self::GROUP_POST, self::GROUP_PUT])]
        public int $id,
        #[Groups([self::GROUP_POST, self::GROUP_GET])]
        public string $login,
        #[Groups([self::GROUP_POST, self::GROUP_GET])]
        public string $phone,
        #[SerializedName('pass')]
        #[Groups([self::GROUP_POST, self::GROUP_GET])]
        public string $password,
    ) {
    }

    public static function fromEntity(\App\Entity\User $user): self
    {
        return new self(
            $user->getId(),
            $user->getLogin(),
            $user->getPhone(),
            $user->getPassword(),
        );
    }
}
