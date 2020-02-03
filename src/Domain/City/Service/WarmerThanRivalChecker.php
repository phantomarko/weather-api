<?php

namespace App\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;

class WarmerThanRivalChecker extends AbstractChecker
{
    public const ALIAS = 'rival';

    private $rival;

    public function __construct(?AbstractChecker $parentChecker, City $rival)
    {
        parent::__construct($parentChecker);
        $this->rival = $rival;
    }

    public function check(City $city): array
    {
        if (!$city->temperature()->unitEquals($this->rival->temperature()->unitValue())) {
            throw new TemperaturesUnitNotMatchException();
        }

        $cityCheck = $cityCheck = new CheckReport(
            self::ALIAS,
            $city->temperature()->degrees() > $this->rival->temperature()->degrees()
        );

        return empty($this->parentChecker())
            ? [$cityCheck]
            : array_merge($this->parentChecker()->check($city), [$cityCheck]);
    }
}