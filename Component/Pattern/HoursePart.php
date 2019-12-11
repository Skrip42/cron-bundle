<?php
namespace Skrip42\Bundle\CronBundle\Component\Pattern;

class HoursePart extends AbstractPart
{
    protected $partName = 'hourse';

    protected $module = 24;

    /**
     * @param mixed $pattern
     *
     * @return null
     */
    public function __construct($pattern)
    {
        $this->range = range(0, 23);
        parent::__construct($pattern);
    }
}
