<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Exception\InvalidTemperatureUnitException;

class TemperatureUnit
{
    public const CELSIUS = 'CELSIUS';
    public const KELVIN = 'KELVIN';
    public const FAHRENHEIT = 'FAHRENHEIT';

    private $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!in_array($value, $this->getValidValues())) {
            throw new InvalidTemperatureUnitException();
        }
    }

    private function getValidValues(): array
    {
        return [
            self::CELSIUS,
            self::KELVIN,
            self::FAHRENHEIT
        ];
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isKelvin(): bool
    {
        return $this->value === self::KELVIN;
    }

    public function isCelsius(): bool
    {
        return $this->value === self::CELSIUS;
    }

    public function isFahrenheit(): bool
    {
        return $this->value === self::FAHRENHEIT;
    }

    public function equals(string $value): bool
    {
        return $this->value === $value;
    }
}