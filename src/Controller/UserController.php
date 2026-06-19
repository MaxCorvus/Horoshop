<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/v1/api/users', format: 'json')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly \App\Service\UserService $userService,
    ) {
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted(\App\Entity\User::ROLE_ROOT)]
    public function create(
        #[MapRequestPayload] \App\DTO\UserRequestDTO $dto
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        $user = $this->userService->create($dto);

        return $this->json(
            \App\DTO\UserResponseDTO::fromEntity($user),
            \Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
            [],
            ['groups' => \App\DTO\UserResponseDTO::GROUP_POST]
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted(\App\Security\UserVoter::VIEW, subject: 'user')]
    public function get(
        \App\Entity\User $user
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        return $this->json(
            \App\DTO\UserResponseDTO::fromEntity($user),
            \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            [],
            ['groups' => \App\DTO\UserResponseDTO::GROUP_GET]
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[IsGranted(\App\Security\UserVoter::EDIT, subject: 'user')]
    public function update(
        \App\Entity\User $user,
        #[MapRequestPayload] \App\DTO\UserRequestDTO $dto
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        $user = $this->userService->update($user, $dto);

        return $this->json(
            \App\DTO\UserResponseDTO::fromEntity($user),
            \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            [],
            ['groups' => \App\DTO\UserResponseDTO::GROUP_PUT],
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted(\App\Security\UserVoter::DELETE, subject: 'user')]
    public function delete(
        \App\Entity\User $user
    ): \Symfony\Component\HttpFoundation\Response {
        $this->userService->delete($user);

        return new \Symfony\Component\HttpFoundation\Response(
            null,
            \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT
        );
    }
}
