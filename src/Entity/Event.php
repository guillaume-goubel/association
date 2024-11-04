<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
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
    private ?\DateTimeInterface $dateStartAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEndAt = null;

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
}
