<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use App\Util\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    private ?string $name = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $classColor = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $classColorHover = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column]
    private ?int $ordering = 0;

    #[ORM\Column]
    private ?bool $isEnabled = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $pictureType = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'activities')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getClassColor(): ?string
    {
        return $this->classColor;
    }

    public function setClassColor(string $classColor): static
    {
        $this->classColor = $classColor;

        return $this;
    }

    public function getClassColorHover(): ?string
    {
        return $this->classColorHover;
    }

    public function setClassColorHover(string $classColorHover): static
    {
        $this->classColorHover = $classColorHover;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getOrdering(): ?int
    {
        return $this->ordering;
    }
    

    public function setOrdering(int $ordering): static
    {
        $this->ordering = $ordering;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getPictureType(): ?string
    {
        return $this->pictureType;
    }

    public function setPictureType(?string $pictureType): static
    {
        $this->pictureType = $pictureType;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addActivity($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeActivity($this);
        }

        return $this;
    }

    public function isActivityInUserControl(User $user): bool
    { 
        return $user->getActivities()->contains($this);
    }   
}
