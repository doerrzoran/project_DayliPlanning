<?php

namespace App\Controller;

use App\Service\AbsenceValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AbsenceValidateController extends AbstractController
{

    private $absenceValidationService;
    public function __construct(
        AbsenceValidationService $absenceValidationService
    )
    {
        $this->absenceValidationService = $absenceValidationService;
    }


    #[Route('/api/team/absence', name: 'api_team_absence')]
    public function validateAbsence(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'];
        foreach ($ids as $id){
            $result = $this->absenceValidationService->validate($id);
        }

        return $this->json(['result' => $result]);
    }
}
