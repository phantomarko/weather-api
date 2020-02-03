<?php

namespace App\Domain\City\Exception;

class InvalidCityArgumentException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid argument of City');
    }
}