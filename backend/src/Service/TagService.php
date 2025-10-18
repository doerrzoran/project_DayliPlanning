<?php

namespace App\Service;

use App\Entity\HalfDay;
use App\Entity\Presence;
use App\Entity\User;
use App\Repository\HalfDayRepository;
use App\Repository\PresenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;
use DateTimeImmutable;
use DateTime;

class TagService
{
    private EntityManagerInterface $em;
    private PresenceRepository $presenceRepository;
    private HalfDayRepository $halfDayRepository;
    private ClockInterface $clock;

    public function __construct(
        EntityManagerInterface $em,
        PresenceRepository $presenceRepository,
        HalfDayRepository $halfDayRepository,
        ClockInterface $clock
    ) {
        $this->em = $em;
        $this->presenceRepository = $presenceRepository;
        $this->halfDayRepository = $halfDayRepository;
        $this->clock = $clock;
    }

    /**
     * Effectue le "badge" : nouvelle présence (arrivée) ou complétion (départ).
     *
     * @return Presence|null la présence créée / modifiée ou null en cas d'erreur
     */
    public function tag(User $user): ?Presence
    {
        // Maintenant (DateTimeImmutable) fourni par le Clock (Symfony)
        $now = $this->clock->now();

        // Détermine la halfDay correspondant à l'heure maintenant
        $halfDay = $this->determineHalfDayForDateTime($now);

        // Cherche une presence incomplète pour cet employé + demi-journée + date
        $openPresence = $this->checkPresence($user, $now, $halfDay);

        if ($openPresence) {
            // on complète (exit badge)
            return $this->exitTag($openPresence, $now);
        }

        // sinon on crée une nouvelle présence (entry badge)
        return $this->entryTag($user, $now, $halfDay);
    }

    /**
     * Cherche une presence non complétée (depature IS NULL) pour l'utilisateur,
     * pour la même date (jour) et même demi-journée.
     *
     * @param User $user
     * @param DateTimeImmutable $dateTime
     * @param HalfDay $halfDay
     * @return Presence|null
     */
    private function checkPresence(User $user, DateTimeImmutable $dateTime, HalfDay $halfDay): ?Presence
    {
        // Normaliser la date à 00:00 pour comparaison DATE
        $dayStart = new DateTimeImmutable($dateTime->format('Y-m-d') . ' 00:00:00');

        // Requête simple via repository : chercher presence pour cet employé, date (Y-m-d), halfDay et depature IS NULL
        // On suppose que PresenceRepository existe et on utilise QueryBuilder au besoin.
        return $this->presenceRepository->findOpenForUserByDateAndHalfDay($user, $date, $halfDay);
    }
    /**
     * Crée et persiste une nouvelle présence (arrivée).
     *
     * @param User $user
     * @param DateTimeImmutable $now
     * @param HalfDay $halfDay
     * @return Presence
     */
    private function entryTag(User $user, DateTimeImmutable $now, HalfDay $halfDay): Presence
    {
        $presence = $this->newPresence($now, $user, $halfDay);

        $this->em->persist($presence);
        $this->em->flush();

        // Ajout à la collection User (si nécessaire)
        $user->addPresence($presence);
        $this->em->persist($user);
        $this->em->flush();

        return $presence;
    }

    /**
     * Crée un objet Presence prêt (mais ne le persiste pas).
     *
     * @param DateTimeImmutable $now
     * @param User $user
     * @param HalfDay $halfDay
     * @return Presence
     */
    private function newPresence(DateTimeImmutable $now, User $user, HalfDay $halfDay): Presence
    {
        // Date : on veut la date (00:00) pour le champ DATE
        $dateOnly = new DateTime($now->format('Y-m-d'));

        $presence = (new Presence())
            ->setDate($dateOnly)
            ->setArrival(DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s')))
            ->setHalfDay($halfDay)
            ->setEmploye($user)
        ;

        return $presence;
    }

    /**
     * Si une demi-journée n'existe pas en base, on la crée avec des heures par défaut.
     *
     * @param string $label
     * @return HalfDay
     */
    private function ensureHalfDayExists(string $label): HalfDay
    {
        $existing = $this->halfDayRepository->findOneBy(['label' => $label]);
        if ($existing) {
            return $existing;
        }

        // Heures par défaut : matin 08:00-12:00, après-midi 13:00-17:00
        if (mb_stripos($label, 'matin') !== false) {
            $start = DateTime::createFromFormat('H:i', '08:00');
            $end = DateTime::createFromFormat('H:i', '12:00');
        } else {
            $start = DateTime::createFromFormat('H:i', '13:00');
            $end = DateTime::createFromFormat('H:i', '17:00');
        }

        $halfDay = (new HalfDay())
            ->setLabel($label)
            ->setHalfDayStart($start)
            ->setHalfDayEnd($end)
        ;

        $this->em->persist($halfDay);
        $this->em->flush();

        return $halfDay;
    }

    /**
     * Détermine le label de HalfDay suivant la date+heure donnée, cherche ou crée l'entité HalfDay.
     *
     * @param DateTimeImmutable $now
     * @return HalfDay
     */
    private function determineHalfDayForDateTime(DateTimeImmutable $now): HalfDay
    {
        // Récupère le jour de la semaine (1 = lundi ... 7 = dimanche)
        $dayNumber = (int) $now->format('N');
        $isMorning = ((int)$now->format('H') < 12); // <12 => matin, sinon après-midi

        // Map dayNumber -> préfixe de constant
        $map = [
            1 => 'MONDAY',
            2 => 'TUESDAY',
            3 => 'WEDNESDAY',
            4 => 'THURSDAY',
            5 => 'FRIDAY',
            6 => 'SATURDAY',
            7 => 'SUNDAY',
        ];

        $prefix = $map[$dayNumber] ?? 'MONDAY';
        $suffix = $isMorning ? 'MORNING' : 'AFTERNOON';

        // Construire le nom de la constante dans HalfDay (ex: MONDAY_MORNING)
        $constName = $prefix . '_' . $suffix;

        // Récupérer la valeur de la constante (libellé français)
        if (defined(HalfDay::class . '::' . $constName)) {
            $label = constant(HalfDay::class . '::' . $constName);
        } else {
            // fallback générique
            $label = ($isMorning ? 'matin' : 'apres midi');
        }

        // Retourne l'entité HalfDay existante ou crée une nouvelle entrée si absente
        return $this->ensureHalfDayExists($label);
    }

    /**
     * Complète la présence en mettant la dédeparture (exit).
     *
     * @param Presence $presence
     * @param DateTimeImmutable $now
     * @return Presence
     */
    private function exitTag(Presence $presence, DateTimeImmutable $now): Presence
    {
        $presence->setDepature(DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s')));
        $this->em->persist($presence);
        $this->em->flush();

        return $presence;
    }
}
