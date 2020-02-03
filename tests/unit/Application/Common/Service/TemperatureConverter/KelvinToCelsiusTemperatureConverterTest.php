<?php

namespace App\Tests\unit\Domain\Common\Service;

use App\Application\Common\Service\TemperatureConverter\KelvinToCelsiusTemperatureConverter;
use App\Domain\Common\Exception\InvalidTemperatureUnitException;
use App\Domain\Common\ValueObject\Temperature;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class KelvinToCelsiusTemperatureConverterTest extends TestCase
{
    private $kelvinToCelsiusTemperatureConverter;
    private $prophet;

    protected function setUp()
    {
        $this->kelvinToCelsiusTemperatureConverter = new KelvinToCelsiusTemperatureConverter();
        $this->prophet = New Prophet();
    }

    public function testConvertWithValidTemperatureUnit()
    {
        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->isKelvin()->willReturn(true);
        $temperatureMock->degrees()->willReturn(273.15);

        $temperatureResult = $this->kelvinToCelsiusTemperatureConverter->convert($temperatureMock->reveal());

        $this->assertInstanceOf(Temperature::class, $temperatureResult);
        $celsiusDegrees = 0;
        $this->assertEquals($celsiusDegrees, $temperatureResult->degrees());
        $this->assertTrue($temperatureResult->isCelsius());
    }

    public function testConvertWithInvalidTemperatureUnit()
    {
        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->isKelvin()->willReturn(false);

        $this->expectException(InvalidTemperatureUnitException::class);
        $this->kelvinToCelsiusTemperatureConverter->convert($temperatureMock->reveal());
    }
}
