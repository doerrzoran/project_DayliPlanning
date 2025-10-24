<?php

namespace App\Controller;

use App\Service\TeamAbsenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TeamController extends AbstractController
{
    private $teamAbsenceService;
    public function __construct(
        TeamAbsenceService $teamAbsenceService,
    )
    {
        $this->teamAbsenceService = $teamAbsenceService;
    }

    #[Route('/api/team', name: 'api_team')]
    public function getTeam(): JsonResponse
    {
        $user = $this->getUser();
        $team = $this->teamAbsenceService->team($user);

        $result = array_map(function($employe) {
            return [
                'name' => $employe->getName(),
                'firstname' => $employe->getFirstname(),
                'email' => $employe->getEmail(),
                'presences' => array_map(function($presence) {
                    return [
                        'id' => $presence->getId(),
                        'date' => $presence->getDate()->format('Y-m-d'),
                    ];
                }, $employe->getPresences()->toArray()),
                'absences' => array_map(function($absence) {
                    return [
                        'id' => $absence->getId(),
                        'date' => $absence->getDate()->format('Y-m-d'),
                        'absenceType' => [
                            'id' => $absence->getAbsenceType()->getId(),
                            'label' => $absence->getAbsenceType()->getLabel(),
                        ],
                        'halfDay' => [
                            'id' => $absence->getHalfDay()->getId(),
                            'label' => $absence->getHalfDay()->getLabel(),
                        ],
                        'isAccepted' => $absence->isAccepted(),
                    ];
                }, $employe->getAbsences()->toArray()),
            ];
        }, $team);

        return $this->json([
            'team' => $result
        ]);
    }

}
