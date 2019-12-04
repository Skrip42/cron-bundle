<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

use DateTime;

/**
 * This is compile part of weekday and day part (for closest date)
 */
class CompileDayPart extends AbstractPartIterator
{
    private $day;
    private $weekday;

    private $values = [];
    private $offset = 0;

    public function __construct(DayPart $day, WeekdayPart $weekday)
    {
        $date = new DateTime('now');
        $date = new DateTime(
            $date->format('Y') . '-' . $date->format('m') . '-01'
        );
        $this->offset = (int) $date->format('w');

        $this->day = $day;
        $this->weekday = $weekday;
    }

    /**
     * Rebuild by $month anb $year
     *
     * @param int $month
     * @param int $year
     *
     * @return null
     */
    public function rebuildByDate(int $month, int $year)
    {
        $date = new DateTime("$year-$month-01");
        $this->offset = (int) $date->format('w');
        $this->day->rebuildRangeByDate($date);
        $this->values = [];
    }

    /**
     *
     * @return array
     */
    public function getValues() : array
    {
        if (empty($this->values)) {
            $this->compile();
        }
        return $this->values;
    }

    /**
     *
     * @return null
     */
    private function compile()
    {
        if ($this->weekday->getPattern() == '*') {
            $this->values = $this->day->getValues();
            return;
        }
        $dayVal = $this->day->getValues();
        $weekVal = $this->weekday->getValues();
        $dayRange = $this->day->getRange();

        $weekDate = array_filter(
            $dayRange,
            function ($el) use ($weekVal) {
                return in_array(($el - 1 + $this->offset) % 7, $weekVal);
            }
        );
        $this->values = array_values(array_intersect($dayVal, $weekDate));
    }
}
