<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Login is required')]
        #[Assert\Length(max: 8, maxMessage: 'Login cannot be longer than {{ limit }} characters')]
        public string $login = '',
        #[Assert\NotBlank(message: 'Phone is required')]
        #[Assert\Length(max: 8, maxMessage: 'Phone cannot be longer than {{ limit }} characters')]
        public string $phone = '',
        #[Assert\NotBlank(message: 'Password is required')]
        #[SerializedName('pass')]
        #[Assert\Length(max: 8, maxMessage: 'Password cannot be longer than {{ limit }} characters')]
        public string $password = '',
    ) {
    }
}
