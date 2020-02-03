<?php

namespace App\Domain\Common\Exception;

class TemperaturesUnitNotMatchException extends \Exception
{
    public function __construct()
    {
        parent::__construct('The temperatures unit does not match');
    }
}