<?php

namespace App\Domain\Common\Exception;

class InvalidTemperatureUnitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Temperature Unit value');
    }
}
