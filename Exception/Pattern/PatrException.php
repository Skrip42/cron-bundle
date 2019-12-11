<?php
namespace Skrip42\Bundle\CronBundle\Exception\Pattern;

use Exception;

class PartException extends Exception
{
    private $partName;
    public function __construct(string $message, string $partName, int $code = 0, Exception $previous = null)
    {
        $this->message = $message;
        $this->partName = $partName;
        parent::__construct($message, $code, $previous);
    }

    public function getPartName() : string
    {
        return $this->partName;
    }
}
