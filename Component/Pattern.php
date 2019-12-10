<?php
namespace Skrip42\Bundle\ChronBundle\Component;

use DateTime;

class Pattern
{
    private $parts = [
        'year'       => null,
        'month'      => null,
        'weekday'    => null,
        'day'        => null,
        'hourse'     => null,
        'minute'     => null,
        'compileDay' => null
    ];

    /**
     * Return $name part of pattern
     *
     * @param string $name
     *
     * @return Pattern\AbstractPart
     */
    public function getPart(string $name) : Pattern\AbstractPart
    {
        return $this->parts[$name];
    }

    private $pattern = '';

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->explodePattern();
    }

    /**
     * exploded string pattern tor pattern part
     *
     * @return null
     */
    private function explodePattern()
    {
        list($minute, $hourse, $day, $weekday, $month, $year) = explode('_', $this->pattern);
        $this->parts['year'] = new Pattern\YearPart($year);
        $this->parts['month'] = new Pattern\MonthPart($month);
        $this->parts['weekday'] = new Pattern\WeekdayPart($weekday);
        $this->parts['day'] = new Pattern\DayPart($day);
        $this->parts['hourse'] = new Pattern\HoursePart($hourse);
        $this->parts['minute'] = new Pattern\MinutePart($minute);
    }

    /**
     * dump all parts values
     *
     * @return null
     */
    public function dump()
    {
        dump(
            [
                'minute' => $this->parts['minute']->getValues(),
                'hourse' => $this->parts['hourse']->getValues(),
                'day' => $this->parts['day']->getValues(),
                'weekday' => $this->parts['weekday']->getValues(),
                'month' => $this->parts['month']->getValues(),
                'year' => $this->parts['year']->getValues()
            ]
        );
    }

    /**
     * test $date to pattern
     *
     * @param DateTime $date
     *
     * @return bool
     */
    public function test(DateTime $date) : bool
    {
        $this->parts['day']->rebuildRangeByDate($date);
        return $this->parts['year']->test((int) $date->format('Y'))
            && $this->parts['month']->test((int) $date->format('n'))
            && $this->parts['weekday']->test((int) $date->format('w'))
            && $this->parts['day']->test((int) $date->format('j'))
            && $this->parts['hourse']->test((int) $date->format('G'))
            && $this->parts['minute']->test((int) $date->format('i'));
    }

    /**
     * get closest date of pattern
     *
     * @param ?int $num
     *
     * @return array
     */
    public function getClosest(int $num = 1) : array
    {
        $result = [];
        $date = new DateTime('now');


        if (empty($this->parts['compileDay'])) {
            $this->parts['compileDay'] = new Pattern\CompileDayPart(
                $this->parts['day'],
                $this->parts['weekday']
            );
        }
        $current = true;

        $current = $this->parts['year']->rewindToLimit((int) $date->format('Y'));

        if ($current) {
            $current = $this->parts['month']->rewindToLimit((int) $date->format('n'));
        } else {
            $this->parts['month']->rewind();
        }
        
        if ($this->parts['year']->valid() && $this->parts['month']->valid()) {
            $this->parts['compileDay']->rebuildByDate(
                $this->parts['month']->current(),
                $this->parts['year']->current()
            );
            if ($current) {
                $current = $this->parts['compileDay']->rewindToLimit((int) $date->format('j'));
            } else {
                $this->parts['compileDay']->rewind();
            }
        }

        if ($current) {
            $current = $this->parts['hourse']->rewindToLimit((int) $date->format('G'));
        } else {
            $this->parts['hourse']->rewind();
        }

        if ($current) {
            $this->parts['minute']->rewindToLimit((int) $date->format('i'));
        } else {
            $this->parts['minute']->rewind();
        }

        while ($this->parts['year']->valid()) {
            while ($this->parts['month']->valid()) {
                while ($this->parts['compileDay']->valid()) {
                    while ($this->parts['hourse']->valid()) {
                        while ($this->parts['minute']->valid()) {
                            $result[] = $this->parts['year']->current()
                                . '-' . $this->fill($this->parts['month']->current(), 2)
                                . '-' . $this->fill($this->parts['compileDay']->current(), 2)
                                . ' ' . $this->fill($this->parts['hourse']->current(), 2)
                                . ':' . $this->fill($this->parts['minute']->current(), 2);
                            if (count($result) >= $num) {
                                return $result;
                            }
                            $this->parts['minute']->next();
                        }
                        $this->parts['minute']->rewind();
                        $this->parts['hourse']->next();
                    }
                    $this->parts['hourse']->rewind();
                    $this->parts['compileDay']->next();
                }
                $this->parts['month']->next();
                if ($this->parts['month']->valid()) {
                    $this->parts['compileDay']->rebuildByDate(
                        $this->parts['month']->current(),
                        $this->parts['year']->current()
                    );
                    $this->parts['compileDay']->rewind();
                }
            }
            $this->parts['month']->rewind();
            $this->parts['year']->next();
            if ($this->parts['year']->valid() && $this->parts['month']->valid()) {
                $this->parts['compileDay']->rebuildByDate(
                    $this->parts['month']->current(),
                    $this->parts['year']->current()
                );
                $this->parts['compileDay']->rewind();
            }
        }
        return $result;
    }

    private function fill(string $val, int $pos)
    {
        if (($len = strlen($val)) < $pos) {
            for ($i = $pos - $len; $i > 0; $i--) {
                $val = '0' . $val;
            }
        }
        return $val;
    }
}
