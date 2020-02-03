<?php

namespace App\Domain\City\Exception;

class CheckerAliasNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('City checker alias not found');
    }
}