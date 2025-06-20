<?php

namespace App\Security\Voter;

use App\Entity\Carpools;
use App\Entity\Users;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarpoolsVoter extends Voter
{
    public const CANCEL = 'CARPOOL_CANCEL';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CANCEL && $subject instanceof Carpools;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Carpools $carpool */
        $carpool = $subject;

        switch ($attribute) {
            case self::CANCEL:

                return $carpool->getUser()->contains($user);
        }

        return false;
    }
}