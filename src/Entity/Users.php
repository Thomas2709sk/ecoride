<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity('pseudo', message: 'Ce pseudo est déjà utilisé')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y à déjà un compte avec cet email')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'adresse e-mail est obligatoire')]
    #[Assert\Email(
        message: 'L\'adresse e-mail {{ value }} n\'est pas au bon format'
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
     #[Assert\Length(
        min: 8,
        minMessage: 'Minimum {{ limit }} caractères',
    )]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Le pseudo est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Minimum {{ limit }} caractères',
        maxMessage: 'Maximum {{ limit }} caractères'
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?int $credits = 20;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column]
    private ?bool $isPassenger = true;

    #[ORM\OneToOne(mappedBy: 'user')]
    private ?Drivers $drivers = null;

     /**
      * @var Collection<int, Carpools>
      */
     #[ORM\ManyToMany(targetEntity: Carpools::class, mappedBy: 'user')]
     private Collection $carpools;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'user')]
    private Collection $reviews;

    /**
     * @var Collection<int, CarpoolsUsers>
     */
    #[ORM\OneToMany(targetEntity: CarpoolsUsers::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $carpoolsUsers;

    public function __construct()
    {
        $this->carpools = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->carpoolsUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getDrivers(): ?Drivers
    {
        return $this->drivers;
    }

    public function setDrivers(Drivers $drivers): static
    {
        // set the owning side of the relation if necessary
        if ($drivers->getUser() !== $this) {
            $drivers->setUser($this);
        }

        $this->drivers = $drivers;

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
             $carpool->addUser($this);
         }

         return $this;
     }

     public function removeCarpool(Carpools $carpool): static
     {
         if ($this->carpools->removeElement($carpool)) {
             $carpool->removeUser($this);
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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of isPassenger
     */ 
    public function isPassenger()
    {
        return $this->isPassenger;
    }

    /**
     * Set the value of isPassenger
     *
     * @return  self
     */ 
    public function setIsPassenger($isPassenger)
    {
        $this->isPassenger = $isPassenger;

        return $this;
    }

    public function getDisplayedRole(): string
{
    if (in_array('ROLE_DRIVER', $this->getRoles(), true)) {
        return $this->isPassenger() ? 'chauffeur/passager' : 'chauffeur';
    }
    return 'passager';
}

    /**
     * @return Collection<int, CarpoolsUsers>
     */
    public function getCarpoolsUsers(): Collection
    {
        return $this->carpoolsUsers;
    }

    public function addCarpoolsUser(CarpoolsUsers $carpoolsUser): static
    {
        if (!$this->carpoolsUsers->contains($carpoolsUser)) {
            $this->carpoolsUsers->add($carpoolsUser);
            $carpoolsUser->setUser($this);
        }

        return $this;
    }

    public function removeCarpoolsUser(CarpoolsUsers $carpoolsUser): static
    {
        if ($this->carpoolsUsers->removeElement($carpoolsUser)) {
            // set the owning side to null (unless already changed)
            if ($carpoolsUser->getUser() === $this) {
                $carpoolsUser->setUser(null);
            }
        }

        return $this;
    }
}
