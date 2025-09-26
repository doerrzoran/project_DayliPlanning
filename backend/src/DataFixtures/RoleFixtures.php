<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Role;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class RoleFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            Role::ROLE_EMPLOYE,
            Role::ROLE_ADMIN,
            Role::ROLE_CADRE,
        ];

        foreach ($roles as $label) {
            $role = new Role();
            $role->setLabel($label);
            $manager->persist($role);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
