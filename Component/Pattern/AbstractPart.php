<?php
namespace Skrip42\Bundle\CronBundle\Component\Pattern;

use DateTime;
use Skrip42\Bundle\CronBundle\Exception\Patern\PartException;

abstract class AbstractPart extends AbstractPartIterator
{
    /** @var int $module module of value*/
    protected $module;

    /** @var string $pattern pattern*/
    protected $pattern;

    /** @var array $range list of available values*/
    protected $range;

    /** @var array $value parsed values */
    protected $values = [];

    /** @var LexIterator $lexIterator */
    protected $lexIterator = null;

    protected $partName = 'abstract';

    /**
     * @param string $pattern
     *
     * @return null
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->lexIterator = new LexIterator($pattern);
    }

    /**
     * Test is value contained in array
     *
     * @param int $value
     *
     * @return bool
     */
    public function test(int $value) : bool
    {
        return in_array($value, $this->getValues());
    }

    public function getPattern() : string
    {
        return $this->pattern;
    }

    /**
     * Get values array
     * parse pattern if value array not exist
     *
     * @return array
     */
    public function getValues() : array
    {
        if (empty($this->values)) {
            $this->reparse();
        }
        return $this->values;
    }

    /**
     * Return renge of available values
     *
     * @return array
     */
    public function getRange() : array
    {
        return $this->range;
    }

    /**
     * Parse pattern initialize
     *
     * @return null
     */
    public function reparse()
    {
        $this->lexIterator->reset();
        if (is_null($this->pattern) || $this->pattern == '') {
            throw new InterpException("Pattern can't be empty!");
        }
        $this->values = $this->parse($this->lexIterator->getLexem());
        sort($this->values);
    }

    /**
     * Parse pattern recursive
     *
     * @param string $lex
     * @param array  $res
     *
     * @return array
     */
    protected function parse(string $lex, array $res = []) : array
    {
        switch ($lex) {
            case '$': //end pattern lexem
                return $this->endLex($res);
            case '(': //group
                $res = $this->groupLex();
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case ')': //end group
                break;
            case ',': //concat lexem
                $res = $this->concatLex($res, $this->parse($this->lexIterator->getLexem()));
                break;
            case '!': //invert lexem
                $res = $this->invLex($this->parse($this->lexIterator->getLexem()));
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case '+': //append lexem
                $res = $this->addLex($res, $this->lexIterator->getLexem());
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case '-': //substract lexem
                $res = $this->subLex($res, $this->lexIterator->getLexem());
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case '/': //step lexem
                $res = $this->stepLex($res, $this->lexIterator->getLexem());
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case ':': //range lexem
                $res = $this->rangeLex($res, $this->lexIterator->getLexem());
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            case '*': //all lexem
                $res = $this->range;
                $res = $this->parse($this->lexIterator->getLexem(), $res);
                break;
            default: //numeric lexem
                if (!is_numeric($lex)) {
                    throw new PartException(
                        'unknow lexem: ' . $lex,
                        $this->partName
                    );
                }
                if (((int) $lex) > max($this->range) || ((int) $lex) < -max($this->range)) {
                    throw new PartException(
                        $lex . ' - number out of available range.',
                        $this->partName
                    );
                }
                $res = $this->parse($this->lexIterator->getLexem(), [(int) $lex]);
                break;
        }
        return $res;
    }

    /**
     * Eval group lexem
     *
     * @return array
     */
    protected function groupLex() : array
    {
        return $this->parse($this->lexIterator->getLexem());
    }

    /**
     * Eval concat lexem
     *
     * @param array $res
     * @param array $concat
     *
     * @return array
     */
    protected function concatLex(array $res, array $concat) : array
    {
        return array_merge($res, $concat);
    }

    /**
     * Eval invert lexem
     *
     * @param array $res
     *
     * @return array
     */
    protected function invLex(array $res) : array
    {
        return array_diff($this->range, $res);
    }

    /**
     * End of pattern
     *
     * @param array $res
     *
     * @return array
     */
    protected function endLex(array $res) : array
    {
        return $res;
    }

    /**
     * Eval range build lexem
     *
     * @param array $l
     * @param mixed $r
     *
     * @return array
     */
    protected function rangeLex(array $l, $r) : array
    {
        if (!is_numeric($r)) {
            throw new PartException(
                'Cannot set range: ' . reset($l) . ':' . $r,
                $this->partName
            );
        }
        return range(reset($l), $r);
    }

    /**
     * Eval step filter lexem
     *
     * @param array $res
     * @param int   $step
     *
     * @return array
     */
    protected function stepLex(array $res, int $step) : array
    {
        if (!is_numeric($step)) {
            throw new PartException(
                'To the right of "/" should be a number!',
                $this->partName
            );
        }
        return array_filter(
            $res,
            function ($el) use ($step) {
                return !($el % $step);
            }
        );
    }

    /**
     * @param int $val
     * @param int $append
     *
     * @return int
     */
    protected function addByModule(int $val, int $append) : int
    {
        $val += $append;
        return $val % $this->module;
    }
    
    /**
     * Eval append lexem
     *
     * @param array $res
     * @param int   $append
     *
     * @return array
     */
    protected function addLex(array $res, int $add) : array
    {
        if (!is_numeric($add)) {
            throw new PartException(
                'To the right of "+" should be a number!',
                $this->partName
            );
        }
        foreach ($res as &$r) {
            $r = $this->addByModule($r, $add);
        }
        return $res;
    }

    /**
     * @param int $val
     * @param int $sub
     *
     * @return int
     */
    protected function subByModule(int $val, int $sub) : int
    {
        $val += $this->module;
        $val -= $sub;
        return $val % $this->module;
    }

    /**
     * Eval substract lexem
     *
     * @param array $res
     * @param int   $sub
     *
     * @return array
     */
    protected function subLex(array $res, int $sub) : array
    {
        if (!is_numeric($sub)) {
            throw new PartException(
                'To the right of "/" should be a number!',
                $this->partName
            );
        }
        if (empty($res)) {
            $res = [0];
        }
        foreach ($res as &$r) {
            $r = $this->subByModule($r, $sub);
        }
        return $res;
    }
}
