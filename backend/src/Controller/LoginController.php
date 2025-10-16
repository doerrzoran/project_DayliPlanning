<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
     #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        return new JsonResponse([
    'id' => $user->getId(),
    'name' => $user->getName(),
    'firstname' => $user->getFirstname(),
    'email' => $user->getEmail(),
    'contractWeeklyHours' => $user->getContractWeeklyHours(),
    'contractStart' => $user->getContratStart()?->format('Y-m-d'),  // Assurez-vous que la méthode s’appelle bien ainsi
    'contractEnd' => $user->getContractEnd()?->format('Y-m-d'),
    'role' => $user->getRole()?->getLabel(),
    'manager' => $user->getManager() ? [
        'id' => $user->getManager()->getId(),
        'name' => $user->getManager()->getName(),
        'firstname' => $user->getManager()->getFirstname(),
        'email' => $user->getManager()->getEmail(),
    ] : null,
    'contractHalfDays' => array_map(
        fn($halfDay) => [
            'id' => $halfDay->getId(),
            'label' => $halfDay->getLabel(),
        ],
        $user->getContractHalfDays()->toArray()
    ),
    'presences' => array_map(
        fn($presence) => [
            'id' => $presence->getId(),
            'date' => $presence->getDate()?->format('Y-m-d'),
            'arrival' => $presence->getArrival()?->format('H:i:s'),
            'depature' => $presence->getDepature()?->format('H:i:s'),
            'halfDayLabel' => $presence->getHalfDay()?->getLabel(),
            // 'status' if it exists on the entity, otherwise remove
        ],
        $user->getPresences()->toArray()
    ),
    'absences' => array_map(
        fn($absence) => [
            'id' => $absence->getId(),
            'dateStart' => $absence->getDateStart()?->format('Y-m-d'),
            'dateEnd' => $absence->getDateEnd()?->format('Y-m-d'),
            'reason' => $absence->getReason(),
        ],
        $user->getAbsences()->toArray()
    ),
    'roles' => $user->getRoles(),  // tableau de chaînes
]);

    }
}
