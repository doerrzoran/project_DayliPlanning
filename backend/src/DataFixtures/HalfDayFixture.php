<?php

namespace App\DataFixtures;

use App\Entity\HalfDay;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HalfDayFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi'];
        foreach ($jours as $jour) {
            $morning = (new HalfDay)
                ->setLabel("$jour matin")
                ->setHalfDayStart(new \DateTime('08:30'))
                ->setHalfDayEnd(new \DateTime('12:30'));
            $manager->persist($morning);
            $this->addReference("{$jour}_matin", $morning);

            $afternoon = (new HalfDay)
                ->setLabel("$jour apres-midi")
                ->setHalfDayStart(new \DateTime('13:30'))
                ->setHalfDayEnd(new \DateTime('17:30'));
            $manager->persist($afternoon);
            $this->addReference("{$jour}_apres_midi", $afternoon);
        }
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}
