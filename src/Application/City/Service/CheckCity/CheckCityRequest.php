<?php

namespace App\Application\City\Service\CheckCity;

use App\Application\City\Exception\InvalidCheckCityRequestArgumentException;

class CheckCityRequest
{
    private $cityName;

    public function __construct(?string $cityName)
    {
        $this->validate($cityName);
        $this->cityName = $cityName;
    }

    private function validate(?string $cityName): void
    {
        if (empty($cityName)) {
            throw new InvalidCheckCityRequestArgumentException();
        }
    }

    public function cityName(): string
    {
        return $this->cityName;
    }
}