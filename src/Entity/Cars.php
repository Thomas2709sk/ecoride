<?php

namespace App\Entity;

use App\Repository\CarsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarsRepository::class)]
class Cars
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $brand = null;

    #[ORM\Column(length: 30)]
    private ?string $model = null;

    #[ORM\Column(length: 30)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $seats = null;

    #[ORM\Column(length: 15)]
    private ?string $plate_number = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $first_registration = null;

    #[ORM\Column(type: 'string', length: 15)]
    private ?string $energy = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Drivers $driver = null;

    /**
     * @var Collection<int, Carpools>
     */
    #[ORM\OneToMany(targetEntity: Carpools::class, mappedBy: 'car', orphanRemoval: true)]
    private Collection $carpools;

    public function __construct()
    {
        $this->carpools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;

        return $this;
    }

    public function getPlateNumber(): ?string
    {
        return $this->plate_number;
    }

    public function setPlateNumber(string $plate_number): static
    {
        $this->plate_number = $plate_number;

        return $this;
    }

    public function getFirstRegistration(): ?\DateTime
    {
        return $this->first_registration;
    }

    public function setFirstRegistration(\DateTime $first_registration): static
    {
        $this->first_registration = $first_registration;

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
            $carpool->setCar($this);
        }

        return $this;
    }

    public function removeCarpool(Carpools $carpool): static
    {
        if ($this->carpools->removeElement($carpool)) {
            // set the owning side to null (unless already changed)
            if ($carpool->getCar() === $this) {
                $carpool->setCar(null);
            }
        }

        return $this;
    }

    public function __toString(): string
{
    return $this->getBrand() . ' ' . $this->getModel();
}

    /**
     * Get the value of energy
     */ 
    public function getEnergy()
    {
        return $this->energy;
    }

    /**
     * Set the value of energy
     *
     * @return  self
     */ 
    public function setEnergy($energy)
    {
        $this->energy = $energy;

        return $this;
    }
}
