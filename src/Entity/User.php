<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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
    private ?string $password = null;

    #[Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,32})/")]
    private string $plainPassword;

    #[Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,32})/")]
    private string $plainPasswordRepeat;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\ManyToMany(targetEntity: Activity::class, inversedBy: 'users')]
    private Collection $activities;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Animator $animator = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'user')]
    private Collection $events;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     *
     * @return list<string>
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
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPasswordRepeat(): ?string
    {
        return $this->plainPasswordRepeat;
    }

    public function setPlainPasswordRepeat(?string $plainPasswordRepeat): self
    {
        $this->plainPasswordRepeat = $plainPasswordRepeat;

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

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    /**
     * @return Collection<int, Activity>
    */
    public function getActivitiesByOrdering(): Collection
    {
        // Convertit la collection en tableau, trie le tableau par la propriété ordering
        $sortedActivities = $this->activities->toArray();
        usort($sortedActivities, function (Activity $a, Activity $b) {
            return $a->getOrdering() <=> $b->getOrdering();
        });

        return new ArrayCollection($sortedActivities);
    }

    public function getActivitiesByName(): Collection
    {
        // Convertit la collection en tableau, trie le tableau par la propriété ordering
        $sortedActivities = $this->activities->toArray();
        usort($sortedActivities, function (Activity $a, Activity $b) {
            return $a->getName() <=> $b->getName();
        });

        return new ArrayCollection($sortedActivities);
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        $this->activities->removeElement($activity);

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCompleteNameByfirstName(): ?string
    {
        return ucfirst($this->firstName).' '.strtoupper($this->lastName) ;
    }

    public function getCompleteNameByLastName(): ?string
    {
        return strtoupper($this->lastName).' '.ucfirst($this->firstName) ;
    }

    public function getAnimator(): ?Animator
    {
        return $this->animator;
    }

    public function setAnimator(?Animator $animator): static
    {
        // unset the owning side of the relation if necessary
        if ($animator === null && $this->animator !== null) {
            $this->animator->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($animator !== null && $animator->getUser() !== $this) {
            $animator->setUser($this);
        }

        $this->animator = $animator;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }
}
