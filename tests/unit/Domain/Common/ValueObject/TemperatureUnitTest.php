<?php

namespace App\Tests\unit\Domain\Common\ValueObject;

use App\Domain\Common\Exception\InvalidTemperatureUnitException;
use App\Domain\Common\ValueObject\TemperatureUnit;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class TemperatureUnitTest extends TestCase
{
    private $prophet;
    private $unitValue;
    private $temperatureUnit;

    protected function setUp()
    {
        $this->prophet = New Prophet();
        $this->unitValue = TemperatureUnit::CELSIUS;

        $this->temperatureUnit = new TemperatureUnit($this->unitValue);
    }

    public function testNewInvalid()
    {
        $this->expectException(InvalidTemperatureUnitException::class);
        $temperatureUnit = new TemperatureUnit('invalid unit');
    }

    public function testValue()
    {
        $result = $this->temperatureUnit->value();
        $this->assertEquals($this->unitValue, $result);
    }

    public function testIsKelvinTrue()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::KELVIN);
        $result = $temperatureUnit->isKelvin();

        $this->assertTrue($result);
    }

    public function testIsKelvinFalse()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::CELSIUS);
        $result = $temperatureUnit->isKelvin();

        $this->assertFalse($result);
    }

    public function testIsCelsiusTrue()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::CELSIUS);
        $result = $temperatureUnit->isCelsius();

        $this->assertTrue($result);
    }

    public function testIsCelsiusFalse()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::KELVIN);
        $result = $temperatureUnit->isCelsius();

        $this->assertFalse($result);
    }

    public function testIsFahrenheitTrue()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::FAHRENHEIT);
        $result = $temperatureUnit->isFahrenheit();

        $this->assertTrue($result);
    }

    public function testIsFahrenheitFalse()
    {
        $temperatureUnit = new TemperatureUnit(TemperatureUnit::KELVIN);
        $result = $temperatureUnit->isFahrenheit();

        $this->assertFalse($result);
    }

    public function testEqualsTrue()
    {
        $result = $this->temperatureUnit->equals($this->unitValue);
        $this->assertTrue($result);
    }

    public function testEqualsFalse()
    {
        $result = $this->temperatureUnit->equals(TemperatureUnit::FAHRENHEIT);
        $this->assertFalse($result);
    }
}
