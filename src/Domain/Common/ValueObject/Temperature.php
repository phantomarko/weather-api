<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;

class Temperature
{
    private $degrees;
    private $unit;

    public function __construct(float $degrees, TemperatureUnit $unit)
    {
        $this->degrees = $degrees;
        $this->unit = $unit;
    }

    public function degrees(): float
    {
        return $this->degrees;
    }

    public function unitValue(): string
    {
        return $this->unit->value();
    }

    public function isFahrenheit(): bool
    {
        return $this->unit->isFahrenheit();
    }

    public function isCelsius(): bool
    {
        return $this->unit->isCelsius();
    }

    public function isKelvin(): bool
    {
        return $this->unit->isKelvin();
    }

    public function unitEquals(string $unitValue): bool
    {
        return $this->unit->equals($unitValue);
    }

    public function greaterThanOrEqual(self $temperature): bool
    {
        if (!$this->unitEquals($temperature->unitValue())) {
            throw new TemperaturesUnitNotMatchException();
        }

        return $this->degrees >= $temperature->degrees();
    }

    public function lessThanOrEqual(self $temperature): bool
    {
        if (!$this->unitEquals($temperature->unitValue())) {
            throw new TemperaturesUnitNotMatchException();
        }

        return $this->degrees <= $temperature->degrees();
    }
}