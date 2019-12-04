<?php

namespace Skrip42\Bundle\ChronBundle\Services;

use Doctrine\DBAL\Connection;
use DateTime;
use DateTimeZone;
use Skrip42\Bundle\ChronBundle\Repository\ScheduleRepository;
use Skrip42\Bundle\ChronBundle\Component\Schedule\Pattern;

class Chron
{
    /**
     * Connection $connection instance of bd connection
     * */
    private $repository;

    /**
     * Class constructor
     *
     * @param Connection $connection instance of bd connection
     */
    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
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
        //$date->setTimezone(new DateTimeZone('+0700'));
        foreach ($schedules as $schedule) {
            $pattern = new Pattern($schedule->getPattern());
            if ($pattern->test($date)) {
                $actualSchedule[] = $schedule;
            }
        }
        return $actualSchedule;
    }
}
