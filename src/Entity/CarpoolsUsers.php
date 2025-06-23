<?php

namespace App\Entity;

use App\Repository\CarpoolsUsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarpoolsUsersRepository::class)]
#[ORM\Table(name: "carpools_users")]
class CarpoolsUsers
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'carpoolsUsers')]
    #[ORM\JoinColumn(name: "users_id", referencedColumnName: "id", nullable: false)]
    private ?Users $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'carpoolsUsers')]
    #[ORM\JoinColumn(name: "carpools_id", referencedColumnName: "id", nullable: false)]
    private ?Carpools $carpool = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private ?bool $isConfirmed = false;

    #[ORM\Column]
    private ?bool $isEnded = false;

    #[ORM\Column]
    private ?bool $isAnswered = false;

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getCarpool(): ?Carpools
    {
        return $this->carpool;
    }

    public function setCarpool(?Carpools $carpool): static
    {
        $this->carpool = $carpool;
        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    public function isEnded(): ?bool
    {
        return $this->isEnded;
    }

    public function setIsEnded(bool $isEnded): static
    {
        $this->isEnded = $isEnded;

        return $this;
    }

    public function isAnswered(): ?bool
    {
        return $this->isAnswered;
    }

    public function setIsAnswered(bool $isAnswered): static
    {
        $this->isAnswered = $isAnswered;

        return $this;
    }
}