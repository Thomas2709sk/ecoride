<?php

namespace App\Security\Voter;

use App\Entity\Cars;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarsVoter extends Voter
{
    public const EDIT = 'CARS_EDIT';

    public const DELETE = 'CARS_DELETE';

        public function __construct(private readonly Security $security)
    {
        
    }

        protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Cars;
    }

        protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if(!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_ADMIN can edit or delete even if it's not the owner
        if($this->security->isGranted('ROLE_ADMIN')) return true;

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                // Verify is User own the car
                return $this->isOwner($subject, $user);

        }
        return false;
    }

        private function isOwner(Cars $car, $user): bool
    {
        return $user === $car->getDriver()->getUser(); // Veridy if the driver associate to the car is the logged User
        return $user === $car->getUsers();
    }
}