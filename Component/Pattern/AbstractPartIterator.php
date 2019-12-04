<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

use Iterator;

abstract class AbstractPartIterator implements Iterator
{
    /**
     * return values of part
     *
     * @return array
     */
    abstract public function getValues() : array;

    /** @var int $iterator current position*/
    private $iterator = 0;

    /**
     * return current values
     *
     * @return int
     */
    public function current() : int
    {
        return $this->getValues()[$this->iterator];
    }

    /**
     * return current key
     *
     * @return int
     */
    public function key() : int
    {
        return $this->iterator;
    }

    /**
     * increase iterator
     *
     * @return null
     */
    public function next()
    {
        $this->iterator++;
    }

    /**
     * rewind iterator to bottom limit ($val)
     *
     * if exist $val return true;
     *
     * @param int $val
     *
     * @return bool
     */
    public function rewindToLimit(int $val) : bool
    {
        $this->rewind();
        while ($this->valid() && $val > $this->current()) {
            $this->next();
        }
        return $this->valid() && $val == $this->current();
    }

    /**
     * rewind iterator to begin
     *
     * @return null
     */
    public function rewind()
    {
        $this->iterator = 0;
    }

    /**
     * Check isValid iterator
     *
     * @return null
     */
    public function valid()
    {
        return isset($this->getValues()[$this->iterator]);
    }
}
