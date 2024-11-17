<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Util\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $preparationInfos = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $eventDistance = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $rdvPlaceName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $rdvLatitude = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $rdvLongitude = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeStartAt = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeEndAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(null,"La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: "La date de début est obligatoire toto"
    )]
    private ?\DateTimeInterface $dateStartAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(null,"La date de fin est obligatoire.")]
    #[AppAssert\DateRange()]
    private ?\DateTimeInterface $dateEndAt = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    /**
     * @var Collection<int, Animator>
     */
    #[ORM\ManyToMany(targetEntity: Animator::class, inversedBy: 'events')]
    private Collection $animators;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?User $lastUserUpdate = null;

    /**
     * @var list<string> The photos
     */
    #[ORM\Column(nullable: true)]
    private array $photos = [];

    #[ORM\Column]
    private ?bool $isEnabled = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainPicture = null;
    private $mainPictureFile;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture2 = null;
    private $picture2File;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture3 = null;
    private $picture3File;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $rdvAddress = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $rdvCity = null;

    #[ORM\Column(length: 150)]
    private ?string $cityPlace = null;

    public function __construct()
    {
        $this->animators = new ArrayCollection();
    }

    public function getPicture2File()
    {
        return $this->picture2File;
    }

    public function setPicture2File($picture2File): self
    {
        $this->picture2File = $picture2File;

        return $this;
    }

    public function getPicture3File()
    {
        return $this->picture3File;
    }

    public function setPicture3File($picture3File): self
    {
        $this->picture3File = $picture3File;

        return $this;
    }

    public function getMainPictureFile()
    {
        return $this->mainPictureFile;
    }

    public function setMainPictureFile($mainPictureFile): self
    {
        $this->mainPictureFile = $mainPictureFile;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getPreparationInfos(): ?string
    {
        return $this->preparationInfos;
    }

    public function setPreparationInfos(?string $preparationInfos): static
    {
        $this->preparationInfos = $preparationInfos;

        return $this;
    }

    public function getEventDistance(): ?string
    {
        return $this->eventDistance;
    }

    public function setEventDistance(?string $eventDistance): static
    {
        $this->eventDistance = $eventDistance;

        return $this;
    }

    public function getRdvPlaceName(): ?string
    {
        return $this->rdvPlaceName;
    }

    public function setRdvPlaceName(?string $rdvPlaceName): static
    {
        $this->rdvPlaceName = $rdvPlaceName;

        return $this;
    }

    public function getRdvLatitude(): ?string
    {
        return $this->rdvLatitude;
    }

    public function setRdvLatitude(?string $rdvLatitude): static
    {
        $this->rdvLatitude = $rdvLatitude;

        return $this;
    }

    public function getRdvLongitude(): ?string
    {
        return $this->rdvLongitude;
    }

    public function setRdvLongitude(?string $rdvLongitude): static
    {
        $this->rdvLongitude = $rdvLongitude;

        return $this;
    }

    public function getTimeStartAt(): ?\DateTimeInterface
    {
        return $this->timeStartAt;
    }

    public function setTimeStartAt(?\DateTimeInterface $timeStartAt): static
    {
        $this->timeStartAt = $timeStartAt;

        return $this;
    }

    public function getTimeEndAt(): ?\DateTimeInterface
    {
        return $this->timeEndAt;
    }

    public function setTimeEndAt(?\DateTimeInterface $timeEndAt): static
    {
        $this->timeEndAt = $timeEndAt;

        return $this;
    }

    public function getDateStartAt(): ?\DateTimeInterface
    {
        return $this->dateStartAt;
    }

    public function setDateStartAt(?\DateTimeInterface $dateStartAt): static
    {
        $this->dateStartAt = $dateStartAt;

        return $this;
    }

    public function getDateEndAt(): ?\DateTimeInterface
    {
        return $this->dateEndAt;
    }

    public function setDateEndAt(?\DateTimeInterface $dateEndAt): static
    {
        $this->dateEndAt = $dateEndAt;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return Collection<int, Animator>
     */
    public function getAnimators(): Collection
    {
        return $this->animators;
    }

    public function addAnimator(Animator $animator): static
    {
        if (!$this->animators->contains($animator)) {
            $this->animators->add($animator);
        }

        return $this;
    }

    public function removeAnimator(Animator $animator): static
    {
        $this->animators->removeElement($animator);

        return $this;
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

    public function getLastUserUpdate(): ?User
    {
        return $this->lastUserUpdate;
    }

    public function setLastUserUpdate(?User $lastUserUpdate): static
    {
        $this->lastUserUpdate = $lastUserUpdate;

        return $this;
    }

    public function getPhotos(): ?array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): self
    {
        $this->photos = $photos;

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

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?string $mainPicture): static
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }
    
    public function getPicture2(): ?string
    {
        return $this->picture2;
    }

    public function setPicture2(?string $picture2): static
    {
        $this->picture2 = $picture2;

        return $this;
    }

    public function getPicture3(): ?string
    {
        return $this->picture3;
    }

    public function setPicture3(?string $picture3): static
    {
        $this->picture3 = $picture3;

        return $this;
    }

    public function getRdvAddress(): ?string
    {
        return $this->rdvAddress;
    }

    public function setRdvAddress(?string $rdvAddress): static
    {
        $this->rdvAddress = $rdvAddress;

        return $this;
    }

    public function getRdvCity(): ?string
    {
        return $this->rdvCity;
    }

    public function setRdvCity(?string $rdvCity): static
    {
        $this->rdvCity = $rdvCity;

        return $this;
    }

    public function getCityPlace(): ?string
    {
        return $this->cityPlace;
    }

    public function setCityPlace(string $cityPlace): static
    {
        $this->cityPlace = $cityPlace;

        return $this;
    }

    public function isPassed(): bool
    {
        // Vérifie si la date de début est avant la date actuelle
        if ($this->dateStartAt instanceof \DateTimeInterface) {
            return $this->dateStartAt < new \DateTime(); // Compare à la date actuelle
        }
        return false; // Si la date de début n'est pas définie, renvoie false
    }

    /**
     * Vérifie si l'événement s'étend sur plusieurs jours.
     * 
     * @return bool
     */
    public function isMultiDay(): bool
    {
        if ($this->dateStartAt instanceof \DateTimeInterface && $this->dateEndAt instanceof \DateTimeInterface) {
            return $this->dateStartAt->format('Y-m-d') !== $this->dateEndAt->format('Y-m-d');
        }
        return false; // Si l'une des deux dates n'est pas définie, on considère que ce n'est pas multi-jour
    }

}
