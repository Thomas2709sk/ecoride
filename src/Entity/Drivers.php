<?php

namespace App\Entity;

use App\Repository\DriversRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriversRepository::class)]
class Drivers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $animals = null;

    #[ORM\Column]
    private ?bool $smoking = null;

    #[ORM\OneToOne(inversedBy: 'drivers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $preferences = null;

    /**
     * @var Collection<int, Cars>
     */
    #[ORM\OneToMany(targetEntity: Cars::class, mappedBy: 'driver',  cascade: ['persist', 'remove'])]
    private Collection $cars;

    /**
     * @var Collection<int, Carpools>
     */
    #[ORM\OneToMany(targetEntity: Carpools::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $carpools;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->carpools = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAnimals(): ?bool
    {
        return $this->animals;
    }

    public function setAnimals(bool $animals): static
    {
        $this->animals = $animals;

        return $this;
    }

    public function isSmoking(): ?bool
    {
        return $this->smoking;
    }

    public function setSmoking(bool $smoking): static
    {
        $this->smoking = $smoking;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPreferences(): ?string
    {
        return $this->preferences;
    }

    public function setPreferences(?string $preferences): static
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * @return Collection<int, Cars>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Cars $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setDriver($this);
        }

        return $this;
    }

    public function removeCar(Cars $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getDriver() === $this) {
                $car->setDriver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Carpools>
     */
    public function getCarpools(): Collection
    {
        return $this->carpools;
    }

    public function addCarpool(Carpools $carpool): static
    {
        if (!$this->carpools->contains($carpool)) {
            $this->carpools->add($carpool);
            $carpool->setDriver($this);
        }

        return $this;
    }

    public function removeCarpool(Carpools $carpool): static
    {
        if ($this->carpools->removeElement($carpool)) {
            // set the owning side to null (unless already changed)
            if ($carpool->getDriver() === $this) {
                $carpool->setDriver(null);
            }
        }

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
            $review->setDriver($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getDriver() === $this) {
                $review->setDriver(null);
            }
        }

        return $this;
    }
}
