<?php
namespace Skrip42\Bundle\CronBundle\Component\Pattern;

class MonthPart extends AbstractPart
{
    protected $module = 12;
    
    protected $partName = 'month';

    public function __construct($pattern)
    {
        $this->range = range(1, 12);
        parent::__construct($pattern);
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
        $val = parent::addByModule($val, $add);
        return ++$val;
    }
}
