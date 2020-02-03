<?php

namespace App\Application\City\Exception;

class CheckCityCriteriaEmptyException extends \Exception
{
    public function __construct()
    {
        parent::__construct('There are not criteria to check the city');
    }
}