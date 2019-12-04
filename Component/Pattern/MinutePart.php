<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

class MinutePart extends AbstractPart
{
    protected $partName = 'minute';

    protected $module = 60;

    /**
     * @param mixed $pattern
     *
     * @return null
     */
    public function __construct($pattern)
    {
        $this->range = range(0, 59);
        parent::__construct($pattern);
    }
}
