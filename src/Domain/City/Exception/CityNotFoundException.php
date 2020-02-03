<?php

namespace App\Domain\City\Exception;

class CityNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('City not found');
    }
}