<?php

namespace App\Service;

use App\Repository\AbsenceRepository;
use Doctrine\ORM\EntityManagerInterface;

class AbsenceValidationService
{
    private $absenceRepository;
    private $em;

    public function __construct(
        AbsenceRepository $absenceRepository,
        EntityManagerInterface $em
    )
    {
        $this->absenceRepository = $absenceRepository; 
        $this->em = $em; 
    }

    public function validate(int $id)
    {
        $absence = $this->absenceRepository->find($id);

        $absence->setIsAccepted(true);

        $this->em->persist($absence);
        $this->em->flush();

        return 'absence validée';
    }
}
