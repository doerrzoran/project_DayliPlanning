<?php

namespace App\Controller;

use App\Service\AbsenceRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AbsenceRequestController extends AbstractController
{
    private $absenceRequestService;
    public function __construct(AbsenceRequestService $absenceRequestService)
    {
        $this->absenceRequestService = $absenceRequestService;
    }

    #[Route('/api/absence/request', name: 'app_absence_request', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $absenceType = $data["absenceType"];
        $dateDebut = $data["dateDebut"];
        $dateFin = $data["dateFin"];
        $timeUniqDay = $data["timeUniqDay"];
        $user = $this->getUser();

        $result = $this->absenceRequestService->newRequest($user, $absenceType, $dateDebut, $timeUniqDay, $dateFin);

        return $this->json([
            "result"=> $result,
        ]);
    }
}
