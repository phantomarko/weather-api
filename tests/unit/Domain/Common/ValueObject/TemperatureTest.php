<?php

namespace App\Tests\unit\Domain\Common\ValueObject;

use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class TemperatureTest extends TestCase
{
    private $prophet;
    private $temperature;
    private $degrees;
    private $temperatureUnitMock;

    protected function setUp()
    {
        $this->prophet = New Prophet();
        $this->degrees = 20;
        $this->temperatureUnitMock = $this->prophet->prophesize(TemperatureUnit::class);

        $this->temperature = new Temperature($this->degrees, $this->temperatureUnitMock->reveal());
    }

    public function testDegrees()
    {
        $result = $this->temperature->degrees();
        $this->assertEquals($this->degrees, $result);
    }

    public function testUnitValue()
    {
        $unitValue = TemperatureUnit::CELSIUS;
        $this->temperatureUnitMock->value()->willReturn($unitValue);

        $result = $this->temperature->unitValue();
        $this->assertEquals($unitValue, $result);
    }

    public function testIsFahrenheitTrue()
    {
        $this->temperatureUnitMock->isFahrenheit()->willReturn(true);

        $result = $this->temperature->isFahrenheit();
        $this->assertTrue($result);
    }

    public function testIsFahrenheitFalse()
    {
        $this->temperatureUnitMock->isFahrenheit()->willReturn(false);

        $result = $this->temperature->isFahrenheit();
        $this->assertFalse($result);
    }

    public function testIsKelvinTrue()
    {
        $this->temperatureUnitMock->isKelvin()->willReturn(true);

        $result = $this->temperature->isKelvin();
        $this->assertTrue($result);
    }

    public function testIsKelvinFalse()
    {
        $this->temperatureUnitMock->isKelvin()->willReturn(false);

        $result = $this->temperature->isKelvin();
        $this->assertFalse($result);
    }

    public function testIsCelsiusTrue()
    {
        $this->temperatureUnitMock->isCelsius()->willReturn(true);

        $result = $this->temperature->isCelsius();
        $this->assertTrue($result);
    }

    public function testIsCelsiusFalse()
    {
        $this->temperatureUnitMock->isCelsius()->willReturn(false);

        $result = $this->temperature->isCelsius();
        $this->assertFalse($result);
    }

    public function testUnitEqualsTrue()
    {
        $unitArgument = TemperatureUnit::CELSIUS;
        $this->temperatureUnitMock->equals($unitArgument)->willReturn(true);

        $result = $this->temperature->unitEquals($unitArgument);
        $this->assertTrue($result);
    }

    public function testUnitEqualsFalse()
    {
        $unitArgument = TemperatureUnit::CELSIUS;
        $this->temperatureUnitMock->equals($unitArgument)->willReturn(false);

        $result = $this->temperature->unitEquals($unitArgument);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider greaterThanOrEqualDataProvider
     */
    public function testGreaterThanOrEqual($mockDegrees, $boolExpected)
    {
        $unitArgument = TemperatureUnit::CELSIUS;

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitValue()->willReturn($unitArgument);
        $temperatureMock->degrees()->willReturn($mockDegrees);

        $this->temperatureUnitMock->equals($unitArgument)->willReturn(true);

        $result = $this->temperature->greaterThanOrEqual($temperatureMock->reveal());
        $this->assertEquals($boolExpected, $result);
    }

    public function greaterThanOrEqualDataProvider()
    {
        return [
            'Test greater' => [10, true],
            'Test equals' => [20, true],
            'Test less' => [30, false]
        ];
    }

    public function testGreaterThanOrEqualWithDifferentTemperatureUnits()
    {
        $unitArgument = TemperatureUnit::CELSIUS;

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitValue()->willReturn($unitArgument);

        $this->temperatureUnitMock->equals($unitArgument)->willReturn(false);

        $this->expectException(TemperaturesUnitNotMatchException::class);
        $this->temperature->greaterThanOrEqual($temperatureMock->reveal());
    }

    /**
     * @dataProvider lessThanOrEqualDataProvider
     */
    public function testLessThanOrEqual($mockDegrees, $boolExpected)
    {
        $unitArgument = TemperatureUnit::CELSIUS;

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitValue()->willReturn($unitArgument);
        $temperatureMock->degrees()->willReturn($mockDegrees);

        $this->temperatureUnitMock->equals($unitArgument)->willReturn(true);

        $result = $this->temperature->lessThanOrEqual($temperatureMock->reveal());
        $this->assertEquals($boolExpected, $result);
    }

    public function lessThanOrEqualDataProvider()
    {
        return [
            'Test greater' => [30, true],
            'Test equals' => [20, true],
            'Test less' => [10, false]
        ];
    }

    public function testLessThanOrEqualWithDifferentTemperatureUnits()
    {
        $unitArgument = TemperatureUnit::CELSIUS;

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitValue()->willReturn($unitArgument);

        $this->temperatureUnitMock->equals($unitArgument)->willReturn(false);

        $this->expectException(TemperaturesUnitNotMatchException::class);
        $this->temperature->lessThanOrEqual($temperatureMock->reveal());
    }
}
