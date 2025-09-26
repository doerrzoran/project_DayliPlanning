<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Role;
use App\Service\PasswordHasherService;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $hasherService;
    public function __construct(PasswordHasherService $hasherService)
    {
        $this->hasherService = $hasherService;
    }

    public function load(ObjectManager $manager): void
{
    $employeRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_EMPLOYE]);
    $adminRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_ADMIN]);
    $cadreRole = $manager->getRepository(Role::class)->findOneBy(['label' => Role::ROLE_CADRE]);

    // Date fixe de début de contrat au 1er janvier de cette année
    $year = (int)(new \DateTime())->format('Y');
    $startOfYear = new \DateTime("$year-01-01");


    $cadreUser = new User();
    $cadreUser->setName('Dupont')
        ->setFirstname('Jean')
        ->setEmail('jean.dupont@example.com')
        ->setPassword($this->hasherService->hash('Cadre:2'))
        ->setRole($cadreRole)
        ->setContractWeeklyHours(35)
        ->setContratStart($startOfYear);
    $manager->persist($cadreUser);

    $adminUser = new User();
    $adminUser->setName('Martin')
        ->setFirstname('Sophie')
        ->setEmail('sophie.martin@example.com')
        ->setPassword($this->hasherService->hash('Admin:2'))
        ->setRole($adminRole)
        ->setContractWeeklyHours(35)
        ->setContratStart($startOfYear);
    $manager->persist($adminUser);

    $employeUser = new User();
    $employeUser->setName('Durand')
        ->setFirstname('Paul')
        ->setEmail('paul.durand@example.com')
        ->setPassword($this->hasherService->hash('Employe:2'))
        ->setRole($employeRole)
        ->setManager($cadreUser)
        ->setContractWeeklyHours(35)
        ->setContratStart($startOfYear);
    $manager->persist($employeUser);

    $employeHalfTimeUser = new User();
    $employeHalfTimeUser->setName('Lemoine')
        ->setFirstname('Claire')
        ->setEmail('claire.lemoine@example.com')
        ->setPassword($this->hasherService->hash('EmployeMiTemps:1'))
        ->setRole($employeRole)
        ->setManager($cadreUser)
        ->setContractWeeklyHours(17)
        ->setContratStart($startOfYear)
        ->setContractEnd((clone $startOfYear)->modify('+1 year'));
    $manager->persist($employeHalfTimeUser);

    $manager->flush();
}
    public function getOrder(): int
    {
        return 2;
    }
}
