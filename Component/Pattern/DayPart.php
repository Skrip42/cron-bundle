<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

use DateTime;

class DayPart extends AbstractPart
{
    protected $partName = 'day';

    public function __construct(string $pattern, int $month = null, int $year = null)
    {
        if (empty($month) || empty($year)) {
            $date = new DateTime('now');
        } else {
            $date = new DateTime("$year-$month-01");
        }
        $this->buildRange($date);
        parent::__construct($pattern);
    }

    /**
     * @param DateTime $date
     *
     * @return null
     */
    private function buildRange(DateTime $date)
    {
        $this->range = range(1, (int) $date->format('t'));
        $this->module = (int) $date->format('t');
    }

    /**
     * @param DateTime $date
     *
     * @return null
     */
    public function rebuildRangeByDate(DateTime $date)
    {
        $this->buildRange($date);
        $this->values = [];
    }

    /**
     * @param int $val
     * @param int $sub
     *
     * @return int
     */
    protected function subByModule(int $val, int $sub) : int
    {
        $val--;
        $val = parent::subByModule($val, $sub);
        return ++$val;
    }

    /**
     * @param int $val
     * @param int $add
     *
     * @return int
     */
    protected function addByModule(int $val, int $add) : int
    {
        $val--;
        $val = parent::addByModule($val, $sub);
        return ++$val;
    }
}
