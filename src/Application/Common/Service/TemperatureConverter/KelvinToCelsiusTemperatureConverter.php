<?php

namespace App\Application\Common\Service\TemperatureConverter;

use App\Domain\Common\Exception\InvalidTemperatureUnitException;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;

class KelvinToCelsiusTemperatureConverter implements TemperatureConverterInterface
{
    public function convert(Temperature $temperature): Temperature
    {
        $this->validate($temperature);
        return new Temperature(
            $temperature->degrees() - 273.15,
            new TemperatureUnit(TemperatureUnit::CELSIUS)
        );
    }

    private function validate(Temperature $temperature): void
    {
        if (!$temperature->isKelvin()) {
            throw new InvalidTemperatureUnitException();
        }
    }
}