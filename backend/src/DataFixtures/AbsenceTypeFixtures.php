<?php

namespace App\DataFixtures;

use App\Entity\AbsenceType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AbsenceTypeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $conge = (new AbsenceType())
            ->setLabel("conge principale")
        ;

        $manager->persist($conge);

        $arretMaladie = (new AbsenceType())
            ->setLabel("arret Maladie")
        ;

        $manager->persist($arretMaladie);

        $manager->flush();
    }

    public static function getGroups(): array
    {
       return ['group1'];
    }
}
