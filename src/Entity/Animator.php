<?php

namespace App\Entity;

use App\Repository\AnimatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimatorRepository::class)]
class Animator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $firstName = null;

    #[ORM\Column(length: 180)]
    private ?string $lastName = null;

    #[ORM\OneToOne(inversedBy: 'animator', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $picture = null;
    private $pictureFile;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isEnabled = true;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'animators')]
    private Collection $events;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getPictureFile()
    {
        return $this->pictureFile;
    }

    public function setPictureFile($pictureFile): self
    {
        $this->pictureFile = $pictureFile;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function getActivitiesUniqByName(): Collection
    {
        // Convertit la collection en tableau, trie le tableau par la propriété ordering
        $sortedEvents = $this->events->toArray();
        usort($sortedEvents, function (Event $a, Event $b) {
            return $a->getName() <=> $b->getName();
        });

        $activityNameArray = [];
        foreach ($sortedEvents as $event) {
            $activityNameArray [] = $event->getActivity()->getName();
        }
        return new ArrayCollection(array_unique($activityNameArray));
    }

    public function getActivitiesUniqByNameAndId(): Collection
    {
        // Convertit la collection en tableau, trie le tableau par le nom de l'activité
        $sortedEvents = $this->events->toArray();
        usort($sortedEvents, function (Event $a, Event $b) {
            return $a->getActivity()->getName() <=> $b->getActivity()->getName();
        });
    
        // Stockage temporaire pour éviter les doublons
        $uniqueActivities = [];
        $uniqueKeys = [];
    
        foreach ($sortedEvents as $event) {
            $activity = $event->getActivity();
            $key = $activity->getId(); // On utilise l'ID pour identifier les activités de façon unique
            if (!in_array($key, $uniqueKeys, true)) {
                $uniqueKeys[] = $key;
                $uniqueActivities[] = [
                    'name' => $activity->getName(),
                    'id' => $activity->getId(),
                ];
            }
        }
    
        // Retourne une Collection des activités uniques
        return new ArrayCollection($uniqueActivities);
    }
    

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addAnimator($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeAnimator($this);
        }

        return $this;
    }


    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

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

}
