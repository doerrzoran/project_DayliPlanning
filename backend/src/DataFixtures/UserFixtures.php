<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Role;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $employeRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_EMPLOYE]);
        $adminRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_ADMIN]);
        $cadreRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_CADRE]);

        $year = (int)(new \DateTime())->format('Y');
        $startOfYear = new \DateTime("$year-01-01");

        $cadreUser = new User();
        $cadreUser->setName('Dupont')
            ->setFirstname('Jean')
            ->setEmail('jean.dupont@example.com')
            ->setRole($cadreRole)
            ->setContractWeeklyHours(35)
            ->setContratStart($startOfYear);

        $hashedPassword = $this->passwordHasher->hashPassword($cadreUser, 'Cadre:2');
        $cadreUser->setPassword($hashedPassword);
        $manager->persist($cadreUser);

        $adminUser = new User();
        $adminUser->setName('Martin')
            ->setFirstname('Sophie')
            ->setEmail('sophie.martin@example.com')
            ->setRole($adminRole)
            ->setContractWeeklyHours(35)
            ->setContratStart($startOfYear);

        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'Admin:2');
        $adminUser->setPassword($hashedPassword);
        $manager->persist($adminUser);

        $employeUser = new User();
        $employeUser->setName('Durand')
            ->setFirstname('Paul')
            ->setEmail('paul.durand@example.com')
            ->setRole($employeRole)
            ->setManager($cadreUser)
            ->setContractWeeklyHours(35)
            ->setContratStart($startOfYear);

        $hashedPassword = $this->passwordHasher->hashPassword($employeUser, 'Employe:2');
        $employeUser->setPassword($hashedPassword);
        $manager->persist($employeUser);

        $employeHalfTimeUser = new User();
        $employeHalfTimeUser->setName('Lemoine')
            ->setFirstname('Claire')
            ->setEmail('claire.lemoine@example.com')
            ->setRole($employeRole)
            ->setManager($cadreUser)
            ->setContractWeeklyHours(17)
            ->setContratStart($startOfYear)
            ->setContractEnd((clone $startOfYear)->modify('+1 year'));

        $hashedPassword = $this->passwordHasher->hashPassword($employeHalfTimeUser, 'EmployeMiTemps:1');
        $employeHalfTimeUser->setPassword($hashedPassword);
        $manager->persist($employeHalfTimeUser);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
