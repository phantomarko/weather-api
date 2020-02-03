<?php

namespace App\Application\City\Exception;

class InvalidCheckCityRequestArgumentException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid argument of CheckCityRequest');
    }
}