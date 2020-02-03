<?php

namespace App\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;
use DateTime;

class TemperatureBetweenValuesChecker extends AbstractChecker
{
    public const ALIAS = 'daytemp';

    public function check(City $city): array
    {
        if (!$city->temperature()->unitEquals(TemperatureUnit::CELSIUS)) {
            throw new TemperaturesUnitNotMatchException();
        }

        $cityCheck = $this->getCheckReport($city);

        return empty($this->parentChecker())
            ? [$cityCheck]
            : array_merge($this->parentChecker()->check($city), [$cityCheck]);
    }

    private function getCheckReport(City $city): CheckReport
    {
        $minimum = $this->getMinimumTemperatureByCity($city);
        $maximum = $this->getMaximumTemperatureByCity($city);

        return new CheckReport(
            self::ALIAS,
            $city->temperature()->greaterThanOrEqual($minimum) && $city->temperature()->lessThanOrEqual($maximum)
        );
    }

    private function getMinimumTemperatureByCity(City $city): Temperature
    {
        $currentDateTime = DateTime::createFromFormat('U', time());
        $degrees = $city->isDayTime($currentDateTime)
            ? 17
            : 10;
        return new Temperature($degrees, new TemperatureUnit(TemperatureUnit::CELSIUS));
    }

    private function getMaximumTemperatureByCity(City $city): Temperature
    {
        $currentDateTime = DateTime::createFromFormat('U', time());
        $degrees = $city->isDayTime($currentDateTime)
            ? 25
            : 15;
        return new Temperature($degrees, new TemperatureUnit(TemperatureUnit::CELSIUS));
    }
}