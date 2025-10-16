<?php

namespace App\DataFixtures;

use App\Entity\HalfDay;
use App\Entity\Presence;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PresenceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Références créées dans UserFixtures et HalfDayFixture
        $user = $this->getReference('user_employe', User::class);
        $halfDays = [
            'lundi_matin', 'lundi_apres_midi',
            'mardi_matin', 'mardi_apres_midi',
            'mercredi_matin', 'mercredi_apres_midi',
            'jeudi_matin', 'jeudi_apres_midi'
        ];

        $weekStart = new \DateTime('monday this week');
        foreach (range(0, 3) as $offset) {
            $date = (clone $weekStart)->modify("+$offset day");
            $morning = new Presence();
            $morning->setDate(clone $date)
                ->setArrival(new \DateTime('08:30'))
                ->setDepature(new \DateTime('12:30'))
                ->setHalfDay($this->getReference($halfDays[$offset * 2], HalfDay::class))
                ->setEmploye($user);
            $manager->persist($morning);

            $afternoon = new Presence();
            $afternoon->setDate(clone $date)
                ->setArrival(new \DateTime('13:30'))
                ->setDepature(new \DateTime('17:30'))
                ->setHalfDay($this->getReference($halfDays[$offset * 2 + 1], HalfDay::class))
                ->setEmploye($user);
            $manager->persist($afternoon);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            HalfDayFixture::class,
        ];
    }

    
}
