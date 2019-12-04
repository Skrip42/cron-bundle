<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

class LexIterator
{
    private $pattern;

    private $iterator;

    public function __construct(string $pattern)
    {
        $this->iterator = 0;
        $this->pattern = $pattern;
    }

    /**
     *
     * @return null
     */
    public function reset()
    {
        $this->iterator = 0;
    }

    /**
     *
     * @return string
     */
    public function getLexem() : string
    {
        if ($this->iterator >= strlen($this->pattern)) {
            return '$';
        }
        $lex = $this->pattern[$this->iterator];
        if (is_numeric($lex)) {
            while ($this->iterator + 1 < strlen($this->pattern) && is_numeric($this->pattern[$this->iterator + 1])) {
                $this->iterator++;
                $lex .= $this->pattern[$this->iterator];
            }
        }
        $this->iterator++;
        return $lex;
    }
}
