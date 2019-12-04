<?php
namespace Skrip42\Bundle\ChronBundle\Component\Pattern;

use Skrip42\Bundle\ChronBundle\Exception\Patern\PartException;

class YearPart extends AbstractPart
{
    protected $partName = 'year';

    public function __construct($pattern)
    {
        $this->range = range(2000, 2050);
        parent::__construct($pattern);
    }

    /**
     * @param array $res
     * @param int   $append
     *
     * @return array
     */
    protected function addLex(array $res, int $append) : array
    {
        if (!is_numeric($append)) {
            throw new PartException(
                'Спава от "+" должно быть число!',
                $this->partName
            );
        }
        foreach ($res as &$r) {
            $r += $append;
        }
        return $res;
    }

    /**
     * @param array $res
     * @param int   $sub
     *
     * @return array
     */
    protected function subLex(array $res, int $sub) : array
    {
        if (!is_numeric($sub)) {
            throw new PartException(
                'Спава от "-" должно быть число!',
                $this->partName
            );
        }
        if (empty($res)) {
            $res = [2000];
        }
        foreach ($res as &$r) {
            $r -= $sub;
        }
        return $res;
    }
}
