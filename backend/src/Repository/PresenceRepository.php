<?php

namespace App\Repository;

use App\Entity\Presence;
use App\Entity\User;
use App\Entity\HalfDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presence>
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    /**
     * Retourne une présence "ouverte" (depature IS NULL) pour un employé,
     * sur la même date (champ DATE) et la même demi-journée.
     *
     * @param User $user
     * @param \DateTimeInterface $date  Date (heure ignorée)
     * @param HalfDay $halfDay
     * @return Presence|null
     */
    public function findOpenForUserByDateAndHalfDay(User $user, \DateTimeInterface $date, HalfDay $halfDay): ?Presence
{
    // Construire le range couvrant toute la journée
    $start = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' 00:00:00');
    $end   = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' 23:59:59');

    $dql = '
        SELECT p
        FROM App\Entity\Presence p
        WHERE p.employe = :user
          AND p.halfDay = :halfDay
          AND p.depature IS NULL
          AND p.date BETWEEN :start AND :end
    ';

    $query = $this->getEntityManager()->createQuery($dql)
        ->setParameter('user', $user)
        ->setParameter('halfDay', $halfDay)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->setMaxResults(1);

    return $query->getOneOrNullResult();
}


    // ... autres méthodes du repository ...
}
