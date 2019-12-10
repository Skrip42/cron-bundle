<?php

namespace Skrip42\Bundle\ChronBundle\Services;

use Doctrine\DBAL\Connection;
use DateTime;
use DateTimeZone;
use Skrip42\Bundle\ChronBundle\Repository\ScheduleRepository;
use Skrip42\Bundle\ChronBundle\Component\Pattern;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Chron
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
        var_export($count);
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
}
