<?php

namespace App\Service;

use App\Entity\Absence;

use App\Entity\HalfDay;
use App\Repository\AbsenceTypeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AbsenceRequestService
{
    const DAY_START ="07:00";
    const LUNCH_BREAK = "12:30";
    const END_BREAK = "13:30";
    const DAY_END = "19:00";

    private $em;
    private $absenceTypeRepository;

    public function __construct(
        EntityManagerInterface $em,
        AbsenceTypeRepository $absenceTypeRepository
    )
    {
        $this->em = $em;
        $this->absenceTypeRepository = $absenceTypeRepository;
    }

    public function newRequest($user, $absenceType, $dateDebut, $timeUniqDay = null, $dateFin = null): string
    {

        try {
            $halfDayArray = [];
            if(!$dateFin && $timeUniqDay){
                $halfDayArray = $this->singleDayAbsence($dateDebut, $timeUniqDay);
            }else {
                $halfDayArray = $this->multipleDaysAbsence($dateDebut, $dateFin);
            }
    
            $this->newHalfDays($halfDayArray, $absenceType, $user);
        }catch(\Exception $e){
            return 'echec de l\'enregistrement';
        }
          
        return 'enregistrement effectué';
    }

    private function singleDayAbsence($dateDebut, ?string $timeUniqDay): array
    {
        $halfDayArray = [];
        $dateDebut = new \DateTimeImmutable($dateDebut);
        $dayString = $dateDebut->format('Y-m-d');

        switch ($timeUniqDay) {
            case 'fullday':
                $halfDayArray[] = [
                    'date' => $dayString,
                    'startHour' => self::DAY_START,
                    'endHour' => self::LUNCH_BREAK,
                ];
                $halfDayArray[] = [
                    'date' => $dayString,
                    'startHour' => self::END_BREAK,
                    'endHour' => self::DAY_END,
                ];
                break;

            case 'morning':
                $halfDayArray[] = [
                    'date' => $dayString,
                    'startHour' => self::DAY_START,
                    'endHour' => self::LUNCH_BREAK,
                ];
                break;

            case 'afternoon':
                $halfDayArray[] = [
                    'date' => $dayString,
                    'startHour' => self::END_BREAK,
                    'endHour' => self::DAY_END,
                ];
                break;

            default:
                // Ici, tu peux gérer le cas où $timeUniqDay n'est pas reconnu
                throw new \InvalidArgumentException("Invalid timeUniqDay value: $timeUniqDay");
        }

        return $halfDayArray;
    }

    private function multipleDaysAbsence($dateDebut, $dateFin)
    {
        $halfDayArray = [];

        $dateDebut = new \DateTimeImmutable($dateDebut);
        $dateFin = new \DateTimeImmutable($dateFin);

        $currentDate = $dateDebut->setTime(0, 0, 0);
        $endDate = $dateFin->setTime(0, 0, 0);

        while ($currentDate <= $endDate) {
            $dayString = $currentDate->format('Y-m-d');

            $halfDayArray[] = [
                'date' => $dayString,
                'startHour' => self::DAY_START,
                'endHour' => self::LUNCH_BREAK,
            ];

            $halfDayArray[] = [
                'date' => $dayString,
                'startHour' => self::END_BREAK,
                'endHour' => self::DAY_END,
            ];

            $currentDate = $currentDate->modify('+1 day');
        }

        return $halfDayArray;
    }

    private function newHalfDays(array $halfDayArray, $absenceType, $user)
    {
        $absenceType = $this->absenceTypeRepository->find($absenceType);
        foreach ($halfDayArray as $halfDay) {
            $date = new \DateTimeImmutable($halfDay['date']);
            $dayName = strtolower($date->format('l')); // donnant "monday", etc.
            $startHour = $halfDay['startHour'];

            $partOfDay = ($startHour === self::DAY_START) ? 'morning' : 'afternoon';

            // Clef constante attendue, ex : MONDAY_MORNING
            $constName = strtoupper($dayName) . '_' . strtoupper($partOfDay);

            // Récupère la valeur de la constante HalfDay correspondante
            $label = defined('App\Entity\HalfDay::' . $constName) 
            ? constant('App\Entity\HalfDay::' . $constName) 
            : null;

            $newHalfDay = (new HalfDay())
                ->setLabel($label)
                ->setHalfDayStart(new \DateTime($halfDay['startHour']))
                ->setHalfDayEnd(new \DateTime($halfDay['endHour']));
            ;

            $this->em->persist($newHalfDay);

            $absence = (new Absence())
                ->setDate(new \DateTime($halfDay['date']))
                ->setAbsenceType($absenceType)
                ->setEmploye($user)
                ->setIsAccepted(false)
                ->setHalfDay($newHalfDay)
            ;
            
            $this->em->persist($absence);
        }

        $this->em->flush();
    }
}
