<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PresenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PresenceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['presence:read']],
    denormalizationContext: ['groups' => ['presence:write']]
)]
class Presence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $arrival = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    #[Groups(['presence:read','presence:write'])]
    private ?\DateTime $depature = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HalfDay $halfDay = null;

    #[ORM\ManyToOne(inversedBy: 'presences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['presence:read','presence:write'])]
    private ?User $employe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getArrival(): ?\DateTime
    {
        return $this->arrival;
    }

    public function setArrival(\DateTime $arrival): static
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getDepature(): ?\DateTime
    {
        return $this->depature;
    }

    public function setDepature(?\DateTime $depature): static
    {
        $this->depature = $depature;

        return $this;
    }

    public function getHalfDay(): ?HalfDay
    {
        return $this->halfDay;
    }

    public function setHalfDay(?HalfDay $halfDay): static
    {
        $this->halfDay = $halfDay;

        return $this;
    }

    public function getEmploye(): ?User
    {
        return $this->employe;
    }

    public function setEmploye(?User $employe): static
    {
        $this->employe = $employe;

        return $this;
    }
}
