<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

/**
 * @extends \Symfony\Component\Security\Core\Authorization\Voter\Voter<string, User>
 */
class UserVoter extends \Symfony\Component\Security\Core\Authorization\Voter\Voter
{
    public const string VIEW = 'USER_VIEW';
    public const string EDIT = 'USER_EDIT';
    public const string DELETE = 'USER_DELETE';

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof User;
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        if ($this->isRoot($currentUser)) {
            return true;
        }

        /** @var \App\Entity\User $subject */
        return match ($attribute) {
            self::VIEW, self::EDIT => $currentUser->getId() === $subject->getId(),
            self::DELETE => false,
            default => false,
        };
    }

    private function isRoot(User $user): bool
    {
        return in_array(User::ROLE_ROOT, $user->getRoles(), true);
    }
}
