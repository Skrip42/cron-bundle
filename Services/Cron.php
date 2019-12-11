<?php

namespace Skrip42\Bundle\CronBundle\Services;

use Doctrine\DBAL\Connection;
use DateTime;
use DateTimeZone;
use Skrip42\Bundle\CronBundle\Repository\ScheduleRepository;
use Skrip42\Bundle\CronBundle\Component\Pattern;
use Skrip42\Bundle\CronBundle\Entity\Schedule;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Cron
{
    /**
     * Connection $connection instance of bd connection
     * */
    private $repository;

    private $container;

    /**
     * Class constructor
     *
     * @param Connection $connection instance of bd connection
     */
    public function __construct(ScheduleRepository $repository, ContainerInterface $container)
    {
        $this->repository = $repository;
        $this->container = $container;
    }

    /**
     * Get current time schedule
     *
     * @return string
     */
    public function getActualOnCurrentTime() : ?array
    {
        $schedules = $this->repository->getAll();
        $actualSchedule = [];
        $date = new DateTime('now');
        foreach ($schedules as $schedule) {
            $pattern = new Pattern($schedule->getPattern());
            if ($pattern->test($date)) {
                $actualSchedule[] = $schedule;
            }
        }
        return $actualSchedule;
    }

    public function optimize() : int
    {
        $schedules = $this->repository->getAll(false);
        $count = 0;
        foreach ($schedules as $schedule) {
            $pattern = new Pattern($schedule->getPattern());
            if (empty($pattern->getClosest())) {
                $count++;
                $schedule->setActive(false);
            }
        }
        $this->container->get('doctrine')->getManager()->flush();
        return $count;
    }

    public function closestList(int $count = 15): ?array
    {
        $schedules = $this->repository->getAll(false);
        $closestList = [];
        foreach ($schedules as $schedule) {
            $pattern = new Pattern($schedule->getPattern());
            $closest = $pattern->getClosest($count);
            $command = $schedule->getCommand();
            $id      = $schedule->getId();
            foreach ($closest as $c) {
                $closestList[] = [$id, $command, $c];
            }
        }
        usort(
            $closestList,
            function ($a, $b) {
                return $a[2] <=> $b[2];
            }
        );
        $closestList = array_slice($closestList, 0, $count);
        return $closestList;
    }

    public function getList(bool $all = false): ?array
    {
        $schedules = $this->repository->getAll($all);
        return $schedules;
    }

    public function addSchedule(string $pattern, string $command)
    {
        $schedule = new Schedule();
        $schedule->setPattern($pattern);
        $schedule->setCommand($command);
        $em = $this->container->get('doctrine')->getManager();
        $em->persist($schedule);
        $em->flush();
    }

    public function toggleSchedule(int $id)
    {
        $schedule = $this->repository->find($id);
        $schedule->toggleActive();
        $this->container->get('doctrine')->getManager()->flush();
    }

    public function updateSchedule(int $id, string $pattern, string $command)
    {
        $schedule = $this->repository->find($id);
        $schedule->setPattern($pattern);
        $schedule->setCommand($command);
        $this->container->get('doctrine')->getManager()->flush();
    }
}
