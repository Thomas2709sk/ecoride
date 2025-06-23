<?php

namespace App\Security\Voter;

use App\Entity\Reviews;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewsVoter extends Voter
{
    public const VALID = 'REV_EDIT';

    public const DELETE = 'REV_DELETE';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VALID, self::DELETE]) && $subject instanceof Reviews;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_ADMIN and STAFF can confirm or delete even if it's not the owner
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_STAFF')) {
            return true;
        }


        return false;
    }

}
