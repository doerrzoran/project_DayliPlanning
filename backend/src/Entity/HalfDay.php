<?php

namespace App\Entity;

use App\Repository\HalfDayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HalfDayRepository::class)]
class HalfDay
{
    public const MONDAY_MORNING = 'lundi matin';
    public const MONDAY_AFTERNOON = 'lundi apres midi';
    public const TUESDAY_MORNING = 'mardi matin';
    public const TUESDAY_AFTERNOON = 'mardi apres midi';
    public const WEDNESDAY_MORNING = 'mercredi matin';
    public const WEDNESDAY_AFTERNOON = 'mercredi apres midi';
    public const THURSDAY_MORNING = 'jeudi matin';
    public const THURSDAY_AFTERNOON = 'jeudi apres midi';
    public const FRIDAY_MORNING = 'vendredi matin';
    public const FRIDAY_AFTERNOON = 'vendredi apres midi';
    public const SATURDAY_MORNING = 'samedi matin';
    public const SATURDAY_AFTERNOON = 'samedi apres midi';
    public const SUNDAY_MORNING = 'dimanche matin';
    public const SUNDAY_AFTERNOON = 'dimanche apres midi';


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
