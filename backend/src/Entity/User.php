<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?int $contractWeeklyHours = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $contratStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $contractEnd = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $manager = null;

    /**
     * @var Collection<int, HalfDay>
     */
    #[ORM\ManyToMany(targetEntity: HalfDay::class)]
    private Collection $contractHalfDays;

    /**
     * @var Collection<int, Presence>
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'employe')]
    private Collection $presences;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'employe')]
    private Collection $absences;

    public function __construct()
    {
        $this->contractHalfDays = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->absences = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getContractWeeklyHours(): ?int
    {
        return $this->contractWeeklyHours;
    }

    public function setContractWeeklyHours(?int $contractWeeklyHours): static
    {
        $this->contractWeeklyHours = $contractWeeklyHours;

        return $this;
    }

    public function getContratStart(): ?\DateTime
    {
        return $this->contratStart;
    }

    public function setContratStart(?\DateTime $contratStart): static
    {
        $this->contratStart = $contratStart;

        return $this;
    }

    public function getContractEnd(): ?\DateTime
    {
        return $this->contractEnd;
    }

    public function setContractEnd(?\DateTime $contractEnd): static
    {
        $this->contractEnd = $contractEnd;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getManager(): ?self
    {
        return $this->manager;
    }

    public function setManager(?self $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return Collection<int, HalfDay>
     */
    public function getContractHalfDays(): Collection
    {
        return $this->contractHalfDays;
    }

    public function addContractHalfDay(HalfDay $contractHalfDay): static
    {
        if (!$this->contractHalfDays->contains($contractHalfDay)) {
            $this->contractHalfDays->add($contractHalfDay);
        }

        return $this;
    }

    public function removeContractHalfDay(HalfDay $contractHalfDay): static
    {
        $this->contractHalfDays->removeElement($contractHalfDay);

        return $this;
    }

    /**
     * @return Collection<int, Presence>
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): static
    {
        if (!$this->presences->contains($presence)) {
            $this->presences->add($presence);
            $presence->setEmploye($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): static
    {
        if ($this->presences->removeElement($presence)) {
            // set the owning side to null (unless already changed)
            if ($presence->getEmploye() === $this) {
                $presence->setEmploye(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setEmploye($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEmploye() === $this) {
                $absence->setEmploye(null);
            }
        }

        return $this;
    }

}
