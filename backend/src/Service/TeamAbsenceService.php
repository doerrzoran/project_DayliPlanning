<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\AbsenceRepository;
use App\Repository\UserRepository;

class TeamAbsenceService
{
    private $userRepository;
    private $absenceRepository;
    public function __construct(
        UserRepository $userRepository,
        AbsenceRepository $absenceRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->absenceRepository = $absenceRepository;  
    }

    public function team(User $user): array
    {
        $team = $this->userRepository->findBy(["manager" => $user]);

        return $team;
    }

    public function teamAbsences(User $user): array
    {
        $team = $this->team($user);

        $demandeAbsences = [];

        foreach($team as $employe){
            $absences = $this->absenceRepository->findBy(["employe" => $employe , "isAccepted" => false ]);
            $demandeAbsences[] = $absences;
        }

        return $demandeAbsences;
    }
}
