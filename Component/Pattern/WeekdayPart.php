<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

class WeekdayPart extends AbstractPart
{
    protected $module = 7;

    protected $partName = 'weekday';

    public function __construct($pattern)
    {
        $this->range = range(0, 6);
        parent::__construct($pattern);
    }
}
