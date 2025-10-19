<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Entity\HalfDay;
use App\Entity\Presence;
use App\Repository\PresenceRepository;
use App\Repository\HalfDayRepository;
use App\Service\TagService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use DateTimeImmutable;
use DateTime;

class TagServiceTest extends TestCase
{
    private $em;
    private $presenceRepository;
    private $halfDayRepository;
    private $clock;
    private $tagService;
    
    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->presenceRepository = $this->createMock(PresenceRepository::class);
        $this->halfDayRepository = $this->createMock(HalfDayRepository::class);
        $this->clock = $this->createMock(ClockInterface::class);

        $this->tagService = new TagService(
            $this->em,
            $this->presenceRepository,
            $this->halfDayRepository,
            $this->clock
        );
    }

    public function testEntryTagCreatesNewPresenceWhenNoOpenPresence(): void
    {
        $user = $this->createMock(User::class);
        $now = new DateTimeImmutable('2025-10-18 09:00:00');

        $this->clock->method('now')->willReturn($now);

        $this->presenceRepository
            ->expects($this->once())
            ->method('findOpenForUserByDateAndHalfDay')
            ->willReturn(null);

        $halfDay = new HalfDay();
        $halfDay->setLabel('matin');

        $this->halfDayRepository
            ->method('findOneBy')
            ->willReturn($halfDay);

        $this->em->expects($this->exactly(2))->method('persist');
        $this->em->expects($this->exactly(2))->method('flush');

        $result = $this->tagService->tag($user);

        // Le service retourne 'present' ou 'absent'
        $this->assertIsString($result);
        $this->assertEquals('present', $result);
    }

    public function testExitTagCompletesOpenPresence(): void
    {
        $user = $this->createMock(User::class);
        $now = new DateTimeImmutable('2025-10-18 17:00:00');

        $openPresence = new Presence();
        $openPresence->setArrival(new DateTime('2025-10-18 08:00:00'));
        $openPresence->setDepature(null);

        $this->clock->method('now')->willReturn($now);

        $this->presenceRepository
            ->method('findOpenForUserByDateAndHalfDay')
            ->willReturn($openPresence);

        $this->em->expects($this->any())->method('persist');
        $this->em->expects($this->any())->method('flush');

        $result = $this->tagService->tag($user);

        $this->assertIsString($result);
        $this->assertEquals('absent', $result);

        // On vérifie bien que la présence a été mise à jour (heure de départ)
        $this->assertInstanceOf(Presence::class, $openPresence);
        $this->assertEquals($now->format('H:i:s'), $openPresence->getDepature()->format('H:i:s'));
    }


}
