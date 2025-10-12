<?php

namespace App\Entity;

use App\Repository\HalfDayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HalfDayRepository::class)]
class HalfDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $halfDayStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $halfDayEnd = null;


    public function __construct()
    {
        $this->employe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getHalfDayStart(): ?\DateTime
    {
        return $this->halfDayStart;
    }

    public function setHalfDayStart(\DateTime $halfDayStart): static
    {
        $this->halfDayStart = $halfDayStart;

        return $this;
    }

    public function getHalfDayEnd(): ?\DateTime
    {
        return $this->halfDayEnd;
    }

    public function setHalfDayEnd(\DateTime $halfDayEnd): static
    {
        $this->halfDayEnd = $halfDayEnd;

        return $this;
    }

}
