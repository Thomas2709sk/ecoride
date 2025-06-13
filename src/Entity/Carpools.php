<?php

namespace App\Entity;

use App\Repository\CarpoolsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarpoolsRepository::class)]
class Carpools
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $day = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $begin = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $end = null;

    #[ORM\Column(length: 255)]
    private ?string $address_start = null;

    #[ORM\Column(length: 255)]
    private ?string $address_end = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $day_end = null;

    #[ORM\Column]
    private ?int $places_available = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: 'string', length: 100, columnDefinition: "ENUM('A venir', 'En cours', 'Terminé', 'Confirmé', 'Vérification par la plateforme') NOT NULL DEFAULT 'A venir'")]
    private $status = 'A venir';

    #[ORM\ManyToOne(inversedBy: 'carpools')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Drivers $driver = null;

    #[ORM\ManyToOne(inversedBy: 'carpools')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cars $car = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'carpools')]
    private Collection $user;

    #[ORM\Column(length: 255)]
    private ?string $carpool_number = null;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'carpool', orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?\DateTime
    {
        return $this->day;
    }

    public function setDay(\DateTime $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getBegin(): ?\DateTime
    {
        return $this->begin;
    }

    public function setBegin(\DateTime $begin): static
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getAddressStart(): ?string
    {
        return $this->address_start;
    }

    public function setAddressStart(string $address_start): static
    {
        $this->address_start = $address_start;

        return $this;
    }

    public function getAddressEnd(): ?string
    {
        return $this->address_end;
    }

    public function setAddressEnd(string $address_end): static
    {
        $this->address_end = $address_end;

        return $this;
    }

    public function getDayEnd(): ?\DateTime
    {
        return $this->day_end;
    }

    public function setDayEnd(\DateTime $day_end): static
    {
        $this->day_end = $day_end;

        return $this;
    }

    public function getPlacesAvailable(): ?int
    {
        return $this->places_available;
    }

    public function setPlacesAvailable(int $places_available): static
    {
        $this->places_available = $places_available;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDriver(): ?Drivers
    {
        return $this->driver;
    }

    public function setDriver(?Drivers $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function getCar(): ?Cars
    {
        return $this->car;
    }

    public function setCar(?Cars $car): static
    {
        $this->car = $car;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Users $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getCarpoolNumber(): ?string
    {
        return $this->carpool_number;
    }

    public function setCarpoolNumber(string $carpool_number): static
    {
        $this->carpool_number = $carpool_number;

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCarpool($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCarpool() === $this) {
                $review->setCarpool(null);
            }
        }

        return $this;
    }
}
